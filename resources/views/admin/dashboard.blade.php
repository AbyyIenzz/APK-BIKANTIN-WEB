@extends('layouts.app')

@section('title', 'Dashboard Admin | Bi-Kantin')

@section('content')
    <section class="welcome-row">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> RUANG PENGELOLA</span>
            <h1>Halo, {{ auth()->user()->username }} <span class="wave">✦</span></h1>
            <p class="muted">Semua hal baik di kantin dimulai dari sini.</p>
        </div>
        <div class="date-pill"><span aria-hidden="true">◷</span> Hari ini · {{ now()->format('d/m/Y') }}</div>
    </section>

    <section class="admin-hero">
        <div class="hero-copy">
            <span class="hero-label">BI-KANTIN · PANEL ADMIN</span>
            <h2>Ruang kecil untuk<br>hal-hal yang <em>lezat.</em></h2>
            <p>Kelola menu dan pesanan kantin dengan mudah. Panel ini siap jadi pusat aktivitas kantinmu.</p>
            <span class="hero-status"><i></i> Akun admin aktif</span>
        </div>
        <div class="hero-illustration" aria-hidden="true">
            <div class="sun-shape"></div>
            <div class="steam steam-one"></div><div class="steam steam-two"></div><div class="steam steam-three"></div>
            <div class="bowl"><span class="bowl-rice"></span><i class="bowl-leaf"></i><i class="bowl-garnish"></i></div>
            <span class="floating-star star-a">✦</span><span class="floating-star star-b">✳</span>
        </div>
    </section>

    <section class="section-heading">
        <div>
            <span class="card-kicker">PUSAT AKTIVITAS</span>
            <h2>Yang bisa kamu kelola</h2>
        </div>
        <span class="section-note">Fitur berikutnya dalam pengembangan</span>
    </section>

    <section class="feature-grid">
        <article class="feature-card">
            <span class="feature-icon icon-menu" aria-hidden="true">✳</span>
            <span class="feature-tag">MENU KANTIN</span>
            <h3>Atur menu favorit</h3>
            <p>Tambah menu baru, perbarui harga, dan jaga katalog kantin tetap menarik.</p>
            <span class="feature-link">Pengelolaan menu <span aria-hidden="true">↗</span></span>
        </article>
        <article class="feature-card">
            <span class="feature-icon icon-orders" aria-hidden="true">▤</span>
            <span class="feature-tag">PESANAN</span>
            <h3>Pantau pesanan</h3>
            <p>Lihat pesanan siswa dan perbarui statusnya sampai siap diambil.</p>
            <span class="feature-link">Manajemen pesanan <span aria-hidden="true">↗</span></span>
        </article>
        <article class="feature-card feature-card-note">
            <span class="note-mark" aria-hidden="true">✦</span>
            <span class="feature-tag">LANGKAH SELANJUTNYA</span>
            <h3>Fondasi sudah siap.</h3>
            <p>Login dan akses admin sudah berjalan. CRUD menu dan pengelolaan pesanan menjadi tahap pengerjaan berikutnya.</p>
        </article>
    </section>
@endsection
