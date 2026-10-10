<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criterion;
use App\Models\Alternative;
use App\Models\User;
use App\Services\SawCalculatorService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin & Responden Users
        User::updateOrCreate(
            ['email' => 'admin@ewallet.test'],
            ['name' => 'Admin SPK', 'password' => 'password', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'responden@ewallet.test'],
            ['name' => 'Responden', 'password' => 'password', 'role' => 'responden']
        );

        // 7 Official Criteria (C1 - C7)
        $criteriaData = [
            ['code' => 'C1', 'name' => 'Keamanan', 'type' => 'benefit', 'weight' => 0.1429, 'description' => 'Tingkat keamanan akun dan transaksi e-wallet.'],
            ['code' => 'C2', 'name' => 'Kemudahan Penggunaan', 'type' => 'benefit', 'weight' => 0.1429, 'description' => 'Kemudahan penggunaan dan navigasi aplikasi e-wallet.'],
            ['code' => 'C3', 'name' => 'Promo dan Cashback', 'type' => 'benefit', 'weight' => 0.1429, 'description' => 'Ketersediaan promo, diskon, dan cashback transaksi.'],
            ['code' => 'C4', 'name' => 'Biaya Transaksi', 'type' => 'benefit', 'weight' => 0.1429, 'description' => 'Keterjangkauan biaya transaksi dan biaya admin (nilai lebih tinggi berarti biaya dirasa lebih terjangkau).'],
            ['code' => 'C5', 'name' => 'Kelengkapan Fitur', 'type' => 'benefit', 'weight' => 0.1428, 'description' => 'Kelengkapan fitur pembayaran dan layanan digital.'],
            ['code' => 'C6', 'name' => 'Kemudahan Top Up', 'type' => 'benefit', 'weight' => 0.1428, 'description' => 'Kemudahan dan ragam metode pengisian saldo (top up).'],
            ['code' => 'C7', 'name' => 'Merchant/Layanan', 'type' => 'benefit', 'weight' => 0.1428, 'description' => 'Luasnya jaringan merchant dan ekosistem pembayaran.'],
        ];

        foreach ($criteriaData as $c) {
            Criterion::updateOrCreate(['code' => $c['code']], $c);
        }

        // 4 Official E-Wallet Alternatives (A1 - A4)
        $alternativesData = [
            ['code' => 'A1', 'name' => 'DANA', 'description' => 'Dompet digital Indonesia dengan fitur pembayaran tagihan dan transaksi harian.', 'brand_color' => '#118EEA', 'logo_icon' => 'dana-icon', 'status' => 'active'],
            ['code' => 'A2', 'name' => 'OVO', 'description' => 'Aplikasi pembayaran digital yang terintegrasi luas di merchant online dan offline.', 'brand_color' => '#4C2A86', 'logo_icon' => 'ovo-icon', 'status' => 'active'],
            ['code' => 'A3', 'name' => 'ShopeePay', 'description' => 'Platform dompet digital dan pembayaran digital terintegrasi di ekosistem Shopee.', 'brand_color' => '#EE4D2D', 'logo_icon' => 'shopeepay-icon', 'status' => 'active'],
            ['code' => 'A4', 'name' => 'GoPay', 'description' => 'Platform dompet digital untuk transaksi harian dan berbagai layanan GoTo.', 'brand_color' => '#00AA13', 'logo_icon' => 'gopay-icon', 'status' => 'active'],
        ];

        foreach ($alternativesData as $a) {
            Alternative::updateOrCreate(['code' => $a['code']], $a);
        }

        // Results are calculated after respondent ratings are collected.
        (new SawCalculatorService())->calculate();
    }
}
