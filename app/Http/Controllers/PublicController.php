<?php

namespace App\Http\Controllers;

use App\Services\SawCalculatorService;
use App\Models\Criterion;
use App\Models\EWallet;
use App\Models\Respondent;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(SawCalculatorService $sawService): View
    {
        return view('public.home', $this->resultData($sawService) + [
            'criteria' => Criterion::orderBy('code')->get(),
            'wallets' => EWallet::whereIn('name', ['GoPay', 'ShopeePay', 'OVO', 'DANA'])
                ->orderByRaw("CASE name
                    WHEN 'GoPay' THEN 1
                    WHEN 'ShopeePay' THEN 2
                    WHEN 'OVO' THEN 3
                    WHEN 'DANA' THEN 4
                    ELSE 5
                END")
                ->get(),
            'respondentCount' => Respondent::count(),
            'respondentTarget' => 30,
        ]);
    }

    public function result(SawCalculatorService $sawService): View
    {
        return view('public.result', $this->resultData($sawService));
    }

    private function resultData(SawCalculatorService $sawService): array
    {
        $calcResult = $sawService->calculate();
        $ranking = [];

        if ($calcResult['has_data']) {
            foreach ($calcResult['alternatives'] as $alternative) {
                $ranking[] = [
                    'alternative' => $alternative,
                    'rank' => $calcResult['ranking'][$alternative->id],
                    'score' => $calcResult['final_scores'][$alternative->id],
                ];
            }

            usort($ranking, fn(array $left, array $right) => $left['rank'] <=> $right['rank']);
        }

        return [
            'calcResult' => $calcResult,
            'ranking' => $ranking,
        ];
    }
}
