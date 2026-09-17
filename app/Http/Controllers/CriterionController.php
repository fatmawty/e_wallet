<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::orderBy('code')->get();
        $totalWeight = round($criteria->sum('weight'), 4);
        $isWeightValid = abs($totalWeight - 1.0) < 0.001;

        return view('criteria.index', [
            'criteria' => $criteria,
            'totalWeight' => $totalWeight,
            'isWeightValid' => $isWeightValid,
        ]);
    }

    public function store(Request $request, SawCalculatorService $sawService)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:1',
            'description' => 'nullable|string|max:1000',
        ], [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.unique' => 'Kode kriteria sudah digunakan.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'type.required' => 'Jenis kriteria (benefit/cost) wajib dipilih.',
            'weight.required' => 'Bobot kriteria wajib diisi.',
            'weight.numeric' => 'Bobot kriteria harus berupa angka.',
            'weight.min' => 'Bobot kriteria tidak boleh bernilai negatif.',
            'weight.max' => 'Bobot kriteria maksimal adalah 1 (atau 100%).',
        ]);

        $criterion = Criterion::create($validated);

        // Attach default score 3 for all existing alternatives
        $alternatives = Alternative::all();
        foreach ($alternatives as $alt) {
            AlternativeScore::firstOrCreate(
                [
                    'alternative_id' => $alt->id,
                    'criteria_id' => $criterion->id,
                ],
                [
                    'score' => 3,
                ]
            );
        }

        // Recalculate SAW
        $sawService->calculate();

        return redirect()->route('criteria.index')
            ->with('success', 'Kriteria baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id, SawCalculatorService $sawService)
    {
        $criterion = Criterion::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:1',
            'description' => 'nullable|string|max:1000',
        ], [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.unique' => 'Kode kriteria sudah digunakan.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'weight.required' => 'Bobot kriteria wajib diisi.',
            'weight.numeric' => 'Bobot kriteria harus berupa angka.',
            'weight.min' => 'Bobot kriteria tidak boleh negatif.',
        ]);

        $criterion->update($validated);

        // Recalculate SAW
        $sawService->calculate();

        return redirect()->route('criteria.index')
            ->with('success', 'Data kriteria berhasil diperbarui.');
    }

    public function destroy($id, SawCalculatorService $sawService)
    {
        $criterion = Criterion::findOrFail($id);
        $criterion->delete();

        // Recalculate SAW
        $sawService->calculate();

        return redirect()->route('criteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
