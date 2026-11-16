<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Poli;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $dokters = Dokter::with('poli', 'jadwals')->orderBy('nama')->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $dokters]);
        }

        return view('dokters.index', compact('dokters'));
    }

    public function create()
    {
        return view('dokters.form', ['dokter' => new Dokter, 'polis' => Poli::orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        $dokter = Dokter::create($this->validated($request));

        if ($request->wantsJson()) {
            return response()->json(['data' => $dokter->load('poli')], 201);
        }

        return redirect()->route('dokters.index')->with('status', "Dokter {$dokter->nama} ditambahkan.");
    }

    public function show(Request $request, Dokter $dokter)
    {
        $dokter->load('poli', 'jadwals');

        if ($request->wantsJson()) {
            return response()->json(['data' => $dokter]);
        }

        return redirect()->route('dokters.edit', $dokter);
    }

    public function edit(Dokter $dokter)
    {
        return view('dokters.form', ['dokter' => $dokter, 'polis' => Poli::orderBy('nama')->get()]);
    }

    public function update(Request $request, Dokter $dokter)
    {
        $dokter->update($this->validated($request));

        if ($request->wantsJson()) {
            return response()->json(['data' => $dokter->load('poli')]);
        }

        return redirect()->route('dokters.index')->with('status', "Dokter {$dokter->nama} diperbarui.");
    }

    public function destroy(Request $request, Dokter $dokter)
    {
        $dokter->delete();

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('dokters.index')->with('status', 'Dokter dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'poli_id' => ['required', 'exists:polis,id'],
        ]);
    }
}
