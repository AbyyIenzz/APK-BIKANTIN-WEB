<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')
    ->get('/dashboard', fn () => match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => abort(403),
    })
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])
    ->prefix('/admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/menu', [AdminController::class, 'menus'])->name('menus');
        Route::post('/menu', [AdminController::class, 'storeMenu'])->name('menus.store');
        Route::put('/menu/{menuId}', [AdminController::class, 'updateMenu'])->whereNumber('menuId')->name('menus.update');
        Route::delete('/menu/{menuId}', [AdminController::class, 'destroyMenu'])->whereNumber('menuId')->name('menus.destroy');
        Route::patch('/pesanan/{orderId}/status', [AdminController::class, 'updateOrderStatus'])->whereNumber('orderId')->name('orders.status');
    });

Route::middleware(['auth', 'role:siswa'])
    ->prefix('/siswa')
    ->name('siswa.')
    ->group(function (): void {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::post('/keranjang/{menuId}', [SiswaController::class, 'addToCart'])->whereNumber('menuId')->name('cart.add');
        Route::put('/keranjang/{menuId}', [SiswaController::class, 'updateCart'])->whereNumber('menuId')->name('cart.update');
        Route::delete('/keranjang/{menuId}', [SiswaController::class, 'removeFromCart'])->whereNumber('menuId')->name('cart.remove');
        Route::post('/checkout', [SiswaController::class, 'checkout'])->name('checkout');
    });
