<?php

namespace App\Http\Controllers;

use App\Models\EWallet;
use App\Models\Criterion;
use App\Models\Respondent;
use App\Models\Rating;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $wallets = EWallet::all();
        $criteria = Criterion::all();
        $totalRespondents = Respondent::count();

        // 1. Matrix: E-Wallet x Criterion scores
        $matrix = [];
        foreach ($wallets as $wallet) {
            $row = [
                'wallet' => $wallet,
                'overall_avg' => round($wallet->ratings()->avg('score') ?? 0, 2),
                'scores' => [],
            ];

            foreach ($criteria as $crit) {
                $avg = round(
                    $wallet->ratings()
                        ->where('criterion_id', $crit->id)
                        ->avg('score') ?? 0,
                    2
                );
                $row['scores'][$crit->id] = $avg;
            }

            $matrix[] = $row;
        }

        // Sort matrix by overall_avg descending
        usort($matrix, fn($a, $b) => $b['overall_avg'] <=> $a['overall_avg']);

        $radarData = [
            'labels' => $criteria->pluck('code')->values()->all(),
            'datasets' => array_map(function ($row) use ($criteria) {
                return [
                    'label' => $row['wallet']->name,
                    'data' => $criteria->map(fn($crit) => $row['scores'][$crit->id])->values()->all(),
                    'borderColor' => $row['wallet']->color_hex,
                    'backgroundColor' => $row['wallet']->color_hex . '33',
                    'pointBackgroundColor' => $row['wallet']->color_hex,
                    'pointBorderColor' => '#ffffff',
                    'pointHoverBackgroundColor' => '#ffffff',
                    'pointHoverBorderColor' => $row['wallet']->color_hex,
                    'borderWidth' => 2,
                    'pointRadius' => 3,
                ];
            }, $matrix),
        ];

        // 2. Best E-Wallet per Criterion
        $bestByCriterion = [];
        foreach ($criteria as $crit) {
            $bestWallet = null;
            $maxScore = -1;

            foreach ($wallets as $wallet) {
                $avg = round(
                    $wallet->ratings()
                        ->where('criterion_id', $crit->id)
                        ->avg('score') ?? 0,
                    2
                );

                if ($avg > $maxScore) {
                    $maxScore = $avg;
                    $bestWallet = $wallet;
                }
            }

            $bestByCriterion[] = [
                'criterion' => $crit,
                'best_wallet' => $bestWallet,
                'max_score' => $maxScore,
            ];
        }

        // 3. Demographics breakdown by University / Institution
        $institutionBreakdown = Respondent::selectRaw('institution, count(*) as count')
            ->groupBy('institution')
            ->orderByDesc('count')
            ->get();

        // 4. Primary E-Wallet Usage Breakdown
        $usageBreakdown = [];
        foreach ($wallets as $wallet) {
            $count = Respondent::where('primary_e_wallet_id', $wallet->id)->count();
            $pct = $totalRespondents > 0 ? round(($count / $totalRespondents) * 100, 1) : 0;
            $usageBreakdown[] = [
                'wallet' => $wallet,
                'count' => $count,
                'pct' => $pct,
            ];
        }

        return view('analytics.index', [
            'wallets' => $wallets,
            'criteria' => $criteria,
            'totalRespondents' => $totalRespondents,
            'matrix' => $matrix,
            'radarData' => $radarData,
            'bestByCriterion' => $bestByCriterion,
            'institutionBreakdown' => $institutionBreakdown,
            'usageBreakdown' => $usageBreakdown,
        ]);
    }
}
