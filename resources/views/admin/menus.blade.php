@extends('layouts.app')

@section('title', 'Kelola Menu | Bi-Kantin')

@section('content')
    <section class="admin-heading">
        <div>
            <span class="eyebrow"><span class="eyebrow-dot"></span> PENGELOLAAN MENU</span>
            <h1>Menu kantin <span class="wave">✳</span></h1>
            <p class="muted">Atur pilihan makanan dan minuman yang tersedia untuk siswa.</p>
        </div>
        <a class="button button-primary" href="{{ route('admin.dashboard') }}">← Kembali ke dashboard</a>
    </section>

    @if (session('success'))
        <div class="notice notice-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice notice-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="manage-layout">
        <section class="admin-panel menu-form-panel">
            <div class="panel-heading">
                <div>
                    <span class="card-kicker">MENU BARU</span>
                    <h2>Tambah pilihan</h2>
                </div>
                <span class="form-mark" aria-hidden="true">＋</span>
            </div>
            <form method="POST" action="{{ route('admin.menus.store') }}" class="manage-form">
                @csrf
                <label for="nama_menu">Nama menu</label>
                <input id="nama_menu" name="nama_menu" value="{{ old('nama_menu') }}" maxlength="100" placeholder="Contoh: Nasi Goreng Spesial" required>

                <label for="harga">Harga (Rp)</label>
                <input id="harga" name="harga" type="number" min="1" max="20000000" value="{{ old('harga') }}" placeholder="Contoh: 15000" required>

                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="" disabled @selected(! old('kategori'))>Pilih kategori</option>
                    <option value="Makanan" @selected(old('kategori') === 'Makanan')>Makanan</option>
                    <option value="Minuman" @selected(old('kategori') === 'Minuman')>Minuman</option>
                </select>

                <button class="button button-primary button-full" type="submit">Tambah menu <span aria-hidden="true">→</span></button>
            </form>
            <p class="form-footnote">Menu tersimpan langsung di database kantin.</p>
        </section>

        <section class="admin-panel manage-list-panel">
            <div class="panel-heading">
                <div>
                    <span class="card-kicker">KATALOG KANTIN</span>
                    <h2>Semua menu</h2>
                </div>
                <span class="panel-count">{{ $menus->count() }} menu</span>
            </div>

            @if ($menus->isNotEmpty())
                <div class="manage-menu-list">
                    @foreach ($menus as $menu)
                        <article class="manage-menu-card">
                            <div class="manage-menu-top">
                                <span class="menu-thumb {{ strtolower($menu->kategori) === 'minuman' ? 'thumb-drink' : 'thumb-food' }}" aria-hidden="true">
                                    {{ strtolower($menu->kategori) === 'minuman' ? '🥤' : '🍽️' }}
                                </span>
                                <span class="menu-info">
                                    <strong>{{ $menu->nama_menu }}</strong>
                                    <small>{{ $menu->kategori }} · Rp {{ number_format($menu->harga, 0, ',', '.') }}</small>
                                </span>
                                <span class="menu-id">#{{ $menu->id }}</span>
                            </div>
                            <details class="edit-menu">
                                <summary>Edit menu</summary>
                                <form method="POST" action="{{ route('admin.menus.update', $menu->id) }}" class="manage-form edit-form">
                                    @csrf
                                    @method('PUT')
                                    <label for="nama_menu_{{ $menu->id }}">Nama menu</label>
                                    <input id="nama_menu_{{ $menu->id }}" name="nama_menu" value="{{ $menu->nama_menu }}" maxlength="100" required>

                                    <label for="harga_{{ $menu->id }}">Harga (Rp)</label>
                                    <input id="harga_{{ $menu->id }}" name="harga" type="number" min="1" max="20000000" value="{{ $menu->harga }}" required>

                                    <label for="kategori_{{ $menu->id }}">Kategori</label>
                                    <select id="kategori_{{ $menu->id }}" name="kategori" required>
                                        <option value="Makanan" @selected($menu->kategori === 'Makanan')>Makanan</option>
                                        <option value="Minuman" @selected($menu->kategori === 'Minuman')>Minuman</option>
                                    </select>
                                    <button class="button button-primary button-small" type="submit">Simpan perubahan</button>
                                </form>
                            </details>
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" onsubmit="return confirm(@js('Hapus menu '.$menu->nama_menu.'?'))">
                                @csrf
                                @method('DELETE')
                                <button class="text-danger" type="submit">Hapus menu</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="panel-empty">
                    <span aria-hidden="true">✳</span>
                    <strong>Belum ada menu</strong>
                    <p>Tambahkan pilihan pertama melalui formulir di sebelah kiri.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
