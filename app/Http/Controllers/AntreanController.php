<?php

namespace App\Http\Controllers;

use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Poli;
use App\Services\AntreanService;
use Illuminate\Http\Request;

class AntreanController extends Controller
{
    public function __construct(private readonly AntreanService $antrean) {}

    /** Halaman petugas/pasien: ambil nomor & panggil antrean. */
    public function index()
    {
        return view('antrean.index', [
            'polis' => Poli::orderBy('nama')->get(),
            'ringkasan' => $this->antrean->ringkasanBerjalan(),
        ]);
    }

    /** POST /antrean — ambil nomor antrean per poli. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'poli_id' => ['required', 'exists:polis,id'],
            'jadwal_dokter_id' => ['nullable', 'exists:jadwal_dokters,id'],
            'pasien_id' => ['nullable', 'exists:pasiens,id'],
            'nama' => ['nullable', 'required_with:no_hp', 'string', 'max:255'],
            'no_hp' => ['nullable', 'required_with:nama', 'string', 'max:20'],
        ]);

        $pasien = $this->resolvePasien($data);
        $jadwal = isset($data['jadwal_dokter_id']) ? JadwalDokter::find($data['jadwal_dokter_id']) : null;

        $antrean = $this->antrean->ambil(Poli::findOrFail($data['poli_id']), $pasien, $jadwal);

        if ($request->wantsJson()) {
            return response()->json(['data' => $antrean->append('kode')], 201);
        }

        return redirect()->route('antrean.index')
            ->with('status', "Nomor antrean Anda: {$antrean->kode} ({$antrean->poli->nama}).");
    }

    /** POST /antrean/panggil — panggil antrean berikutnya pada poli. */
    public function panggil(Request $request)
    {
        $data = $request->validate(['poli_id' => ['required', 'exists:polis,id']]);
        $poli = Poli::findOrFail($data['poli_id']);

        $antrean = $this->antrean->panggilBerikutnya($poli);

        if ($request->wantsJson()) {
            return $antrean
                ? response()->json(['data' => $antrean->append('kode')])
                : response()->json(['message' => "Tidak ada antrean menunggu di {$poli->nama}."], 404);
        }

        return redirect()->route('antrean.index')->with(
            'status',
            $antrean ? "Memanggil {$antrean->kode} ke {$poli->nama}." : "Tidak ada antrean menunggu di {$poli->nama}.",
        );
    }

    /** GET /antrean/berjalan — tampilan antrean berjalan (halaman publik). */
    public function berjalan(Request $request)
    {
        $ringkasan = $this->antrean->ringkasanBerjalan();

        if ($request->wantsJson()) {
            return response()->json(['tanggal' => today()->toDateString(), 'data' => $ringkasan]);
        }

        return view('antrean.berjalan', compact('ringkasan'));
    }

    private function resolvePasien(array $data): ?Pasien
    {
        if (! empty($data['pasien_id'])) {
            return Pasien::find($data['pasien_id']);
        }

        if (! empty($data['no_hp'])) {
            return Pasien::firstOrCreate(['no_hp' => $data['no_hp']], ['nama' => $data['nama']]);
        }

        return null;
    }
}
