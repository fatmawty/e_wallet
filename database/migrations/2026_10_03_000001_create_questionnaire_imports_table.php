<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('total_rows');        // total baris CSV
            $table->integer('valid_rows');        // baris yang berhasil diproses
            $table->json('averages');             // rata-rata per kriteria: {"C1":4.5,"C2":3.8,...}
            $table->json('weights');              // bobot ternormalisasi: {"C1":0.151,"C2":0.141,...}
            $table->float('total_weight', 8, 6); // harus mendekati 1.0
            $table->integer('respondent_count');  // jumlah responden valid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_imports');
    }
};
