@extends('layouts.app')

@section('title', 'Janji Temu')

@section('content')
    <section class="card">
        <h2>Buat Janji Temu</h2>
        <form method="POST" action="{{ route('janji-temu.store') }}">
            @csrf
            <div class="grid">
                <div>
                    <label for="jadwal_dokter_id">Dokter &amp; jadwal praktik</label>
                    <select id="jadwal_dokter_id" name="jadwal_dokter_id" required>
                        @foreach ($jadwals as $jadwal)
                            <option value="{{ $jadwal->id }}" @selected(old('jadwal_dokter_id') == $jadwal->id)>
                                {{ $jadwal->dokter->nama }} ({{ $jadwal->dokter->poli->nama }}) — {{ $jadwal->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" min="{{ today()->toDateString() }}" required>
                </div>
                <div>
                    <label for="jam_mulai">Jam mulai</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                </div>
                <div>
                    <label for="jam_selesai">Jam selesai (opsional, default {{ config('antrisehat.durasi_slot_menit') }} menit)</label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}">
                </div>
                <div>
                    <label for="nama">Nama pasien</label>
                    <input id="nama" name="nama" value="{{ old('nama') }}" required>
                </div>
                <div>
                    <label for="no_hp">No. HP</label>
                    <input id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                </div>
            </div>
            <button type="submit">Buat Janji Temu</button>
        </form>
    </section>

    <section class="card">
        <h2>Janji Temu Mendatang</h2>
        <table>
            <thead>
            <tr><th>Tanggal</th><th>Jam</th><th>Dokter</th><th>Poli</th><th>Pasien</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse ($janjiTemus as $janji)
                <tr>
                    <td>{{ $janji->tanggal }}</td>
                    <td>{{ substr($janji->jam_mulai, 0, 5) }}–{{ substr($janji->jam_selesai, 0, 5) }}</td>
                    <td>{{ $janji->dokter->nama }}</td>
                    <td>{{ $janji->dokter->poli->nama }}</td>
                    <td>{{ $janji->pasien->nama }}</td>
                    <td>{{ $janji->status->value }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Belum ada janji temu.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
