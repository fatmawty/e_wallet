<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $criteria = [
            'C1' => ['name' => 'Keamanan', 'type' => 'benefit', 'weight' => 0.151087],
            'C2' => ['name' => 'Kemudahan', 'type' => 'benefit', 'weight' => 0.141304],
            'C3' => ['name' => 'Promo', 'type' => 'benefit', 'weight' => 0.135870],
            'C4' => ['name' => 'Biaya Transaksi', 'type' => 'cost', 'weight' => 0.142391],
            'C5' => ['name' => 'Fitur', 'type' => 'benefit', 'weight' => 0.145652],
            'C6' => ['name' => 'Top Up', 'type' => 'benefit', 'weight' => 0.143478],
            'C7' => ['name' => 'Merchant', 'type' => 'benefit', 'weight' => 0.140217],
        ];

        $criteriaIds = [];
        foreach ($criteria as $code => $data) {
            DB::table('criteria')->updateOrInsert(
                ['code' => $code],
                array_merge($data, ['description' => $data['name'], 'updated_at' => $now, 'created_at' => $now])
            );
            $criteriaIds[$code] = DB::table('criteria')->where('code', $code)->value('id');
        }

        $alternatives = [
            'A1' => ['name' => 'GoPay', 'brand_color' => '#00AA13', 'logo_icon' => 'gopay-icon'],
            'A2' => ['name' => 'OVO', 'brand_color' => '#4C2A86', 'logo_icon' => 'ovo-icon'],
            'A3' => ['name' => 'ShopeePay', 'brand_color' => '#EE4D2D', 'logo_icon' => 'shopeepay-icon'],
            'A4' => ['name' => 'DANA', 'brand_color' => '#118EEA', 'logo_icon' => 'dana-icon'],
        ];

        $alternativeIds = [];
        foreach ($alternatives as $code => $data) {
            DB::table('alternatives')->updateOrInsert(
                ['code' => $code],
                array_merge($data, [
                    'description' => $data['description'] ?? null,
                    'status' => 'active',
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
            $alternativeIds[$code] = DB::table('alternatives')->where('code', $code)->value('id');
        }

        $scores = [
            'A1' => ['C1' => 5, 'C2' => 5, 'C3' => 3, 'C4' => 3, 'C5' => 5, 'C6' => 5, 'C7' => 5],
            'A2' => ['C1' => 4, 'C2' => 5, 'C3' => 3, 'C4' => 4, 'C5' => 4, 'C6' => 5, 'C7' => 4],
            'A3' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 3, 'C5' => 4, 'C6' => 4, 'C7' => 4],
            'A4' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 2, 'C5' => 5, 'C6' => 4, 'C7' => 4],
        ];

        foreach ($scores as $alternativeCode => $criteriaScores) {
            foreach ($criteriaScores as $criteriaCode => $score) {
                DB::table('alternative_scores')->updateOrInsert(
                    [
                        'alternative_id' => $alternativeIds[$alternativeCode],
                        'criteria_id' => $criteriaIds[$criteriaCode],
                    ],
                    ['score' => $score, 'updated_at' => $now, 'created_at' => $now]
                );
            }
        }
    }

    public function down(): void
    {
        // Dataset migrations are intentionally not destructive.
    }
};