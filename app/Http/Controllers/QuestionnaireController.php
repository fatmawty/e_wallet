<?php

namespace App\Http\Controllers;

use App\Models\EWallet;
use App\Models\Criterion;
use App\Models\CriterionImportanceRating;
use App\Models\Rating;
use App\Models\Respondent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionnaireController extends Controller
{
    protected function getQuestionnaireWallets()
    {
        return EWallet::whereIn('name', ['OVO', 'DANA', 'ShopeePay', 'GoPay'])
            ->orderByRaw("CASE name
                WHEN 'OVO' THEN 1
                WHEN 'DANA' THEN 2
                WHEN 'ShopeePay' THEN 3
                WHEN 'GoPay' THEN 4
                ELSE 5
            END")
            ->get();
    }

    public function create()
    {
        $wallets = $this->getQuestionnaireWallets();
        $criteria = Criterion::orderBy('code')->get();

        return view('kuesioner.create', [
            'wallets' => $wallets,
            'criteria' => $criteria,
        ]);
    }

    public function store(Request $request)
    {
        $wallets = $this->getQuestionnaireWallets();
        $criteria = Criterion::orderBy('code')->get();

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'student_confirmation' => 'accepted',
            'research_consent' => 'accepted',
            'institution' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'cohort_year' => 'required|integer|min:2000|max:' . now()->year,
            'general_feedback' => 'nullable|string|max:1000',
            'importance' => 'required|array',
            'ratings' => 'required|array',
        ];

        foreach ($criteria as $crit) {
            $rules["importance.{$crit->id}"] = 'required|integer|min:1|max:5';
        }

        foreach ($wallets as $wallet) {
            foreach ($criteria as $crit) {
                $rules["ratings.{$wallet->id}.{$crit->id}"] = 'required|integer|min:1|max:5';
            }
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama responden wajib diisi.',
            'student_confirmation.accepted' => 'Konfirmasi bahwa Anda mahasiswa aktif wajib disetujui.',
            'research_consent.accepted' => 'Persetujuan partisipasi penelitian wajib disetujui untuk mengirim jawaban.',
            'institution.required' => 'Nama Perguruan Tinggi / Instansi wajib diisi.',
            'major.required' => 'Program studi wajib diisi.',
            'cohort_year.required' => 'Tahun angkatan wajib diisi.',
            'cohort_year.integer' => 'Tahun angkatan harus berupa tahun.',
            'cohort_year.min' => 'Tahun angkatan tidak valid.',
            'cohort_year.max' => 'Tahun angkatan tidak boleh melebihi tahun saat ini.',
            'importance.*.required' => 'Mohon isi tingkat kepentingan untuk semua kriteria.',
            'ratings.*.*.required' => 'Mohon lengkapi semua penilaian skala Likert 1-5 untuk tiap kriteria.',
        ]);

        DB::transaction(function () use ($validated, $wallets, $criteria) {
            $respondent = Respondent::create([
                'name' => $validated['name'],
                'status' => 'Mahasiswa Aktif',
                'institution' => $validated['institution'],
                'major' => $validated['major'],
                'cohort_year' => (int) $validated['cohort_year'],
                'general_feedback' => $validated['general_feedback'] ?? null,
                'consented_at' => now(),
            ]);

            foreach ($criteria as $crit) {
                CriterionImportanceRating::create([
                    'respondent_id' => $respondent->id,
                    'criterion_id' => $crit->id,
                    'score' => (int) $validated['importance'][$crit->id],
                ]);
            }

            // Save Ratings for each E-Wallet and Criterion
            foreach ($wallets as $wallet) {
                foreach ($criteria as $crit) {
                    $score = (int) $validated['ratings'][$wallet->id][$crit->id];
                    Rating::create([
                        'respondent_id' => $respondent->id,
                        'e_wallet_id' => $wallet->id,
                        'criterion_id' => $crit->id,
                        'score' => $score,
                    ]);
                }
            }
        });

        return redirect()->route('ranking.index')
            ->with('success', 'Terima kasih! Jawaban Anda berhasil disimpan. Berikut hasil rekomendasi terbaru.');
    }
}
