<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index(SawCalculatorService $sawService)
    {
        $calcResult = $sawService->calculate();
        $alternatives = Alternative::orderBy('code')->get();
        $criteria = Criterion::orderBy('code')->get();

        return view('about', [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'hasDynamicWeights' => $calcResult['has_data'],
        ]);
    }
}
