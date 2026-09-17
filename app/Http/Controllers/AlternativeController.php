<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\AlternativeScore;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    public function index()
    {
        $alternatives = Alternative::orderBy('code')->get();

        return view('alternatives.index', [
            'alternatives' => $alternatives,
        ]);
    }

    public function store(Request $request, SawCalculatorService $sawService)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:alternatives,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'brand_color' => 'nullable|string|max:20',
        ], [
            'code.required' => 'Kode alternatif wajib diisi.',
            'code.unique' => 'Kode alternatif sudah digunakan.',
            'name.required' => 'Nama alternatif E-Wallet wajib diisi.',
        ]);

        $alternative = Alternative::create($validated);

        // Attach default scores (3) for all criteria
        $criteria = Criterion::all();
        foreach ($criteria as $crit) {
            AlternativeScore::create([
                'alternative_id' => $alternative->id,
                'criteria_id' => $crit->id,
                'score' => 3,
            ]);
        }

        // Recalculate SAW
        $sawService->calculate();

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternatif E-Wallet baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id, SawCalculatorService $sawService)
    {
        $alternative = Alternative::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:alternatives,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'brand_color' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'Kode alternatif wajib diisi.',
            'code.unique' => 'Kode alternatif sudah digunakan.',
            'name.required' => 'Nama alternatif E-Wallet wajib diisi.',
        ]);

        $alternative->update($validated);

        // Recalculate SAW
        $sawService->calculate();

        return redirect()->route('alternatives.index')
            ->with('success', 'Data alternatif E-Wallet berhasil diperbarui.');
    }

    public function destroy($id, SawCalculatorService $sawService)
    {
        $alternative = Alternative::findOrFail($id);
        $alternative->delete();

        // Recalculate SAW
        $sawService->calculate();

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternatif E-Wallet berhasil dihapus.');
    }
}
