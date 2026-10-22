<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('username', 50);
            $table->string('password', 50);
            $table->enum('role', ['admin', 'siswa']);
        });
        Schema::create('menu', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_menu', 100);
            $table->integer('harga');
            $table->string('kategori', 50);
        });
        Schema::create('pesanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('menu_id');
            $table->integer('jumlah');
            $table->integer('total_harga');
            $table->string('status', 100)->nullable()->default('Menunggu Konfirmasi');
        });

        DB::table('users')->insert([
            ['username' => 'admin_kantin', 'password' => 'admin123', 'role' => 'admin'],
            ['username' => 'budi_siswa', 'password' => '123', 'role' => 'siswa'],
        ]);

        DB::table('menu')->insert([
            ['nama_menu' => 'Nasi Goreng', 'harga' => 15000, 'kategori' => 'Makanan'],
        ]);
    }

    public function test_student_login_redirects_to_student_dashboard(): void
    {
        $this->post(route('login.store'), [
            'username' => 'budi_siswa',
            'password' => '123',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('siswa.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->post(route('login.store'), [
            'username' => 'admin_kantin',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('siswa.dashboard'))->assertForbidden();
    }

    public function test_invalid_login_is_rejected(): void
    {
        $this->from(route('login'))
            ->post(route('login.store'), [
                'username' => 'budi_siswa',
                'password' => 'salah',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_guest_cannot_open_role_dashboards_and_can_log_out(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'username' => 'budi_siswa',
            'password' => '123',
        ]);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_can_create_update_and_delete_unused_menu(): void
    {
        $this->post(route('login.store'), [
            'username' => 'admin_kantin',
            'password' => 'admin123',
        ]);

        $this->post(route('admin.menus.store'), [
            'nama_menu' => 'Es Jeruk',
            'harga' => 5000,
            'kategori' => 'Minuman',
        ])->assertRedirect(route('admin.menus'));

        $menuId = (int) DB::table('menu')->where('nama_menu', 'Es Jeruk')->value('id');
        $this->assertDatabaseHas('menu', ['id' => $menuId, 'harga' => 5000]);

        $this->put(route('admin.menus.update', $menuId), [
            'nama_menu' => 'Es Jeruk Segar',
            'harga' => 6000,
            'kategori' => 'Minuman',
        ])->assertRedirect(route('admin.menus'));
        $this->assertDatabaseHas('menu', ['id' => $menuId, 'nama_menu' => 'Es Jeruk Segar', 'harga' => 6000]);

        $this->delete(route('admin.menus.destroy', $menuId))
            ->assertRedirect(route('admin.menus'));
        $this->assertDatabaseMissing('menu', ['id' => $menuId]);
    }

    public function test_menu_with_existing_orders_cannot_be_deleted(): void
    {
        DB::table('pesanan')->insert([
            'user_id' => 2,
            'menu_id' => 1,
            'jumlah' => 1,
            'total_harga' => 15000,
            'status' => 'Menunggu Konfirmasi',
        ]);

        $this->post(route('login.store'), [
            'username' => 'admin_kantin',
            'password' => 'admin123',
        ]);

        $this->from(route('admin.menus'))
            ->delete(route('admin.menus.destroy', 1))
            ->assertRedirect(route('admin.menus'))
            ->assertSessionHasErrors('menu');

        $this->assertDatabaseHas('menu', ['id' => 1]);
    }

    public function test_admin_can_update_order_status_but_student_cannot(): void
    {
        DB::table('pesanan')->insert([
            'user_id' => 2,
            'menu_id' => 1,
            'jumlah' => 1,
            'total_harga' => 15000,
            'status' => 'Menunggu Konfirmasi',
        ]);
        $orderId = (int) DB::table('pesanan')->value('id');

        $this->post(route('login.store'), [
            'username' => 'budi_siswa',
            'password' => '123',
        ]);

        $this->patch(route('admin.orders.status', $orderId), [
            'status' => 'Siap Diambil',
        ])->assertForbidden();

        $this->post(route('logout'));
        $this->post(route('login.store'), [
            'username' => 'admin_kantin',
            'password' => 'admin123',
        ]);

        $this->patch(route('admin.orders.status', $orderId), [
            'status' => 'Sedang Dimasak',
        ])->assertSessionHas('success');
        $this->assertDatabaseHas('pesanan', ['id' => $orderId, 'status' => 'Sedang Dimasak']);
    }

    public function test_student_can_add_to_cart_and_checkout_current_menu_price(): void
    {
        $this->post(route('login.store'), [
            'username' => 'budi_siswa',
            'password' => '123',
        ]);

        $this->post(route('siswa.cart.add', 1), ['jumlah' => 2])
            ->assertSessionHas('cart', [1 => 2]);
        $this->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Nasi Goreng');

        $this->post(route('siswa.checkout'))
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pesanan', [
            'user_id' => 2,
            'menu_id' => 1,
            'jumlah' => 2,
            'total_harga' => 30000,
            'status' => 'Menunggu Konfirmasi',
        ]);
        $this->assertFalse(session()->has('cart'));
    }

    public function test_student_cannot_access_admin_menu_management(): void
    {
        $this->post(route('login.store'), [
            'username' => 'budi_siswa',
            'password' => '123',
        ]);

        $this->get(route('admin.menus'))->assertForbidden();
        $this->post(route('admin.menus.store'), [
            'nama_menu' => 'Terlarang',
            'harga' => 1000,
            'kategori' => 'Makanan',
        ])->assertForbidden();
    }
}
