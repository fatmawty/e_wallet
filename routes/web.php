<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\SawProcessController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\EWalletController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RespondentController;
use App\Http\Controllers\PublicController;

// Authentication
Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'create'])->name('login');
	Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
	->middleware('auth')
	->name('logout');

// Public respondent experience: no login required.
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/beranda', [PublicController::class, 'home'])->name('public.home');
Route::get('/kuesioner', [QuestionnaireController::class, 'create'])->name('kuesioner.create');
Route::post('/kuesioner', [QuestionnaireController::class, 'store'])->name('kuesioner.store');
Route::get('/ranking', [PublicController::class, 'result'])->name('ranking.index');
Route::get('/ewallet/{slug}', [EWalletController::class, 'show'])->name('ewallet.show');

// Admin-only SPK management and analysis pages
Route::middleware(['auth', 'role:admin'])->group(function () {
	Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');

	// Data Responden
	Route::get('/responden', [RespondentController::class, 'index'])->name('respondents.index');
	Route::get('/responden/export-validitas', [RespondentController::class, 'exportCsv'])->name('respondents.export');
	Route::get('/responden/{respondent}/edit', [RespondentController::class, 'edit'])->name('respondents.edit');
	Route::put('/responden/{respondent}', [RespondentController::class, 'update'])->name('respondents.update');
	Route::delete('/responden/{respondent}', [RespondentController::class, 'destroy'])->name('respondents.destroy');
	Route::get('/responden/{respondent}', [RespondentController::class, 'show'])->name('respondents.show');

	// Kriteria CRUD & Bobot
	Route::get('/kriteria', [CriterionController::class, 'index'])->name('criteria.index');
	Route::post('/kriteria', [CriterionController::class, 'store'])->name('criteria.store');
	Route::put('/kriteria/{id}', [CriterionController::class, 'update'])->name('criteria.update');
	Route::delete('/kriteria/{id}', [CriterionController::class, 'destroy'])->name('criteria.destroy');

	// Alternatif E-Wallet CRUD
	Route::get('/alternatif', [AlternativeController::class, 'index'])->name('alternatives.index');
	Route::post('/alternatif', [AlternativeController::class, 'store'])->name('alternatives.store');
	Route::put('/alternatif/{id}', [AlternativeController::class, 'update'])->name('alternatives.update');
	Route::delete('/alternatif/{id}', [AlternativeController::class, 'destroy'])->name('alternatives.destroy');

	// Process SAW (Stepper 7 Langkah)
	Route::get('/proses-saw', [SawProcessController::class, 'index'])->name('saw.process');
	Route::post('/proses-saw/calculate', [SawProcessController::class, 'calculate'])->name('saw.calculate');
	Route::get('/analisis', [AnalyticsController::class, 'index'])->name('analytics.index');

	// Tentang Sistem SPK
	Route::get('/tentang', [AboutController::class, 'index'])->name('about');

});
