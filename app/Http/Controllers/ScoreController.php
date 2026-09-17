<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criterion;
use App\Models\AlternativeScore;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index()
    {
        $alternatives = Alternative::where('status', 'active')->orderBy('code')->get();
        $criteria = Criterion::orderBy('code')->get();

        $scores = [];
        $existingScores = AlternativeScore::all();

        foreach ($existingScores as $scoreObj) {
            $scores[$scoreObj->alternative_id][$scoreObj->criteria_id] = $scoreObj->score;
        }

        return view('scores.index', [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'scores' => $scores,
        ]);
    }

    public function store(Request $request, SawCalculatorService $sawService)
    {
        $alternatives = Alternative::where('status', 'active')->get();
        $criteria = Criterion::all();

        $rules = [
            'scores' => 'required|array',
        ];

        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $rules["scores.{$alt->id}.{$crit->id}"] = 'required|integer|min:1|max:5';
            }
        }

        $validated = $request->validate($rules, [
            'scores.*.*.required' => 'Semua nilai alternatif wajib diisi.',
            'scores.*.*.integer' => 'Nilai alternatif harus berupa angka bulat (1 - 5).',
            'scores.*.*.min' => 'Nilai minimal adalah 1.',
            'scores.*.*.max' => 'Nilai maksimal adalah 5.',
        ]);

        foreach ($validated['scores'] as $altId => $critScores) {
            foreach ($critScores as $critId => $scoreValue) {
                AlternativeScore::updateOrCreate(
                    [
                        'alternative_id' => $altId,
                        'criteria_id' => $critId,
                    ],
                    [
                        'score' => (int)$scoreValue,
                    ]
                );
            }
        }

        // Trigger SAW Calculation
        $sawService->calculate();

        return redirect()->route('scores.index')
            ->with('success', 'Penilaian alternatif berhasil disimpan dan perhitungan SAW telah diperbarui!');
    }

    public function reset(SawCalculatorService $sawService)
    {
        // Default sample ratings
        $initialScores = [
            'A1' => ['C1' => 4, 'C2' => 5, 'C3' => 4, 'C4' => 5, 'C5' => 3], // GoPay
            'A2' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 4, 'C5' => 3], // OVO
            'A3' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 4, 'C5' => 1], // ShopeePay
            'A4' => ['C1' => 5, 'C2' => 5, 'C3' => 5, 'C4' => 4, 'C5' => 2], // DANA
        ];

        $criteria = Criterion::all()->keyBy('code');
        $alternatives = Alternative::all()->keyBy('code');

        foreach ($initialScores as $altCode => $scores) {
            if (isset($alternatives[$altCode])) {
                $alt = $alternatives[$altCode];
                foreach ($scores as $critCode => $scoreVal) {
                    if (isset($criteria[$critCode])) {
                        $crit = $criteria[$critCode];

                        AlternativeScore::updateOrCreate(
                            [
                                'alternative_id' => $alt->id,
                                'criteria_id' => $crit->id,
                            ],
                            [
                                'score' => $scoreVal,
                            ]
                        );
                    }
                }
            }
        }

        // Trigger SAW Calculation
        $sawService->calculate();

        return redirect()->route('scores.index')
            ->with('success', 'Penilaian telah di-reset ke nilai sampel awal.');
    }
}
