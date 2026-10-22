@extends('layouts.app')

@section('title', 'Dashboard Siswa | Bi-Kantin')

@section('content')
    <section class="welcome-row">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> WAKTU ISTIRAHATMU</span>
            <h1>Hai, {{ auth()->user()->username }} <span class="wave">✦</span></h1>
            <p class="muted">Semoga harimu menyenangkan. Yuk, intip yang ada di kantin!</p>
        </div>
        <div class="date-pill"><span aria-hidden="true">◷</span> Hari ini · {{ now()->format('d/m/Y') }}</div>
    </section>

    <section class="student-hero">
        <div class="student-hero-copy">
            <span class="hero-label">JEDA SEBENTAR, MAKAN YANG ENAK</span>
            <h2>Menu kantin,<br>dekat <em>di hati.</em></h2>
            <p>Temukan makanan dan minuman favoritmu untuk menemani waktu istirahat.</p>
            <span class="coming-soon"><span aria-hidden="true">✦</span> Katalog menu segera hadir</span>
        </div>
        <div class="lunch-art" aria-hidden="true">
            <div class="lunch-sun"></div>
            <div class="lunch-cup"><span></span><i></i></div>
            <div class="lunch-plate"><i class="plate-rice"></i><i class="plate-green"></i><i class="plate-orange"></i></div>
            <span class="lunch-spark spark-left">✳</span><span class="lunch-spark spark-right">✦</span>
            <span class="lunch-label">made with care</span>
        </div>
    </section>

    <section class="section-heading">
        <div>
            <span class="card-kicker">PERJALANAN PESANAN</span>
            <h2>Semudah ini nantinya</h2>
        </div>
        <span class="section-note">Fitur pemesanan sedang disiapkan</span>
    </section>

    <section class="journey-grid">
        <article class="journey-card">
            <span class="journey-number">01</span>
            <span class="journey-icon journey-browse" aria-hidden="true">⌕</span>
            <h3>Pilih menu</h3>
            <p>Jelajahi pilihan makanan dan minuman favorit dari kantin.</p>
        </article>
        <article class="journey-card">
            <span class="journey-number">02</span>
            <span class="journey-icon journey-order" aria-hidden="true">＋</span>
            <h3>Buat pesanan</h3>
            <p>Tentukan jumlahnya, lalu kirim pesananmu dengan praktis.</p>
        </article>
        <article class="journey-card">
            <span class="journey-number">03</span>
            <span class="journey-icon journey-pickup" aria-hidden="true">✓</span>
            <h3>Pantau & ambil</h3>
            <p>Cek status pesanan dan ambil saat sudah siap.</p>
        </article>
    </section>

    <div class="student-footnote"><span aria-hidden="true">☀</span> Siapkan diri untuk pengalaman jajan yang lebih praktis.</div>
@endsection
