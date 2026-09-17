<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class HasilRankingController extends Controller
{
    protected SawCalculatorService $sawService;

    public function __construct(SawCalculatorService $sawService)
    {
        $this->sawService = $sawService;
    }

    public function index()
    {
        $sawData = $this->sawService->calculate();

        return view('hasil_ranking.index', [
            'rankings' => $sawData['rankings'],
            'bestAlternative' => $sawData['best_alternative'],
            'bestScore' => $sawData['rankings']->first()?->final_score ?? 0,
            'preferences' => $sawData['preferences'],
            'criteria' => $sawData['criteria'],
            'sawData' => $sawData,
        ]);
    }

    public function detail($id)
    {
        $sawData = $this->sawService->calculate();
        $alternative = Alternative::findOrFail($id);
        $preference = $sawData['preferences'][$alternative->id] ?? null;

        if (!$preference) {
            return redirect()->route('hasil-ranking.index')->with('error', 'Detail data alternatif tidak ditemukan.');
        }

        $rankResult = $sawData['rankings']->firstWhere('alternative_id', $alternative->id);

        return view('hasil_ranking.detail', [
            'alternative' => $alternative,
            'preference' => $preference,
            'rankResult' => $rankResult,
            'criteria' => $sawData['criteria'],
        ]);
    }
}
