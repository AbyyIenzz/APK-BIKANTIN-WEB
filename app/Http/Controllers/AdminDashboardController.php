<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $orders = DB::table('pesanan')
            ->leftJoin('users', 'users.id', '=', 'pesanan.user_id')
            ->leftJoin('menu', 'menu.id', '=', 'pesanan.menu_id')
            ->select([
                'pesanan.id',
                'pesanan.jumlah',
                'pesanan.total_harga',
                'pesanan.status',
                'users.username',
                'menu.nama_menu',
            ])
            ->orderByDesc('pesanan.id')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'menuCount' => DB::table('menu')->count(),
            'orderCount' => DB::table('pesanan')->count(),
            'pendingOrderCount' => DB::table('pesanan')
                ->where('status', 'Menunggu Konfirmasi')
                ->count(),
            'orderValue' => DB::table('pesanan')->sum('total_harga'),
            'menus' => DB::table('menu')
                ->select(['id', 'nama_menu', 'harga', 'kategori'])
                ->orderBy('nama_menu')
                ->limit(5)
                ->get(),
            'orders' => $orders,
        ]);
    }
}
