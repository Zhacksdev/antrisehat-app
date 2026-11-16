<?php

namespace App\Http\Controllers;

use App\Models\JadwalDokter;
use App\Models\JanjiTemu;
use App\Models\Pasien;
use App\Services\JanjiTemuService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JanjiTemuController extends Controller
{
    public function __construct(private readonly JanjiTemuService $janjiTemu) {}

    public function index(Request $request)
    {
        $janjiTemus = JanjiTemu::with('dokter.poli', 'pasien')
            ->where('tanggal', '>=', today()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $janjiTemus]);
        }

        return view('janji-temu.index', [
            'janjiTemus' => $janjiTemus,
            'jadwals' => JadwalDokter::with('dokter.poli')->orderBy('hari')->orderBy('jam_mulai')->get(),
        ]);
    }

    /** POST /janji-temu — validasi slot jadwal, bentrok, dan kuota 30. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jadwal_dokter_id' => ['required', 'exists:jadwal_dokters,id'],
            'dokter_id' => ['nullable', 'exists:dokters,id'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'date_format:H:i', 'after:jam_mulai'],
            'pasien_id' => ['nullable', 'required_without:no_hp', 'exists:pasiens,id'],
            'nama' => ['nullable', 'required_with:no_hp', 'string', 'max:255'],
            'no_hp' => ['nullable', 'required_without:pasien_id', 'string', 'max:20'],
        ]);

        $jadwal = JadwalDokter::findOrFail($data['jadwal_dokter_id']);

        if (! empty($data['dokter_id']) && (int) $data['dokter_id'] !== $jadwal->dokter_id) {
            throw ValidationException::withMessages(['dokter_id' => 'Jadwal tidak sesuai dengan dokter yang dipilih.']);
        }

        $pasien = ! empty($data['pasien_id'])
            ? Pasien::findOrFail($data['pasien_id'])
            : Pasien::firstOrCreate(['no_hp' => $data['no_hp']], ['nama' => $data['nama']]);

        $janji = $this->janjiTemu->buat($jadwal, $pasien, $data['tanggal'], $data['jam_mulai'], $data['jam_selesai'] ?? null);

        if ($request->wantsJson()) {
            return response()->json(['data' => $janji->load('dokter', 'pasien')], 201);
        }

        return redirect()->route('janji-temu.index')->with(
            'status',
            "Janji temu dibuat untuk {$pasien->nama} pada {$janji->tanggal} pukul ".substr($janji->jam_mulai, 0, 5).'.',
        );
    }
}
