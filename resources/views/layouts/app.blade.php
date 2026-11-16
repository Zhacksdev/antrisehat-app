<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AntriSehat') · {{ config('app.name') }}</title>
    @stack('head')
    <style>
        :root { --hijau:#0f766e; --hijau-muda:#ccfbf1; --abu:#f1f5f9; --teks:#0f172a; --merah:#b91c1c; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; color:var(--teks); background:var(--abu); }
        header { background:var(--hijau); color:#fff; }
        header .wrap { display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between; }
        header a { color:#fff; text-decoration:none; margin-right:1rem; font-weight:500; }
        header strong { font-size:1.25rem; }
        .wrap { max-width:1080px; margin:0 auto; padding:1rem; }
        .card { background:#fff; border-radius:10px; padding:1.25rem; margin-bottom:1rem; box-shadow:0 1px 3px rgba(0,0,0,.08); }
        .grid { display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); }
        h1,h2 { margin-top:0; }
        label { display:block; font-size:.875rem; margin:.5rem 0 .25rem; font-weight:500; }
        input,select { width:100%; padding:.5rem; border:1px solid #cbd5e1; border-radius:6px; font:inherit; }
        button,.btn { display:inline-block; margin-top:.75rem; padding:.5rem 1rem; border:0; border-radius:6px; background:var(--hijau); color:#fff; font:inherit; cursor:pointer; text-decoration:none; }
        .btn-sekunder { background:#475569; }
        .btn-bahaya { background:var(--merah); }
        table { width:100%; border-collapse:collapse; }
        th,td { text-align:left; padding:.5rem; border-bottom:1px solid #e2e8f0; font-size:.925rem; }
        .alert { padding:.75rem 1rem; border-radius:6px; margin-bottom:1rem; }
        .alert-ok { background:var(--hijau-muda); }
        .alert-err { background:#fee2e2; color:var(--merah); }
        .inline { display:inline; }
        .muted { color:#64748b; font-size:.875rem; }
        .nomor { font-size:3rem; font-weight:700; color:var(--hijau); }
    </style>
</head>
<body>
<header>
    <div class="wrap">
        <strong>🏥 AntriSehat</strong>
        <nav>
            <a href="{{ route('antrean.index') }}">Antrean</a>
            <a href="{{ route('antrean.berjalan') }}">Antrean Berjalan</a>
            <a href="{{ route('janji-temu.index') }}">Janji Temu</a>
            <a href="{{ route('dokters.index') }}">Dokter</a>
            <a href="{{ route('jadwal-dokters.index') }}">Jadwal Praktik</a>
        </nav>
    </div>
</header>
<main class="wrap">
    @if (session('status'))
        <div class="alert alert-ok">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
