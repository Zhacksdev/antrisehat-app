@extends('layouts.app')

@section('title', 'Dokter')

@section('content')
    <section class="card">
        <h2>Data Dokter</h2>
        <a class="btn" href="{{ route('dokters.create') }}">+ Tambah Dokter</a>
        <table>
            <thead>
            <tr><th>Nama</th><th>Poli</th><th>Jumlah Jadwal</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($dokters as $dokter)
                <tr>
                    <td>{{ $dokter->nama }}</td>
                    <td>{{ $dokter->poli->nama }}</td>
                    <td>{{ $dokter->jadwals->count() }}</td>
                    <td>
                        <a class="btn btn-sekunder" href="{{ route('dokters.edit', $dokter) }}">Ubah</a>
                        <form class="inline" method="POST" action="{{ route('dokters.destroy', $dokter) }}" onsubmit="return confirm('Hapus dokter ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-bahaya" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada dokter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
