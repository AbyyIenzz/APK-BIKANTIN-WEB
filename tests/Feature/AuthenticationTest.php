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
}
