@extends('layouts.app')

@section('title', 'Jadwal Praktik')

@section('content')
    <section class="card">
        <h2>Jadwal Praktik Dokter</h2>
        <a class="btn" href="{{ route('jadwal-dokters.create') }}">+ Tambah Jadwal</a>
        <table>
            <thead>
            <tr><th>Dokter</th><th>Poli</th><th>Hari</th><th>Jam</th><th>Maks Pasien</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwal->dokter->nama }}</td>
                    <td>{{ $jadwal->dokter->poli->nama }}</td>
                    <td>{{ $jadwal->nama_hari }}</td>
                    <td>{{ substr($jadwal->jam_mulai, 0, 5) }}–{{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                    <td>{{ $jadwal->maks_pasien }}</td>
                    <td>
                        <a class="btn btn-sekunder" href="{{ route('jadwal-dokters.edit', $jadwal) }}">Ubah</a>
                        <form class="inline" method="POST" action="{{ route('jadwal-dokters.destroy', $jadwal) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-bahaya" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Belum ada jadwal.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
