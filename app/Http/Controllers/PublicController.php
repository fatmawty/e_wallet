<?php

namespace App\Http\Controllers;

use App\Services\SawCalculatorService;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(SawCalculatorService $sawService): View
    {
        return view('public.home', $this->resultData($sawService));
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
