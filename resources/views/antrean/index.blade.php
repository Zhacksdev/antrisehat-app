@extends('layouts.app')

@section('title', 'Antrean')

@section('content')
    <div class="grid">
        <section class="card">
            <h2>Ambil Nomor Antrean</h2>
            <form method="POST" action="{{ route('antrean.store') }}">
                @csrf
                <label for="poli_id">Poli</label>
                <select id="poli_id" name="poli_id" required>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id }}">{{ $poli->nama }} ({{ $poli->kode }})</option>
                    @endforeach
                </select>
                <label for="nama">Nama pasien (opsional)</label>
                <input id="nama" name="nama" value="{{ old('nama') }}">
                <label for="no_hp">No. HP (opsional)</label>
                <input id="no_hp" name="no_hp" value="{{ old('no_hp') }}">
                <button type="submit">Ambil Nomor</button>
            </form>
        </section>

        <section class="card">
            <h2>Panggil Antrean (Petugas)</h2>
            <form method="POST" action="{{ route('antrean.panggil') }}">
                @csrf
                <label for="poli_panggil">Poli</label>
                <select id="poli_panggil" name="poli_id" required>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id }}">{{ $poli->nama }}</option>
                    @endforeach
                </select>
                <button type="submit">Panggil Berikutnya</button>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>Ringkasan Hari Ini</h2>
        @include('antrean.partials.tabel', ['ringkasan' => $ringkasan])
    </section>
@endsection
