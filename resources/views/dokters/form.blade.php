@extends('layouts.app')

@section('title', $dokter->exists ? 'Ubah Dokter' : 'Tambah Dokter')

@section('content')
    <section class="card">
        <h2>{{ $dokter->exists ? 'Ubah Dokter' : 'Tambah Dokter' }}</h2>
        <form method="POST" action="{{ $dokter->exists ? route('dokters.update', $dokter) : route('dokters.store') }}">
            @csrf
            @if ($dokter->exists) @method('PUT') @endif
            <label for="nama">Nama</label>
            <input id="nama" name="nama" value="{{ old('nama', $dokter->nama) }}" required>
            <label for="poli_id">Poli</label>
            <select id="poli_id" name="poli_id" required>
                @foreach ($polis as $poli)
                    <option value="{{ $poli->id }}" @selected(old('poli_id', $dokter->poli_id) == $poli->id)>{{ $poli->nama }}</option>
                @endforeach
            </select>
            <button type="submit">Simpan</button>
            <a class="btn btn-sekunder" href="{{ route('dokters.index') }}">Batal</a>
        </form>
    </section>
@endsection
