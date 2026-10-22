<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const ORDER_STATUSES = [
        'Menunggu Konfirmasi',
        'Sedang Dimasak',
        'Siap Diambil',
        'Selesai',
    ];

    public function menus(): View
    {
        return view('admin.menus', [
            'menus' => DB::table('menu')->orderByDesc('id')->get(),
        ]);
    }

    public function storeMenu(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_menu' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:1', 'max:20000000'],
            'kategori' => ['required', 'string', 'max:50'],
        ]);

        DB::table('menu')->insert($data);

        return redirect()->route('admin.menus')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function updateMenu(Request $request, int $menuId): RedirectResponse
    {
        $data = $request->validate([
            'nama_menu' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:1', 'max:20000000'],
            'kategori' => ['required', 'string', 'max:50'],
        ]);

        $updated = DB::table('menu')->where('id', $menuId)->update($data);

        if ($updated === 0 && ! DB::table('menu')->where('id', $menuId)->exists()) {
            abort(404);
        }

        return redirect()->route('admin.menus')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroyMenu(int $menuId): RedirectResponse
    {
        $menuExists = DB::table('menu')->where('id', $menuId)->exists();

        if (! $menuExists) {
            abort(404);
        }

        if (DB::table('pesanan')->where('menu_id', $menuId)->exists()) {
            return back()->withErrors([
                'menu' => 'Menu tidak dapat dihapus karena sudah tercatat dalam pesanan.',
            ]);
        }

        DB::table('menu')->where('id', $menuId)->delete();

        return redirect()->route('admin.menus')->with('success', 'Menu berhasil dihapus.');
    }

    public function updateOrderStatus(Request $request, int $orderId): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::ORDER_STATUSES)],
        ]);

        $updated = DB::table('pesanan')->where('id', $orderId)->update($data);

        if ($updated === 0 && ! DB::table('pesanan')->where('id', $orderId)->exists()) {
            abort(404);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
