@extends('layouts.app')

@section('title', 'Antrean Berjalan')

@push('head')
    <meta http-equiv="refresh" content="10">
@endpush

@section('content')
    <h1>Antrean Berjalan</h1>
    <p class="muted">{{ now()->translatedFormat('l, d F Y H:i') }} · diperbarui otomatis setiap 10 detik</p>

    <div class="grid">
        @forelse ($ringkasan as $baris)
            <section class="card">
                <h2>{{ $baris['poli'] }}</h2>
                <div class="muted">Sedang dipanggil</div>
                <div class="nomor">
                    {{ $baris['nomor_dipanggil'] ? sprintf('%s-%03d', $baris['kode'], $baris['nomor_dipanggil']) : '—' }}
                </div>
                <div class="muted">
                    Berikutnya: {{ $baris['nomor_berikutnya'] ? sprintf('%s-%03d', $baris['kode'], $baris['nomor_berikutnya']) : '-' }}
                    · Menunggu: {{ $baris['jumlah_menunggu'] }}
                </div>
            </section>
        @empty
            <section class="card muted">Belum ada data poli.</section>
        @endforelse
    </div>
@endsection
