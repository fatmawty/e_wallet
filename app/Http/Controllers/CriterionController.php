<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index(SawCalculatorService $sawService)
    {
        $calcResult = $sawService->calculate();
        $criteria = Criterion::orderBy('code')->get();
        $totalWeight = round($criteria->sum('weight'), 4);
        $isWeightValid = abs($totalWeight - 1.0) < 0.001;

        return view('criteria.index', [
            'criteria' => $criteria,
            'totalWeight' => $totalWeight,
            'isWeightValid' => $isWeightValid,
            'hasDynamicWeights' => $calcResult['has_data'],
            'weightRespondentCount' => $calcResult['has_data'] ? $calcResult['weight_respondent_count'] : 0,
        ]);
    }

    public function store(Request $request, SawCalculatorService $sawService)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'description' => 'nullable|string|max:1000',
        ], [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.unique' => 'Kode kriteria sudah digunakan.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'type.required' => 'Jenis kriteria (benefit/cost) wajib dipilih.',
        ]);

        $validated['weight'] = 0;
        Criterion::create($validated);

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
            'description' => 'nullable|string|max:1000',
        ], [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.unique' => 'Kode kriteria sudah digunakan.',
            'name.required' => 'Nama kriteria wajib diisi.',
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
