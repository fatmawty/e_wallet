<?php

namespace App\Services;

use App\Models\Alternative;
use App\Models\CalculationResult;
use App\Models\Criterion;
use App\Models\EWallet;
use App\Models\Rating;
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
        $criteria = Criterion::orderBy('id')->get();
        $alternatives = Alternative::where('status', 'active')->orderBy('id')->get();

        if ($criteria->isEmpty() || $alternatives->isEmpty()) {
            return [
                'has_data' => false,
                'message' => 'Data kriteria atau alternatif masih kosong.',
            ];
        }

        $totalWeight = round($criteria->sum('weight'), 4);
        $isWeightValid = abs($totalWeight - 1.0) < 0.001;

        // Build Decision Matrix X from the average questionnaire ratings.
        $wallets = EWallet::all()->keyBy(fn($wallet) => strtolower(trim($wallet->name)));
        $ratingAverages = Rating::query()
            ->select('e_wallet_id', 'criterion_id')
            ->selectRaw('AVG(score) as average_score')
            ->groupBy('e_wallet_id', 'criterion_id')
            ->get()
            ->keyBy(fn($rating) => $rating->e_wallet_id . '_' . $rating->criterion_id);

        $matrixX = [];
        $missingRatings = [];

        foreach ($alternatives as $alt) {
            $wallet = $wallets->get(strtolower(trim($alt->name)));

            foreach ($criteria as $crit) {
                $key = $wallet ? $wallet->id . '_' . $crit->id : null;
                $average = $key ? $ratingAverages->get($key) : null;

                if (!$average) {
                    $missingRatings[] = $alt->name . ' - ' . $crit->name;
                    continue;
                }

                $matrixX[$alt->id][$crit->id] = round((float) $average->average_score, 4);
            }
        }

        if ($missingRatings) {
            return [
                'has_data' => false,
                'message' => 'Data rating kuesioner belum lengkap untuk: ' . implode(', ', $missingRatings) . '.',
            ];
        }

        // Find max (benefit) and min (cost) values for each criterion.
        $extremeValues = [];
        foreach ($criteria as $crit) {
            $colValues = [];
            foreach ($alternatives as $alt) {
                $colValues[] = $matrixX[$alt->id][$crit->id];
            }

            $extremeValues[$crit->id] = [
                'type' => strtolower($crit->type),
                'max' => max($colValues),
                'min' => min($colValues),
            ];
        }

        // Normalize the decision matrix.
        $matrixR = [];
        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $x = $matrixX[$alt->id][$crit->id];
                $type = $extremeValues[$crit->id]['type'];

                if ($type === 'benefit') {
                    $max = $extremeValues[$crit->id]['max'];
                    $r = $max > 0 ? ($x / $max) : 0;
                } else {
                    $min = $extremeValues[$crit->id]['min'];
                    $r = $x > 0 ? ($min / $x) : 0;
                }

                $matrixR[$alt->id][$crit->id] = round($r, 4);
            }
        }

        // Apply the stored criterion weights and calculate preference values.
        $weightedMatrix = [];
        $finalScores = [];

        foreach ($alternatives as $alt) {
            $sumV = 0.0;
            foreach ($criteria as $crit) {
                $r = $matrixR[$alt->id][$crit->id];
                $w = (float) $crit->weight;
                $wR = round($w * $r, 4);

                $weightedMatrix[$alt->id][$crit->id] = $wR;
                $sumV += $wR;
            }
            $finalScores[$alt->id] = round($sumV, 4);
        }

        arsort($finalScores);

        $ranking = [];
        $rankPos = 1;
        foreach ($finalScores as $altId => $score) {
            $ranking[$altId] = $rankPos++;
        }

        DB::transaction(function () use ($alternatives, $matrixX, $matrixR, $weightedMatrix, $finalScores, $ranking) {
            foreach ($alternatives as $alt) {
                CalculationResult::updateOrCreate(
                    ['alternative_id' => $alt->id],
                    [
                        'final_score' => $finalScores[$alt->id],
                        'rank' => $ranking[$alt->id],
                        'details' => [
                            'raw_scores' => $matrixX[$alt->id],
                            'normalized_scores' => $matrixR[$alt->id],
                            'weighted_scores' => $weightedMatrix[$alt->id],
                        ],
                    ]
                );
            }
        });

        $bestAlternative = null;
        $bestScore = -1;

        foreach ($alternatives as $alt) {
            if ($ranking[$alt->id] === 1) {
                $bestAlternative = $alt;
                $bestScore = $finalScores[$alt->id];
                break;
            }
        }

        return [
            'has_data' => true,
            'is_weight_valid' => $isWeightValid,
            'total_weight' => $totalWeight,
            'criteria' => $criteria,
            'alternatives' => $alternatives,
            'matrix_x' => $matrixX,
            'extreme_values' => $extremeValues,
            'matrix_r' => $matrixR,
            'weighted_matrix' => $weightedMatrix,
            'final_scores' => $finalScores,
            'ranking' => $ranking,
            'best_alternative' => $bestAlternative,
            'best_score' => $bestScore,
        ];
    }
}
