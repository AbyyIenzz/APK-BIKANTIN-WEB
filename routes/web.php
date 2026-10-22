<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
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
    ->get('/admin/dashboard', AdminDashboardController::class)
    ->name('admin.dashboard');

Route::middleware(['auth', 'role:siswa'])
    ->get('/siswa/dashboard', fn () => view('siswa.dashboard'))
    ->name('siswa.dashboard');
