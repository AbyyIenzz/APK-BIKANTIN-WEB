<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8f2">
    <title>Masuk | Bi-Kantin</title>
    <link rel="stylesheet" href="{{ asset('css/bi-kantin.css') }}">
</head>
<body class="login-page">
    <a class="login-brand brand" href="{{ route('login') }}">
        <span class="brand-mark" aria-hidden="true">B</span>
        <span>bi-kantin<span class="brand-period">.</span></span>
    </a>

    <main class="login-shell">
        <section class="login-story">
            <span class="eyebrow"><span class="eyebrow-dot"></span> KANTIN SEKOLAH, CARA BARU</span>
            <h1>Waktunya istirahat.<br><span>Waktunya makan enak.</span></h1>
            <p>Temukan menu favoritmu dan nikmati waktu istirahat dengan lebih santai.</p>
            <div class="food-art" aria-hidden="true">
                <span class="food-spark spark-one">✳</span>
                <span class="food-spark spark-two">✦</span>
                <div class="plate">
                    <div class="plate-food">
                        <span class="food-leaf leaf-one"></span>
                        <span class="food-leaf leaf-two"></span>
                        <span class="food-egg"><i></i></span>
                        <span class="food-tomato"></span>
                    </div>
                </div>
                <div class="art-note"><span>🍽️</span> Istirahat jadi lebih seru</div>
            </div>
            <div class="story-bottom"><span>SEGAR</span><i></i><span>PRAKTIS</span><i></i><span>LEBIH ASYIK</span></div>
        </section>

        <section class="login-card">
            <span class="card-kicker">SELAMAT DATANG KEMBALI</span>
            <h2>Masuk ke akunmu</h2>
            <p class="muted">Masukkan username dan password untuk melanjutkan.</p>

            @if ($errors->any())
                <div class="alert-error" role="alert">
                    <span aria-hidden="true">!</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="login-form">
                @csrf
                <label for="username">Username</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">◎</span>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" maxlength="50" placeholder="contoh: budi_siswa" autocomplete="username" required autofocus>
                </div>

                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">⌑</span>
                    <input id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required>
                </div>

                <button class="button button-primary button-full" type="submit">Masuk sekarang <span aria-hidden="true">→</span></button>
            </form>
            <div class="login-hint"><span aria-hidden="true">✦</span> Gunakan akun yang sudah terdaftar di database kantin.</div>
        </section>
    </main>
    <footer class="login-footer"><span>© {{ date('Y') }} Bi-Kantin</span><span>Dibuat untuk waktu istirahat yang lebih baik.</span></footer>
</body>
</html>
