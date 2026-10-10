<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CriteriaController extends Controller
{
    protected SawCalculatorService $sawService;

    public function __construct(SawCalculatorService $sawService)
    {
        $this->sawService = $sawService;
    }

    public function index()
    {
        $criteria = Criterion::orderBy('code')->get();
        $totalWeight = round($criteria->sum('weight'), 4);
        $isWeightValid = abs($totalWeight - 1.0) <= 0.001;

        return view('kriteria.index', compact('criteria', 'totalWeight', 'isWeightValid'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:criteria,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['benefit', 'cost'])],
            'description' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.unique' => 'Kode kriteria sudah digunakan, gunakan kode lain.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'type.required' => 'Jenis kriteria wajib dipilih.',
        ]);

        $validated['weight'] = 0;
        Criterion::create($validated);

        // Recalculate SAW
        $this->sawService->calculate();

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan dan perhitungan SAW telah diperbarui.');
    }

    public function update(Request $request, Criterion $kriterium)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('criteria', 'code')->ignore($kriterium->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['benefit', 'cost'])],
            'description' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.unique' => 'Kode kriteria sudah digunakan.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'type.required' => 'Jenis kriteria wajib dipilih.',
        ]);

        $kriterium->update($validated);

        // Recalculate SAW
        $this->sawService->calculate();

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui dan perhitungan SAW telah dihitung ulang.');
    }

    public function destroy(Criterion $kriterium)
    {
        $kriterium->delete();

        // Recalculate SAW
        $this->sawService->calculate();

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
