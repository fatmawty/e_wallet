<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->unsignedSmallInteger('cohort_year')->nullable()->after('major');
            $table->timestamp('consented_at')->nullable()->after('general_feedback');
        });
    }

    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropColumn(['cohort_year', 'consented_at']);
        });
    }
};
