<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('color_hex')->nullable();
            $table->string('badge_text')->nullable();
            $table->timestamps();
        });

        $now = now();
        $alternatives = DB::table('alternatives')->where('status', 'active')->orderBy('code')->get();
        foreach ($alternatives as $alternative) {
            DB::table('e_wallets')->insert([
                'name' => $alternative->name,
                'slug' => strtolower(preg_replace('/[^a-z0-9]+/i', '-', $alternative->name)),
                'description' => $alternative->description,
                'color_hex' => $alternative->brand_color,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('respondent_id')->constrained('respondents')->cascadeOnDelete();
            $table->foreignId('e_wallet_id')->constrained('e_wallets')->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained('criteria')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->timestamps();
            $table->unique(['respondent_id', 'e_wallet_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('e_wallets');
    }
};