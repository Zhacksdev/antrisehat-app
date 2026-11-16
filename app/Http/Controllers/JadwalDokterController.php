<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use Illuminate\Http\Request;

class JadwalDokterController extends Controller
{
    public function index(Request $request)
    {
        $jadwals = JadwalDokter::with('dokter.poli')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $jadwals]);
        }

        return view('jadwal-dokters.index', compact('jadwals'));
    }

    public function create()
    {
        return view('jadwal-dokters.form', [
            'jadwal' => new JadwalDokter(['maks_pasien' => config('antrisehat.maks_pasien_per_sesi')]),
            'dokters' => Dokter::with('poli')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $jadwal = JadwalDokter::create($this->validated($request));

        if ($request->wantsJson()) {
            return response()->json(['data' => $jadwal->load('dokter')], 201);
        }

        return redirect()->route('jadwal-dokters.index')->with('status', 'Jadwal praktik ditambahkan.');
    }

    public function show(Request $request, JadwalDokter $jadwalDokter)
    {
        if ($request->wantsJson()) {
            return response()->json(['data' => $jadwalDokter->load('dokter.poli')]);
        }

        return redirect()->route('jadwal-dokters.edit', $jadwalDokter);
    }

    public function edit(JadwalDokter $jadwalDokter)
    {
        return view('jadwal-dokters.form', [
            'jadwal' => $jadwalDokter,
            'dokters' => Dokter::with('poli')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, JadwalDokter $jadwalDokter)
    {
        $jadwalDokter->update($this->validated($request));

        if ($request->wantsJson()) {
            return response()->json(['data' => $jadwalDokter->load('dokter')]);
        }

        return redirect()->route('jadwal-dokters.index')->with('status', 'Jadwal praktik diperbarui.');
    }

    public function destroy(Request $request, JadwalDokter $jadwalDokter)
    {
        $jadwalDokter->delete();

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('jadwal-dokters.index')->with('status', 'Jadwal praktik dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'dokter_id' => ['required', 'exists:dokters,id'],
            'hari' => ['required', 'integer', 'between:1,7'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'maks_pasien' => ['nullable', 'integer', 'min:1', 'max:'.config('antrisehat.maks_pasien_per_sesi')],
        ]);

        $data['maks_pasien'] ??= config('antrisehat.maks_pasien_per_sesi');

        return $data;
    }
}
