@extends('layouts.app')

@section('title', 'Katalog Kantin | Bi-Kantin')

@section('content')
    <section class="welcome-row">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> WAKTU ISTIRAHATMU</span>
            <h1>Hai, {{ auth()->user()->username }} <span class="wave">✦</span></h1>
            <p class="muted">Pilih yang kamu suka, nanti tinggal ambil saat siap.</p>
        </div>
        <a class="date-pill cart-shortcut" href="#keranjang"><span aria-hidden="true">🛍</span> Keranjang <strong>{{ array_sum($cart) }}</strong></a>
    </section>

    @if (session('success'))
        <div class="notice notice-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice notice-error" role="alert">{{ $errors->first() }}</div>
    @endif
    @if ($cartNotice)
        <div class="notice notice-success" role="status">{{ $cartNotice }}</div>
    @endif

    <section class="student-hero catalog-hero">
        <div class="student-hero-copy">
            <span class="hero-label">JEDA SEBENTAR, MAKAN YANG ENAK</span>
            <h2>Menu kantin,<br>dekat <em>di hati.</em></h2>
            <p>Temukan makanan dan minuman favoritmu untuk menemani waktu istirahat.</p>
            <a class="button button-primary catalog-cta" href="#katalog">Lihat menu <span aria-hidden="true">↓</span></a>
        </div>
        <div class="lunch-art" aria-hidden="true">
            <div class="lunch-sun"></div>
            <div class="lunch-cup"><span></span><i></i></div>
            <div class="lunch-plate"><i class="plate-rice"></i><i class="plate-green"></i><i class="plate-orange"></i></div>
            <span class="lunch-spark spark-left">✳</span><span class="lunch-spark spark-right">✦</span>
            <span class="lunch-label">made with care</span>
        </div>
    </section>

    <div class="student-shop-layout">
        <section class="catalog-section" id="katalog">
            <div class="section-heading catalog-heading">
                <div>
                    <span class="card-kicker">DIBUAT DENGAN SEPENUH HATI</span>
                    <h2>Menu hari ini</h2>
                </div>
                <span class="section-note">{{ $menus->count() }} pilihan tersedia</span>
            </div>

            @if ($menus->isNotEmpty())
                <div class="catalog-grid">
                    @foreach ($menus as $menu)
                        @php($quantityInCart = (int) ($cart[$menu->id] ?? 0))
                        <article class="catalog-card">
                            <div class="catalog-art {{ strtolower($menu->kategori) === 'minuman' ? 'catalog-art-drink' : 'catalog-art-food' }}">
                                <span class="catalog-category">{{ $menu->kategori }}</span>
                                <span class="catalog-emoji" aria-hidden="true">{{ strtolower($menu->kategori) === 'minuman' ? '🥤' : '🍛' }}</span>
                                @if ($quantityInCart > 0)
                                    <span class="in-cart-badge">{{ $quantityInCart }} di keranjang</span>
                                @endif
                            </div>
                            <div class="catalog-card-body">
                                <h3>{{ $menu->nama_menu }}</h3>
                                <div class="catalog-card-bottom">
                                    <strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong>
                                    <form method="POST" action="{{ route('siswa.cart.add', $menu->id) }}">
                                        @csrf
                                        <input type="hidden" name="jumlah" value="1">
                                        <button class="add-cart-button" type="submit" aria-label="Tambah {{ $menu->nama_menu }} ke keranjang">+</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-panel catalog-empty">
                    <div class="panel-empty">
                        <span aria-hidden="true">☕</span>
                        <strong>Menu sedang disiapkan</strong>
                        <p>Belum ada menu yang tersedia. Coba cek lagi nanti, ya!</p>
                    </div>
                </div>
            @endif
        </section>

        <aside class="student-side">
            <section class="admin-panel cart-panel" id="keranjang">
                <div class="panel-heading">
                    <div>
                        <span class="card-kicker">SIAP DINIKMATI</span>
                        <h2>Keranjangmu</h2>
                    </div>
                    <span class="cart-count">{{ array_sum($cart) }}</span>
                </div>

                @php($cartTotal = 0)
                @if (count($cart) > 0 && $cartMenus->isNotEmpty())
                    <div class="cart-list">
                        @foreach ($cart as $menuId => $quantity)
                            @if ($cartMenus->has((int) $menuId))
                                @php($menu = $cartMenus->get((int) $menuId))
                                @php($cartTotal += $menu->harga * $quantity)
                                <div class="cart-item">
                                    <span class="cart-item-icon" aria-hidden="true">{{ strtolower($menu->kategori) === 'minuman' ? '🥤' : '🍽️' }}</span>
                                    <div class="cart-item-content">
                                        <strong>{{ $menu->nama_menu }}</strong>
                                        <small>Rp {{ number_format($menu->harga * $quantity, 0, ',', '.') }}</small>
                                        <div class="quantity-actions">
                                            <form method="POST" action="{{ route('siswa.cart.update', $menuId) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="jumlah" value="{{ max(1, $quantity - 1) }}">
                                                <button type="submit" aria-label="Kurangi {{ $menu->nama_menu }}">−</button>
                                            </form>
                                            <span>{{ $quantity }}</span>
                                            <form method="POST" action="{{ route('siswa.cart.update', $menuId) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="jumlah" value="{{ min(99, $quantity + 1) }}">
                                                <button type="submit" aria-label="Tambah {{ $menu->nama_menu }}">+</button>
                                            </form>
                                            <form class="remove-cart-form" method="POST" action="{{ route('siswa.cart.remove', $menuId) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="cart-total"><span>Total</span><strong>Rp {{ number_format($cartTotal, 0, ',', '.') }}</strong></div>
                    <form method="POST" action="{{ route('siswa.checkout') }}">
                        @csrf
                        <button class="button button-primary button-full checkout-button" type="submit">Buat pesanan <span aria-hidden="true">→</span></button>
                    </form>
                    <p class="cart-note">Pesanan akan masuk dengan status menunggu konfirmasi.</p>
                @else
                    <div class="cart-empty">
                        <span aria-hidden="true">🛍</span>
                        <strong>Keranjang masih kosong</strong>
                        <p>Tambah menu favoritmu untuk mulai memesan.</p>
                    </div>
                @endif
            </section>

            <section class="admin-panel history-panel">
                <div class="panel-heading">
                    <div>
                        <span class="card-kicker">PESANAN KAMU</span>
                        <h2>Riwayat terbaru</h2>
                    </div>
                </div>
                @forelse ($orders as $order)
                    @php($status = $order->status ?: 'Menunggu Konfirmasi')
                    <div class="history-row">
                        <span class="history-mark" aria-hidden="true">✓</span>
                        <span class="history-info">
                            <strong>{{ $order->nama_menu ?? 'Menu kantin' }}</strong>
                            <small>{{ $order->jumlah }} item · Rp {{ number_format($order->total_harga, 0, ',', '.') }}</small>
                        </span>
                        <span class="history-status">{{ $status }}</span>
                    </div>
                @empty
                    <div class="history-empty">Pesanan yang sudah dibuat akan tampil di sini.</div>
                @endforelse
            </section>
        </aside>
    </div>
@endsection
