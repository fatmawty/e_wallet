<?php

namespace App\Services;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CalculationResult;
use App\Models\Criterion;
use Illuminate\Support\Facades\DB;

class SawCalculatorService
{
    /**
     * Perform SAW (Simple Additive Weighting) calculation.
     *
     * @return array
     */
    public function calculate(): array
    {
        $criteria = Criterion::orderBy('code')->get();
        $alternatives = Alternative::where('status', 'active')->orderBy('code')->get();

        if ($criteria->isEmpty() || $alternatives->isEmpty()) {
            return [
                'has_data' => false,
                'message'  => 'Data kriteria atau alternatif masih kosong.',
            ];
        }

        $totalWeight   = round($criteria->sum('weight'), 4);
        $isWeightValid = abs($totalWeight - 1.0) < 0.001;

        // 1. Build Decision Matrix X from AlternativeScore (scale 1 - 5)
        $scoresMap = AlternativeScore::all()->keyBy(fn($s) => $s->alternative_id . '_' . $s->criteria_id);
        $matrixX   = [];

        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $key = $alt->id . '_' . $crit->id;
                $scoreObj = $scoresMap->get($key);
                $matrixX[$alt->id][$crit->id] = $scoreObj ? (float)$scoreObj->score : 3.0;
            }
        }

        // 2. Find Extreme Values (MAX for Benefit, MIN for Cost) per Criterion
        $extremeValues = [];
        foreach ($criteria as $crit) {
            $colValues = [];
            foreach ($alternatives as $alt) {
                $colValues[] = $matrixX[$alt->id][$crit->id];
            }

            $extremeValues[$crit->id] = [
                'type' => strtolower($crit->type),
                'max'  => max($colValues),
                'min'  => min($colValues),
            ];
        }

        // 3. Normalize Decision Matrix (Matrix R)
        $matrixR = [];
        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $x    = $matrixX[$alt->id][$crit->id];
                $type = $extremeValues[$crit->id]['type'];

                if ($type === 'benefit') {
                    $max = $extremeValues[$crit->id]['max'];
                    $r   = $max > 0 ? ($x / $max) : 0;
                } else {
                    $min = $extremeValues[$crit->id]['min'];
                    $r   = $x > 0 ? ($min / $x) : 0;
                }

                $matrixR[$alt->id][$crit->id] = round($r, 4);
            }
        }

        // 4. Calculate Weighted Values (W * R) & Final Preference Scores (V_i)
        $weightedMatrix = [];
        $finalScores    = [];

        foreach ($alternatives as $alt) {
            $sumV = 0.0;
            foreach ($criteria as $crit) {
                $r  = $matrixR[$alt->id][$crit->id];
                $w  = (float)$crit->weight;
                $wR = round($w * $r, 4);

                $weightedMatrix[$alt->id][$crit->id] = $wR;
                $sumV += $wR;
            }
            $finalScores[$alt->id] = round($sumV, 4);
        }

        // 5. Rank Alternatives (Descending Order)
        arsort($finalScores);

        $ranking = [];
        $rankPos = 1;
        foreach ($finalScores as $altId => $score) {
            $ranking[$altId] = $rankPos++;
        }

        // 6. Save/Update Calculation Results in Database
        DB::transaction(function () use ($alternatives, $matrixX, $matrixR, $weightedMatrix, $finalScores, $ranking) {
            foreach ($alternatives as $alt) {
                CalculationResult::updateOrCreate(
                    ['alternative_id' => $alt->id],
                    [
                        'final_score' => $finalScores[$alt->id],
                        'rank'        => $ranking[$alt->id],
                        'details'     => [
                            'raw_scores'        => $matrixX[$alt->id],
                            'normalized_scores' => $matrixR[$alt->id],
                            'weighted_scores'   => $weightedMatrix[$alt->id],
                        ],
                    ]
                );
            }
        });

        // 7. Best Alternative Winner
        $bestAlternative = null;
        $bestScore       = -1;

        foreach ($alternatives as $alt) {
            if ($ranking[$alt->id] === 1) {
                $bestAlternative = $alt;
                $bestScore       = $finalScores[$alt->id];
                break;
            }
        }

        return [
            'has_data'         => true,
            'is_weight_valid'  => $isWeightValid,
            'total_weight'     => $totalWeight,
            'criteria'         => $criteria,
            'alternatives'     => $alternatives,
            'matrix_x'         => $matrixX,
            'extreme_values'   => $extremeValues,
            'matrix_r'         => $matrixR,
            'weighted_matrix'  => $weightedMatrix,
            'final_scores'     => $finalScores,
            'ranking'          => $ranking,
            'best_alternative' => $bestAlternative,
            'best_score'       => $bestScore,
        ];
    }
}
