<?php

namespace App\Http\Controllers;

use App\Services\SawCalculatorService;
use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\CalculationResult;
use App\Models\Respondent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(SawCalculatorService $sawService)
    {
        $calcResult = $sawService->calculate();

        $totalAlternatives = Alternative::count();
        $totalCriteria = Criterion::count();
        $totalRespondents = Respondent::count();
        $results = CalculationResult::with('alternative')->orderBy('rank')->get();

        return view('dashboard', [
            'totalAlternatives' => $totalAlternatives,
            'totalCriteria' => $totalCriteria,
            'totalRespondents' => $totalRespondents,
            'totalComparedWallets' => 4,
            'googleFormUrl' => config('services.google_form.url'),
            'calcResult' => $calcResult,
            'results' => $results,
        ]);
    }
}
