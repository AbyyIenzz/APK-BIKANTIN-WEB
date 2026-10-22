<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function dashboard(Request $request): View
    {
        $cart = $request->session()->get('cart', []);
        $cartMenuIds = array_map('intval', array_keys($cart));
        $cartMenus = $cartMenuIds === []
            ? collect()
            : DB::table('menu')->whereIn('id', $cartMenuIds)->get()->keyBy('id');
        $availableCart = array_intersect_key($cart, $cartMenus->all());
        $cartNotice = null;

        if (count($availableCart) !== count($cart)) {
            $cart = $availableCart;
            $request->session()->put('cart', $cart);
            $cartNotice = 'Menu yang sudah tidak tersedia telah dikeluarkan dari keranjang.';
        }

        $orders = DB::table('pesanan')
            ->leftJoin('menu', 'menu.id', '=', 'pesanan.menu_id')
            ->where('pesanan.user_id', $request->user()->id)
            ->select([
                'pesanan.id',
                'pesanan.jumlah',
                'pesanan.total_harga',
                'pesanan.status',
                'menu.nama_menu',
            ])
            ->orderByDesc('pesanan.id')
            ->limit(10)
            ->get();

        return view('siswa.dashboard', [
            'menus' => DB::table('menu')->orderBy('kategori')->orderBy('nama_menu')->get(),
            'cart' => $cart,
            'cartMenus' => $cartMenus,
            'cartNotice' => $cartNotice,
            'orders' => $orders,
        ]);
    }

    public function addToCart(Request $request, int $menuId): RedirectResponse
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $menu = DB::table('menu')->where('id', $menuId)->first();

        if (! $menu) {
            abort(404);
        }

        $cart = $request->session()->get('cart', []);
        $cart[$menuId] = min(99, ($cart[$menuId] ?? 0) + $data['jumlah']);
        $request->session()->put('cart', $cart);

        return back()->with('success', $menu->nama_menu.' ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, int $menuId): RedirectResponse
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (! array_key_exists($menuId, $cart)) {
            abort(404);
        }

        $cart[$menuId] = $data['jumlah'];
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Jumlah item di keranjang diperbarui.');
    }

    public function removeFromCart(Request $request, int $menuId): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$menuId]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            return back()->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        $validator = Validator::make(['cart' => $cart], [
            'cart' => ['required', 'array', 'min:1'],
            'cart.*' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['cart' => 'Isi keranjang tidak valid. Hapus item lalu tambahkan kembali.']);
        }

        $menus = DB::table('menu')->whereIn('id', array_map('intval', array_keys($cart)))->get()->keyBy('id');

        if ($menus->count() !== count($cart)) {
            return back()->withErrors(['cart' => 'Sebagian menu sudah tidak tersedia. Perbarui keranjang sebelum checkout.']);
        }

        DB::transaction(function () use ($cart, $menus, $request): void {
            foreach ($cart as $menuId => $quantity) {
                $menu = $menus->get((int) $menuId);
                DB::table('pesanan')->insert([
                    'user_id' => $request->user()->id,
                    'menu_id' => $menu->id,
                    'jumlah' => $quantity,
                    'total_harga' => $menu->harga * $quantity,
                    'status' => 'Menunggu Konfirmasi',
                ]);
            }
        });

        $request->session()->forget('cart');

        return redirect()->route('siswa.dashboard')->with('success', 'Pesanan berhasil dibuat. Cek statusnya di riwayat pesanan.');
    }
}
