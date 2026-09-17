<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('alternatives')
            ->where('code', 'A3')
            ->update([
                'name' => 'ShopeePay',
                'description' => 'Dompet digital untuk pembayaran, transfer, top up, dan berbagai promo di ekosistem Shopee.',
                'brand_color' => '#EE4D2D',
                'logo_icon' => 'shopeepay-icon',
            ]);
    }

    public function down(): void
    {
        DB::table('alternatives')
            ->where('code', 'A3')
            ->update([
                'name' => 'ShopeePay',
                'description' => 'Dompet digital untuk pembayaran, transfer, top up, dan berbagai promo di ekosistem Shopee.',
                'brand_color' => '#EE4D2D',
                'logo_icon' => 'shopeepay-icon',
            ]);
    }
};