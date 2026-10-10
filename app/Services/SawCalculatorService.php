<?php

namespace App\Services;

use App\Models\Alternative;
use App\Models\CalculationResult;
use App\Models\Criterion;
use App\Models\CriterionImportanceRating;
use App\Models\EWallet;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            CalculationResult::query()->delete();

            return [
                'has_data' => false,
                'message'  => 'Data kriteria atau alternatif masih kosong.',
            ];
        }

        // Match each alternative with the corresponding questionnaire wallet.
        $walletsByName = EWallet::all()->keyBy(fn (EWallet $wallet) => Str::lower(trim($wallet->name)));
        $alternativesByWallet = [];
        foreach ($alternatives as $alternative) {
            $wallet = $walletsByName->get(Str::lower(trim($alternative->name)));
            if ($wallet) {
                $alternativesByWallet[$wallet->id] = $alternative->id;
            }
        }

        if (count($alternativesByWallet) !== $alternatives->count()) {
            CalculationResult::query()->delete();

            return [
                'has_data' => false,
                'message' => 'Ranking belum dapat dihitung karena setiap alternatif harus memiliki e-wallet yang sesuai di kuesioner.',
            ];
        }

        // Only respondents who completed both parts of the questionnaire participate.
        $expectedRatingCount = count($alternativesByWallet) * $criteria->count();
        $completeRatingRespondentIds = Rating::query()
            ->select('respondent_id')
            ->whereIn('e_wallet_id', array_keys($alternativesByWallet))
            ->whereIn('criterion_id', $criteria->modelKeys())
            ->groupBy('respondent_id')
            ->havingRaw('COUNT(*) = ?', [$expectedRatingCount])
            ->pluck('respondent_id')
            ->all();

        $completeImportanceRespondentIds = CriterionImportanceRating::query()
            ->select('respondent_id')
            ->whereIn('criterion_id', $criteria->modelKeys())
            ->groupBy('respondent_id')
            ->havingRaw('COUNT(*) = ?', [$criteria->count()])
            ->pluck('respondent_id')
            ->all();

        $respondentIds = array_values(array_intersect(
            $completeRatingRespondentIds,
            $completeImportanceRespondentIds
        ));

        if ($respondentIds === []) {
            CalculationResult::query()->delete();

            return [
                'has_data' => false,
                'message' => 'Ranking belum dapat dihitung karena belum ada responden dengan penilaian e-wallet dan tingkat kepentingan semua kriteria yang lengkap.',
            ];
        }

        // 1. Derive normalized criterion weights from average importance responses.
        $averageImportance = CriterionImportanceRating::query()
            ->select('criterion_id')
            ->selectRaw('AVG(score) as average_score')
            ->whereIn('respondent_id', $respondentIds)
            ->whereIn('criterion_id', $criteria->modelKeys())
            ->groupBy('criterion_id')
            ->get()
            ->keyBy('criterion_id');

        $totalImportance = $criteria->sum(
            fn (Criterion $criterion) => (float) $averageImportance[$criterion->id]->average_score
        );

        if ($totalImportance <= 0) {
            CalculationResult::query()->delete();

            return [
                'has_data' => false,
                'message' => 'Ranking belum dapat dihitung karena total tingkat kepentingan kriteria harus lebih dari nol.',
            ];
        }

        $lastCriterionId = $criteria->last()->id;
        $assignedWeight = 0.0;
        foreach ($criteria as $criterion) {
            $weight = round((float) $averageImportance[$criterion->id]->average_score / $totalImportance, 10);
            if ($criterion->id === $lastCriterionId) {
                $weight = round(1 - $assignedWeight, 10);
            }

            $criterion->weight = $weight;
            $assignedWeight += $weight;
        }

        $totalWeight = round($criteria->sum('weight'), 10);
        $isWeightValid = abs($totalWeight - 1.0) < 0.001;

        // 2. Build Decision Matrix X from complete respondent rating averages.
        $averageScores = Rating::query()
            ->select('e_wallet_id', 'criterion_id')
            ->selectRaw('AVG(score) as average_score')
            ->whereIn('respondent_id', $respondentIds)
            ->whereIn('e_wallet_id', array_keys($alternativesByWallet))
            ->whereIn('criterion_id', $criteria->modelKeys())
            ->groupBy('e_wallet_id', 'criterion_id')
            ->get();

        $averageScoresByPair = $averageScores->keyBy(
            fn ($score) => $score->e_wallet_id . ':' . $score->criterion_id
        );
        $matrixX   = [];
        $missingScores = [];
        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $walletId = array_search($alt->id, $alternativesByWallet, true);
                $average = $walletId === false
                    ? null
                    : $averageScoresByPair->get($walletId . ':' . $crit->id);

                if (!$average) {
                    $missingScores[] = "{$alt->name} / {$crit->name}";
                    continue;
                }

                $matrixX[$alt->id][$crit->id] = (float) $average->average_score;
            }
        }

        if ($missingScores !== []) {
            CalculationResult::query()->delete();

            return [
                'has_data' => false,
                'message' => 'Ranking belum dapat dihitung karena belum ada penilaian responden untuk: '
                    . implode(', ', $missingScores) . '.',
            ];
        }

        // 3. Find Extreme Values (MAX for Benefit, MIN for Cost) per Criterion
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

        // 4. Normalize Decision Matrix (Matrix R)
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

        // 5. Calculate Weighted Values (W * R) & Final Preference Scores (V_i)
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

        // 6. Rank Alternatives (Descending Order)
        arsort($finalScores);

        $ranking = [];
        $rankPos = 1;
        foreach ($finalScores as $altId => $score) {
            $ranking[$altId] = $rankPos++;
        }

        // 7. Save/Update Calculation Results in Database
        DB::transaction(function () use ($criteria, $alternatives, $matrixX, $matrixR, $weightedMatrix, $finalScores, $ranking) {
            foreach ($criteria as $criterion) {
                if ((float) $criterion->getOriginal('weight') !== (float) $criterion->weight) {
                    $criterion->save();
                }
            }

            CalculationResult::whereNotIn('alternative_id', $alternatives->modelKeys())->delete();

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

        // Identify the highest-ranked alternative.
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
            'respondent_count' => count($respondentIds),
            'weight_respondent_count' => count($respondentIds),
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
