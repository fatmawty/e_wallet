<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('alternative_scores');

        DB::table('criteria')
            ->where('code', 'C4')
            ->update([
                'type' => 'benefit',
                'description' => 'Keterjangkauan biaya transaksi dan biaya admin (nilai lebih tinggi berarti biaya dirasa lebih terjangkau).',
            ]);
    }

    public function down(): void
    {
        Schema::create('alternative_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternative_id')->constrained('alternatives')->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('criteria')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(3);
            $table->timestamps();
            $table->unique(['alternative_id', 'criteria_id']);
        });

        DB::table('criteria')
            ->where('code', 'C4')
            ->update([
                'type' => 'cost',
                'description' => 'Besaran biaya transaksi dan biaya admin (Nilai lebih rendah lebih baik).',
            ]);
    }
};
