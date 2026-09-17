<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criterion;
use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\User;
use App\Services\SawCalculatorService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ewallet.test'],
            ['name' => 'Admin SPK', 'password' => 'password', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'responden@ewallet.test'],
            ['name' => 'Responden', 'password' => 'password', 'role' => 'responden']
        );

        $criteriaData = [
            ['code' => 'C1', 'name' => 'Keamanan', 'type' => 'benefit', 'weight' => 0.151087, 'description' => 'Tingkat keamanan dan perlindungan transaksi e-wallet.'],
            ['code' => 'C2', 'name' => 'Kemudahan', 'type' => 'benefit', 'weight' => 0.141304, 'description' => 'Kemudahan penggunaan dan navigasi aplikasi.'],
            ['code' => 'C3', 'name' => 'Promo', 'type' => 'benefit', 'weight' => 0.135870, 'description' => 'Ketersediaan promo dan keuntungan transaksi.'],
            ['code' => 'C4', 'name' => 'Biaya Transaksi', 'type' => 'cost', 'weight' => 0.142391, 'description' => 'Besaran biaya transaksi. Nilai yang lebih rendah lebih baik.'],
            ['code' => 'C5', 'name' => 'Fitur', 'type' => 'benefit', 'weight' => 0.145652, 'description' => 'Kelengkapan fitur layanan e-wallet.'],
            ['code' => 'C6', 'name' => 'Top Up', 'type' => 'benefit', 'weight' => 0.143478, 'description' => 'Kemudahan dan kualitas layanan top up.'],
            ['code' => 'C7', 'name' => 'Merchant', 'type' => 'benefit', 'weight' => 0.140217, 'description' => 'Luasnya penerimaan e-wallet pada merchant.'],
        ];

        $criteriaMap = [];
        foreach ($criteriaData as $c) {
            $crit = Criterion::updateOrCreate(['code' => $c['code']], $c);
            $criteriaMap[$c['code']] = $crit;
        }

        $alternativesData = [
            ['code' => 'A1', 'name' => 'GoPay', 'description' => 'Platform dompet digital untuk transaksi harian dan berbagai layanan digital.', 'brand_color' => '#00AA13', 'logo_icon' => 'gopay-icon', 'status' => 'active'],
            ['code' => 'A2', 'name' => 'OVO', 'description' => 'Aplikasi pembayaran digital yang terintegrasi luas di merchant online dan offline.', 'brand_color' => '#4C2A86', 'logo_icon' => 'ovo-icon', 'status' => 'active'],
            ['code' => 'A3', 'name' => 'ShopeePay', 'description' => 'Dompet digital untuk pembayaran, transfer, top up, dan berbagai promo di ekosistem Shopee.', 'brand_color' => '#EE4D2D', 'logo_icon' => 'shopeepay-icon', 'status' => 'active'],
            ['code' => 'A4', 'name' => 'DANA', 'description' => 'Dompet digital Indonesia dengan fitur pembayaran tagihan dan transaksi harian.', 'brand_color' => '#118EEA', 'logo_icon' => 'dana-icon', 'status' => 'active'],
        ];

        $altMap = [];
        foreach ($alternativesData as $a) {
            $alt = Alternative::updateOrCreate(['code' => $a['code']], $a);
            $altMap[$a['code']] = $alt;
        }

        $initialScores = [
            'A1' => ['C1' => 5, 'C2' => 5, 'C3' => 3, 'C4' => 3, 'C5' => 5, 'C6' => 5, 'C7' => 5],
            'A2' => ['C1' => 4, 'C2' => 5, 'C3' => 3, 'C4' => 4, 'C5' => 4, 'C6' => 5, 'C7' => 4],
            'A3' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 3, 'C5' => 4, 'C6' => 4, 'C7' => 4],
            'A4' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 2, 'C5' => 5, 'C6' => 4, 'C7' => 4],
        ];

        foreach ($initialScores as $altCode => $scores) {
            $alt = $altMap[$altCode];
            foreach ($scores as $critCode => $scoreVal) {
                AlternativeScore::updateOrCreate(
                    ['alternative_id' => $alt->id, 'criteria_id' => $criteriaMap[$critCode]->id],
                    ['score' => $scoreVal]
                );
            }
        }

        (new SawCalculatorService())->calculate();
    }
}
