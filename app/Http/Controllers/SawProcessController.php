<?php

namespace App\Http\Controllers;

use App\Services\SawCalculatorService;
use App\Models\CalculationResult;
use Illuminate\Http\Request;

class SawProcessController extends Controller
{
    public function index(SawCalculatorService $sawService)
    {
        $calcResult = $sawService->calculate();

        return view('saw.process', [
            'calcResult' => $calcResult,
        ]);
    }

    public function calculate(SawCalculatorService $sawService)
    {
        $calcResult = $sawService->calculate();

        return redirect()->route('saw.process')
            ->with('success', 'Perhitungan metode Simple Additive Weighting (SAW) berhasil dijalankan secara dinamis!');
    }
}
