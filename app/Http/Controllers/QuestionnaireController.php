<?php

namespace App\Http\Controllers;

use App\Models\EWallet;
use App\Models\Criterion;
use App\Models\Respondent;
use App\Models\Rating;
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
            'status' => 'required|string|max:100',
            'institution' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
            'primary_e_wallet_id' => 'required|exists:e_wallets,id',
            'general_feedback' => 'nullable|string|max:1000',
            'ratings' => 'required|array',
        ];

        // Ensure every ewallet has ratings for every criterion (1-5)
        foreach ($wallets as $wallet) {
            foreach ($criteria as $crit) {
                $rules["ratings.{$wallet->id}.{$crit->id}"] = 'required|integer|min:1|max:5';
            }
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama responden wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'institution.required' => 'Nama Perguruan Tinggi / Instansi wajib diisi.',
            'primary_e_wallet_id.required' => 'Silakan pilih E-Wallet utama yang paling sering Anda gunakan.',
            'ratings.*.*.required' => 'Mohon lengkapi semua penilaian skala Likert 1-5 untuk tiap kriteria.',
        ]);

        DB::transaction(function () use ($validated, $wallets, $criteria) {
            // Create Respondent record
            $respondent = Respondent::create([
                'name' => $validated['name'],
                'status' => $validated['status'],
                'institution' => $validated['institution'],
                'major' => $validated['major'] ?? null,
                'primary_e_wallet_id' => $validated['primary_e_wallet_id'],
                'general_feedback' => $validated['general_feedback'] ?? null,
            ]);

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
