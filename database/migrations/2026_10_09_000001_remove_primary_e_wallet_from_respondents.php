<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('respondents', 'primary_e_wallet_id')) {
            Schema::table('respondents', function (Blueprint $table) {
                $table->dropColumn('primary_e_wallet_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('respondents', 'primary_e_wallet_id')) {
            Schema::table('respondents', function (Blueprint $table) {
                $table->unsignedBigInteger('primary_e_wallet_id')->nullable();
            });
        }
    }
};
