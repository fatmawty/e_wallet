<?php

namespace App\Http\Controllers;

use App\Models\EWallet;
use App\Models\Criterion;
use App\Models\Respondent;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EWalletController extends Controller
{
    /**
     * Calculate the fixed Excel SAW dataset.
     *
     * @return array{bobot: array, matriksX: array, hasilAkhir: array, pemenangUtama: array}
     */
    public function calculateSaw(): array
    {
        $bobot = [
            'C1' => 0.151087,
            'C2' => 0.141304,
            'C3' => 0.135870,
            'C4' => 0.142391,
            'C5' => 0.145652,
            'C6' => 0.143478,
            'C7' => 0.140217,
        ];

        $matriksX = [
            'A1' => ['nama' => 'GoPay', 'nilai' => ['C1' => 5, 'C2' => 5, 'C3' => 3, 'C4' => 3, 'C5' => 5, 'C6' => 5, 'C7' => 5]],
            'A2' => ['nama' => 'OVO', 'nilai' => ['C1' => 4, 'C2' => 5, 'C3' => 3, 'C4' => 4, 'C5' => 4, 'C6' => 5, 'C7' => 4]],
            'A3' => ['nama' => 'ShopeePay', 'nilai' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 3, 'C5' => 4, 'C6' => 4, 'C7' => 4]],
            'A4' => ['nama' => 'DANA', 'nilai' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 2, 'C5' => 5, 'C6' => 4, 'C7' => 4]],
        ];

        $jenisKriteria = [
            'C1' => 'benefit', 'C2' => 'benefit', 'C3' => 'benefit',
            'C4' => 'cost',
            'C5' => 'benefit', 'C6' => 'benefit', 'C7' => 'benefit',
        ];

        $nilaiKolom = [];
        foreach ($bobot as $kodeKriteria => $weight) {
            $nilaiKolom[$kodeKriteria] = array_map(
                fn (array $alternatif): int => $alternatif['nilai'][$kodeKriteria],
                $matriksX
            );
        }

        $max = array_map('max', $nilaiKolom);
        $min = array_map('min', $nilaiKolom);
        $hasilAkhir = [];

        foreach ($matriksX as $kodeAlternatif => $alternatif) {
            $normalisasi = [];
            $nilaiPreferensi = 0.0;

            foreach ($bobot as $kodeKriteria => $weight) {
                $nilai = $alternatif['nilai'][$kodeKriteria];
                $normalisasi[$kodeKriteria] = $jenisKriteria[$kodeKriteria] === 'cost'
                    ? $min[$kodeKriteria] / $nilai
                    : $nilai / $max[$kodeKriteria];
                $nilaiPreferensi += $normalisasi[$kodeKriteria] * $weight;
            }

            $hasilAkhir[] = [
                'kode' => $kodeAlternatif,
                'nama' => $alternatif['nama'],
                'normalisasi' => $normalisasi,
                'nilai' => $nilaiPreferensi,
            ];
        }

        usort($hasilAkhir, fn (array $a, array $b): int => $b['nilai'] <=> $a['nilai']);
        foreach ($hasilAkhir as $peringkat => &$hasil) {
            $hasil['peringkat'] = $peringkat + 1;
        }
        unset($hasil);

        return [
            'bobot' => $bobot,
            'matriksX' => $matriksX,
            'hasilAkhir' => $hasilAkhir,
            'pemenangUtama' => $hasilAkhir[0],
        ];
    }

    public function index()
    {
        $hasilSaw = $this->calculateSaw();
        $wallets = EWallet::withCount('primaryRespondents')->get();
        $criteria = Criterion::all();
        $totalRespondents = Respondent::count();

        // Process overall ranking and stats for each e-wallet
        $rankingData = $wallets->map(function ($wallet) use ($totalRespondents, $criteria) {
            $avgScore = round($wallet->ratings()->avg('score') ?? 0, 2);
            $totalRatings = $wallet->ratings()->count();

            // Usage percentage among respondents
            $usagePct = $totalRespondents > 0
                ? round(($wallet->primary_respondents_count / $totalRespondents) * 100, 1)
                : 0;

            // Score per criterion for radar chart
            $criterionScores = [];
            foreach ($criteria as $crit) {
                $cAvg = round(
                    $wallet->ratings()
                        ->where('criterion_id', $crit->id)
                        ->avg('score') ?? 0,
                    2
                );
                $criterionScores[$crit->id] = $cAvg;
            }

            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'slug' => $wallet->slug,
                'tagline' => $wallet->tagline,
                'description' => $wallet->description,
                'color_hex' => $wallet->color_hex,
                'badge_text' => $wallet->badge_text,
                'avg_score' => $avgScore,
                'total_ratings' => $totalRatings,
                'primary_count' => $wallet->primary_respondents_count,
                'usage_pct' => $usagePct,
                'criterion_scores' => $criterionScores,
            ];
        });

        // Sort by avg_score descending for main leaderboard
        $sortedRanking = $rankingData->sortByDesc('avg_score')->values();

        // Assign ranks (1 to 4)
        $rankedWallets = $sortedRanking->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // Most popular by usage
        $mostPopularWallet = $rankedWallets->sortByDesc('primary_count')->first();

        // Recent student feedback
        $recentRespondents = Respondent::with('primaryEWallet')
            ->whereNotNull('general_feedback')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('home.index', [
            'wallets' => $rankedWallets,
            'criteria' => $criteria,
            'totalRespondents' => $totalRespondents,
            'topWallet' => $rankedWallets->first(),
            'mostPopularWallet' => $mostPopularWallet,
            'recentRespondents' => $recentRespondents,
            'bobot' => $hasilSaw['bobot'],
            'matriksX' => $hasilSaw['matriksX'],
            'hasilAkhir' => $hasilSaw['hasilAkhir'],
            'pemenangUtama' => $hasilSaw['pemenangUtama'],
        ]);
    }

    public function show($slug)
    {
        $wallet = EWallet::where('slug', $slug)->firstOrFail();
        $criteria = Criterion::all();
        $totalRespondents = Respondent::count();

        $avgScore = round($wallet->ratings()->avg('score') ?? 0, 2);
        $primaryCount = $wallet->primaryRespondents()->count();
        $usagePct = $totalRespondents > 0
            ? round(($primaryCount / $totalRespondents) * 100, 1)
            : 0;

        // Ratings breakdown per criterion
        $criterionBreakdown = [];
        foreach ($criteria as $crit) {
            $avg = round(
                $wallet->ratings()
                    ->where('criterion_id', $crit->id)
                    ->avg('score') ?? 0,
                2
            );

            // Distribution of 1-5 stars for this criterion
            $distribution = [];
            for ($star = 5; $star >= 1; $star--) {
                $count = $wallet->ratings()
                    ->where('criterion_id', $crit->id)
                    ->where('score', $star)
                    ->count();
                $totalForCrit = $wallet->ratings()->where('criterion_id', $crit->id)->count();
                $pct = $totalForCrit > 0 ? round(($count / $totalForCrit) * 100) : 0;
                $distribution[$star] = [
                    'count' => $count,
                    'pct' => $pct,
                ];
            }

            $criterionBreakdown[] = [
                'criterion' => $crit,
                'avg_score' => $avg,
                'distribution' => $distribution,
            ];
        }

        // Overall star distribution across all criteria for this wallet
        $starCounts = [];
        $totalRatings = $wallet->ratings()->count();
        for ($star = 5; $star >= 1; $star--) {
            $c = $wallet->ratings()->where('score', $star)->count();
            $starCounts[$star] = [
                'count' => $c,
                'pct' => $totalRatings > 0 ? round(($c / $totalRatings) * 100) : 0,
            ];
        }

        // Primary users feedback
        $feedbacks = Respondent::where('primary_e_wallet_id', $wallet->id)
            ->whereNotNull('general_feedback')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ewallet.show', [
            'wallet' => $wallet,
            'avgScore' => $avgScore,
            'primaryCount' => $primaryCount,
            'usagePct' => $usagePct,
            'criterionBreakdown' => $criterionBreakdown,
            'starCounts' => $starCounts,
            'totalRatings' => $totalRatings,
            'feedbacks' => $feedbacks,
        ]);
    }
}
