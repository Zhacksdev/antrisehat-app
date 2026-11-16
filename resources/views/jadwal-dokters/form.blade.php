@extends('layouts.app')

@section('title', $jadwal->exists ? 'Ubah Jadwal' : 'Tambah Jadwal')

@section('content')
    <section class="card">
        <h2>{{ $jadwal->exists ? 'Ubah Jadwal Praktik' : 'Tambah Jadwal Praktik' }}</h2>
        <form method="POST" action="{{ $jadwal->exists ? route('jadwal-dokters.update', $jadwal) : route('jadwal-dokters.store') }}">
            @csrf
            @if ($jadwal->exists) @method('PUT') @endif
            <label for="dokter_id">Dokter</label>
            <select id="dokter_id" name="dokter_id" required>
                @foreach ($dokters as $dokter)
                    <option value="{{ $dokter->id }}" @selected(old('dokter_id', $jadwal->dokter_id) == $dokter->id)>
                        {{ $dokter->nama }} ({{ $dokter->poli->nama }})
                    </option>
                @endforeach
            </select>
            <label for="hari">Hari</label>
            <select id="hari" name="hari" required>
                @foreach (\App\Models\JadwalDokter::NAMA_HARI as $nilai => $nama)
                    <option value="{{ $nilai }}" @selected(old('hari', $jadwal->hari) == $nilai)>{{ $nama }}</option>
                @endforeach
            </select>
            <label for="jam_mulai">Jam mulai</label>
            <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '') }}" required>
            <label for="jam_selesai">Jam selesai</label>
            <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '') }}" required>
            <label for="maks_pasien">Maks pasien per sesi</label>
            <input type="number" id="maks_pasien" name="maks_pasien" min="1" max="{{ config('antrisehat.maks_pasien_per_sesi') }}" value="{{ old('maks_pasien', $jadwal->maks_pasien) }}">
            <button type="submit">Simpan</button>
            <a class="btn btn-sekunder" href="{{ route('jadwal-dokters.index') }}">Batal</a>
        </form>
    </section>
@endsection
