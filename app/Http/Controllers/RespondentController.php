<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\EWallet;
use App\Models\Respondent;
use App\Services\SawCalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RespondentController extends Controller
{
    public function index(): View
    {
        return view('respondents.index', [
            'respondents' => Respondent::latest()
                ->paginate(20),
        ]);
    }

    public function show(Respondent $respondent): View
    {
        $respondent->load([
            'ratings.eWallet',
            'ratings.criterion',
            'criterionImportanceRatings.criterion',
        ]);

        return view('respondents.show', [
            'respondent' => $respondent,
            'criteria' => Criterion::orderBy('code')->get(),
            'wallets' => EWallet::orderBy('name')->get(),
        ]);
    }

    public function edit(Respondent $respondent): View
    {
        return view('respondents.edit', [
            'respondent' => $respondent,
        ]);
    }

    public function update(Request $request, Respondent $respondent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'cohort_year' => 'required|integer|min:2000|max:' . now()->year,
            'general_feedback' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Nama responden wajib diisi.',
            'institution.required' => 'Nama Universitas / Institusi wajib diisi.',
            'major.required' => 'Program studi wajib diisi.',
            'cohort_year.required' => 'Tahun angkatan wajib diisi.',
        ]);

        $respondent->update($validated);

        return redirect()->route('respondents.index')
            ->with('success', "Data profil responden {$respondent->name} berhasil diperbarui.");
    }

    public function destroy(Respondent $respondent, SawCalculatorService $sawService): RedirectResponse
    {
        $name = $respondent->name;

        DB::transaction(function () use ($respondent) {
            $respondent->ratings()->delete();
            $respondent->criterionImportanceRatings()->delete();
            $respondent->delete();
        });

        // Recalculate SAW ranking without this respondent
        $sawService->calculate();

        return redirect()->route('respondents.index')
            ->with('success', "Data responden {$name} beserta seluruh jawabannya berhasil dihapus. Ranking SAW telah diperbarui.");
    }

    /**
     * Export all questionnaire response data to CSV for validity & reliability testing (SPSS / Excel).
     */
    public function exportCsv(): StreamedResponse
    {
        $respondents = Respondent::with([
            'criterionImportanceRatings.criterion',
            'ratings.eWallet',
            'ratings.criterion',
        ])->orderBy('id')->get();

        $criteria = Criterion::orderBy('code')->get();
        $wallets = EWallet::whereIn('name', ['DANA', 'OVO', 'ShopeePay', 'GoPay'])
            ->orderByRaw("CASE name
                WHEN 'DANA' THEN 1
                WHEN 'OVO' THEN 2
                WHEN 'ShopeePay' THEN 3
                WHEN 'GoPay' THEN 4
                ELSE 5
            END")
            ->get();

        $filename = 'rekap_jawaban_kuesioner_validitas_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($respondents, $criteria, $wallets) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Microsoft Excel opens it directly without garbled characters
            fputs($handle, "\xEF\xBB\xBF");

            // Header columns
            $headerRow = [
                'No',
                'Nama Responden',
                'Universitas/Institusi',
                'Program Studi',
                'Angkatan',
                'Tanggal Pengisian',
            ];

            // 7 Columns for Importance of Criteria
            foreach ($criteria as $criterion) {
                $headerRow[] = "Kepentingan_{$criterion->code} ({$criterion->name})";
            }
            $headerRow[] = 'Total_Skor_Kepentingan';

            // 28 Columns for E-Wallet Ratings
            foreach ($wallets as $wallet) {
                foreach ($criteria as $criterion) {
                    $headerRow[] = "{$wallet->name}_{$criterion->code}";
                }
            }

            fputcsv($handle, $headerRow);

            // Data rows per respondent
            foreach ($respondents as $index => $respondent) {
                $importanceMap = $respondent->criterionImportanceRatings->keyBy('criterion_id');
                $ratingsMap = $respondent->ratings->keyBy(fn ($r) => $r->e_wallet_id . ':' . $r->criterion_id);

                $row = [
                    $index + 1,
                    $respondent->name,
                    $respondent->institution,
                    $respondent->major,
                    $respondent->cohort_year ?? '-',
                    $respondent->created_at?->format('d/m/Y H:i'),
                ];

                $totalImportance = 0;
                foreach ($criteria as $criterion) {
                    $score = $importanceMap->get($criterion->id)?->score ?? 0;
                    $row[] = $score;
                    $totalImportance += $score;
                }
                $row[] = $totalImportance;

                foreach ($wallets as $wallet) {
                    foreach ($criteria as $criterion) {
                        $score = $ratingsMap->get($wallet->id . ':' . $criterion->id)?->score ?? 0;
                        $row[] = $score;
                    }
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}