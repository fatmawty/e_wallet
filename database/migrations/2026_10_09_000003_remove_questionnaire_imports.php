<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('questionnaire_imports');
    }

    public function down(): void
    {
        Schema::create('questionnaire_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('total_rows');
            $table->integer('valid_rows');
            $table->json('averages');
            $table->json('weights');
            $table->float('total_weight', 8, 6);
            $table->integer('respondent_count');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }
};
