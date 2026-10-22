@extends('layouts.app')

@section('title', 'Dashboard Admin | Bi-Kantin')

@section('content')
    <section class="admin-heading">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> RUANG PENGELOLA</span>
            <h1>Halo, {{ auth()->user()->username }} <span class="wave">✦</span></h1>
            <p class="muted">Ini ringkasan aktivitas kantinmu hari ini.</p>
        </div>
        <span class="admin-date"><span aria-hidden="true">◷</span> {{ now()->format('d/m/Y') }}</span>
    </section>
    @if (session('success'))
        <div class="notice notice-success" role="status">{{ session('success') }}</div>
    @endif

    <section class="admin-banner">
        <div class="banner-copy">
            <span class="banner-kicker"><i></i> BI-KANTIN · ADMIN PANEL</span>
            <h2>Hal baik sedang<br>disajikan hari ini.</h2>
            <p>Kelola menu, pantau pesanan, dan bikin waktu istirahat makin menyenangkan.</p>
        </div>
        <div class="banner-art" aria-hidden="true">
            <span class="banner-orbit orbit-one"></span>
            <span class="banner-orbit orbit-two"></span>
            <span class="banner-spark spark-top">✦</span>
            <span class="banner-spark spark-bottom">✳</span>
            <div class="banner-plate"><span class="banner-rice"></span><i class="banner-greens"></i><i class="banner-egg"><b></b></i><i class="banner-tomato"></i></div>
            <div class="banner-cup"><i></i><b></b></div>
            <span class="banner-caption">fresh from the kantin</span>
        </div>
        <span class="banner-decoration" aria-hidden="true">BI-KANTIN</span>
    </section>

    <section class="admin-stats" aria-label="Ringkasan kantin">
        <article class="admin-stat">
            <span class="stat-icon stat-green" aria-hidden="true">✳</span>
            <span class="stat-label">TOTAL MENU</span>
            <strong>{{ number_format($menuCount) }}</strong>
            <span class="stat-foot">Pilihan tersedia di kantin</span>
        </article>
        <article class="admin-stat">
            <span class="stat-icon stat-orange" aria-hidden="true">▤</span>
            <span class="stat-label">TOTAL PESANAN</span>
            <strong>{{ number_format($orderCount) }}</strong>
            <span class="stat-foot">Pesanan tercatat</span>
        </article>
        <article class="admin-stat">
            <span class="stat-icon stat-yellow" aria-hidden="true">◷</span>
            <span class="stat-label">PERLU KONFIRMASI</span>
            <strong>{{ number_format($pendingOrderCount) }}</strong>
            <span class="stat-foot">Menunggu konfirmasi</span>
        </article>
        <article class="admin-stat">
            <span class="stat-icon stat-lilac" aria-hidden="true">Rp</span>
            <span class="stat-label">NILAI PESANAN</span>
            <strong class="stat-currency">Rp {{ number_format($orderValue, 0, ',', '.') }}</strong>
            <span class="stat-foot">Akumulasi seluruh pesanan</span>
        </article>
    </section>

    <section class="admin-content-grid">
        <article class="admin-panel menu-panel">
            <div class="panel-heading">
                <div>
                    <span class="card-kicker">YANG TERSEDIA</span>
                    <h2>Menu kantin</h2>
                </div>
                <div class="panel-actions">
                    <span class="panel-count">{{ $menuCount }} menu</span>
                    <a class="manage-link" href="{{ route('admin.menus') }}">Kelola ↗</a>
                </div>
            </div>
            @if ($menus->isNotEmpty())
                <div class="menu-list">
                    @foreach ($menus as $menu)
                        <div class="menu-row">
                            <span class="menu-thumb {{ strtolower($menu->kategori) === 'minuman' ? 'thumb-drink' : 'thumb-food' }}" aria-hidden="true">
                                {{ strtolower($menu->kategori) === 'minuman' ? '🥤' : '🍽️' }}
                            </span>
                            <span class="menu-info">
                                <strong>{{ $menu->nama_menu }}</strong>
                                <small>{{ $menu->kategori }}</small>
                            </span>
                            <strong class="menu-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>
                <p class="panel-footnote">Menampilkan hingga 5 menu dari database.</p>
            @else
                <div class="panel-empty">
                    <span aria-hidden="true">✳</span>
                    <strong>Belum ada menu</strong>
                    <p>Menu kantin yang ditambahkan akan muncul di sini.</p>
                </div>
            @endif
        </article>

        <article class="admin-panel order-panel">
            <div class="panel-heading">
                <div>
                    <span class="card-kicker">AKTIVITAS TERBARU</span>
                    <h2>Pesanan masuk</h2>
                </div>
                <span class="live-indicator"><i></i> TERBARU</span>
            </div>
            @if ($orders->isNotEmpty())
                <div class="order-list">
                    @foreach ($orders as $order)
                        @php($status = $order->status ?: 'Menunggu Konfirmasi')
                        <div class="order-row">
                            <span class="order-avatar">{{ strtoupper(substr($order->username ?? 'S', 0, 1)) }}</span>
                            <span class="order-info">
                                <strong>{{ $order->username ?? 'Siswa' }}</strong>
                                <small>{{ $order->jumlah }} × {{ $order->nama_menu ?? 'Menu dihapus' }}</small>
                            </span>
                            <span class="order-meta">
                                <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                                <small class="status-chip {{ str_contains(strtolower($status), 'menunggu') ? 'status-waiting' : (str_contains(strtolower($status), 'selesai') ? 'status-done' : 'status-progress') }}">{{ $status }}</small>
                            </span>
                        </div>
                        <form class="status-update-form" method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <label class="sr-only" for="status_{{ $order->id }}">Status pesanan #{{ $order->id }}</label>
                            <select id="status_{{ $order->id }}" name="status">
                                @foreach (['Menunggu Konfirmasi', 'Sedang Dimasak', 'Siap Diambil', 'Selesai'] as $orderStatus)
                                    <option value="{{ $orderStatus }}" @selected($status === $orderStatus)>{{ $orderStatus }}</option>
                                @endforeach
                            </select>
                            <button type="submit">Perbarui</button>
                        </form>
                    @endforeach
                </div>
            @else
                <div class="panel-empty order-empty">
                    <span aria-hidden="true">☕</span>
                    <strong>Belum ada pesanan</strong>
                    <p>Pesanan siswa akan muncul di sini setelah dibuat.</p>
                </div>
            @endif
            <div class="order-panel-footer"><span class="footer-pulse"></span> Status diambil dari database kantin</div>
        </article>
    </section>

    <section class="admin-tip">
        <span class="tip-icon" aria-hidden="true">✦</span>
        <span><strong>Dashboard siap menemani aktivitasmu.</strong> Data menu dan pesanan ditampilkan langsung dari database Bi-Kantin.</span>
    </section>
@endsection
