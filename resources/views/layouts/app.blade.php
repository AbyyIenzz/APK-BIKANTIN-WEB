<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8f2">
    <title>@yield('title', 'Bi-Kantin')</title>
    <link rel="stylesheet" href="{{ asset('css/bi-kantin.css') }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="Bi-Kantin beranda">
            <span class="brand-mark" aria-hidden="true">B</span>
            <span>bi-kantin<span class="brand-period">.</span></span>
        </a>
        <div class="topbar-right">
            <span class="user-chip">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</span>
                <span class="user-info">
                    <strong>{{ auth()->user()->username }}</strong>
                    <small>{{ auth()->user()->role === 'admin' ? 'Pengelola kantin' : 'Siswa' }}</small>
                </span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="button button-quiet button-small" type="submit">Keluar <span aria-hidden="true">↗</span></button>
            </form>
        </div>
    </header>
    <main class="page-shell">
        @yield('content')
    </main>
    <footer class="site-footer">
        <span>Bi-Kantin</span>
        <span>Jajan enak, urusan jadi mudah.</span>
    </footer>
</body>
</html>
