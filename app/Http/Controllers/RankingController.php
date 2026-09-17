<?php

namespace App\Http\Controllers;

use App\Services\SawCalculatorService;
use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\CalculationResult;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(SawCalculatorService $sawService)
    {
        $calcResult = $sawService->calculate();
        $results = CalculationResult::with('alternative')->orderBy('rank')->get();

        return view('ranking.index', [
            'calcResult' => $calcResult,
            'results' => $results,
        ]);
    }

    public function detail($id, SawCalculatorService $sawService)
    {
        $alternative = Alternative::findOrFail($id);
        $result = CalculationResult::where('alternative_id', $id)->firstOrFail();
        $criteria = Criterion::orderBy('code')->get();

        $calcResult = $sawService->calculate();

        return view('ranking.detail', [
            'alternative' => $alternative,
            'result' => $result,
            'criteria' => $criteria,
            'calcResult' => $calcResult,
        ]);
    }
}
