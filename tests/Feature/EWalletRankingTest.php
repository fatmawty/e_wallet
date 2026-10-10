<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Alternative;
use App\Models\EWallet;
use App\Models\Criterion;
use App\Models\CriterionImportanceRating;
use App\Models\Rating;
use App\Models\Respondent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\DatabaseSeeder;
use App\Services\SawCalculatorService;

class EWalletRankingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_homepage_displays_e_wallet_leaderboard(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('GoPay');
        $response->assertSee('ShopeePay');
        $response->assertSee('OVO');
        $response->assertSee('DANA');
        $response->assertSee('Cara mahasiswa memilih');
        $response->assertSee('Respons masuk');
        $response->assertSee('Target rencana');
        $response->assertSee('Metode SAW');
        $response->assertSee('Keamanan');
    }

    public function test_questionnaire_page_loads_successfully(): void
    {
        $response = $this->get('/kuesioner');
        $response->assertStatus(200);
        $response->assertSee('Kuesioner Evaluasi E-Wallet');
        $response->assertSee('Tingkat Kepentingan Kriteria');
        $response->assertSee('Kelengkapan kuesioner');
        $response->assertSee('Persetujuan penelitian wajib; ulasan tambahan opsional dan tidak dihitung.');
        $response->assertSee('aria-label="Kelengkapan kuesioner"', false);
        $response->assertSee('Angkatan');
        $response->assertSee('Program Studi');
        $response->assertSee('mahasiswa aktif');
        $response->assertSee('Nama Responden');
        $response->assertSee('Nama tidak ditampilkan pada website publik');
        $response->assertDontSee('name="student_confirmation"');
        $response->assertDontSee('Anda dapat berhenti sebelum mengirim kuesioner');
        $response->assertDontSee('primary_e_wallet_id');
        $response->assertDontSee('E-Wallet Utama Yang Paling Sering Anda Gunakan');
    }

    public function test_legacy_csv_import_feature_and_history_table_are_removed(): void
    {
        $this->assertFalse(Schema::hasTable('questionnaire_imports'));
        $this->get('/import-kuesioner')->assertNotFound();
        $this->post('/import-kuesioner')->assertNotFound();
    }

    public function test_initial_criteria_weights_are_equal_and_total_one(): void
    {
        $criteria = Criterion::orderBy('code')->get();

        $this->assertEquals(1.0, $criteria->sum('weight'));
        $this->assertEquals(0.1429, $criteria->firstWhere('code', 'C1')->weight);
        $this->assertEquals(0.1428, $criteria->firstWhere('code', 'C7')->weight);
    }

    public function test_submitting_questionnaire_creates_respondent_and_ratings(): void
    {
        $gopay = EWallet::where('slug', 'gopay')->first();
        $shopeepay = EWallet::where('slug', 'shopeepay')->first();
        $ovo = EWallet::where('slug', 'ovo')->first();
        $dana = EWallet::where('slug', 'dana')->first();

        $criteria = Criterion::all();

        $ratingsData = [];
        foreach ([$gopay, $shopeepay, $ovo, $dana] as $wallet) {
            foreach ($criteria as $crit) {
                $ratingsData[$wallet->id][$crit->id] = 5;
            }
        }

        $importanceData = [];
        foreach ($criteria as $criterion) {
            $importanceData[$criterion->id] = 3;
        }

        $postData = [
            'name' => 'Budi Penguji',
            'research_consent' => '1',
            'institution' => 'Universitas Indonesia',
            'major' => 'Sistem Informasi',
            'cohort_year' => now()->year - 2,
            'general_feedback' => 'ShopeePay sangat membantu untuk transaksi sehari-hari.',
            'importance' => $importanceData,
            'ratings' => $ratingsData,
        ];

        $response = $this->post('/kuesioner', $postData);
        $response->assertRedirect('/ranking');

        $this->assertDatabaseHas('respondents', [
            'name' => 'Budi Penguji',
            'status' => 'Mahasiswa Aktif',
            'institution' => 'Universitas Indonesia',
            'major' => 'Sistem Informasi',
            'cohort_year' => now()->year - 2,
        ]);
        $this->assertNotNull(Respondent::firstOrFail()->consented_at);
        $this->assertDatabaseCount('criterion_importance_ratings', $criteria->count());
        $this->assertFalse(Schema::hasColumn('respondents', 'primary_e_wallet_id'));

        $this->get('/ranking')
            ->assertOk()
            ->assertSee('Rekomendasi utama');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Budi Penguji');

        $this->actingAs(User::where('role', 'admin')->firstOrFail())
            ->get('/responden')
            ->assertOk()
            ->assertSee('Budi Penguji');

        $this->actingAs(User::where('role', 'admin')->firstOrFail())
            ->get('/proses-saw')
            ->assertOk()
            ->assertSee('Rata-rata penilaian 1 responden lengkap');
    }

    public function test_saw_uses_average_respondent_ratings_for_the_decision_matrix(): void
    {
        $criteria = Criterion::orderBy('code')->get();
        $primaryCriterion = $criteria->firstWhere('code', 'C1');

        $respondents = collect([
            ['name' => 'Responden Satu', 'status' => 'Mahasiswa', 'institution' => 'Kampus A'],
            ['name' => 'Responden Dua', 'status' => 'Mahasiswa', 'institution' => 'Kampus B'],
        ])->map(fn (array $data) => Respondent::create($data));

        $scoresByWallet = [
            'GoPay' => [3, 5],
            'OVO' => [2, 4],
            'ShopeePay' => [1, 3],
            'DANA' => [1, 1],
        ];
        $wallets = EWallet::all()->keyBy('name');

        foreach ($respondents as $respondentIndex => $respondent) {
            foreach ($criteria as $criterion) {
                CriterionImportanceRating::create([
                    'respondent_id' => $respondent->id,
                    'criterion_id' => $criterion->id,
                    'score' => $criterion->id === $primaryCriterion->id ? 5 : 1,
                ]);
            }

            foreach ($scoresByWallet as $walletName => $scores) {
                foreach ($criteria as $criterion) {
                    Rating::create([
                        'respondent_id' => $respondent->id,
                        'e_wallet_id' => $wallets[$walletName]->id,
                        'criterion_id' => $criterion->id,
                        'score' => $criterion->id === $primaryCriterion->id
                            ? $scores[$respondentIndex]
                            : 3,
                    ]);
                }
            }
        }

        $legacyRespondent = Respondent::create([
            'name' => 'Responden Lama',
            'status' => 'Mahasiswa',
            'institution' => 'Kampus Lama',
        ]);
        foreach ($wallets as $wallet) {
            foreach ($criteria as $criterion) {
                Rating::create([
                    'respondent_id' => $legacyRespondent->id,
                    'e_wallet_id' => $wallet->id,
                    'criterion_id' => $criterion->id,
                    'score' => 1,
                ]);
            }
        }

        $result = app(SawCalculatorService::class)->calculate();
        $gopay = Alternative::where('name', 'GoPay')->firstOrFail();
        $dana = Alternative::where('name', 'DANA')->firstOrFail();

        $this->assertTrue($result['has_data']);
        $this->assertSame(2, $result['respondent_count']);
        $this->assertSame(2, $result['weight_respondent_count']);
        $this->assertEqualsWithDelta(5 / 11, $result['criteria']->firstWhere('id', $primaryCriterion->id)->weight, 0.0000000001);
        $this->assertEqualsWithDelta(5 / 11, Criterion::findOrFail($primaryCriterion->id)->weight, 0.0000000001);
        $this->assertEquals(1.0, $result['total_weight']);
        $this->assertEquals(4.0, $result['matrix_x'][$gopay->id][$primaryCriterion->id]);
        $this->assertEquals(0.4545, $result['weighted_matrix'][$gopay->id][$primaryCriterion->id]);
        $this->assertEquals(1.0, $result['matrix_x'][$dana->id][$primaryCriterion->id]);
        $this->assertSame('GoPay', $result['best_alternative']->name);
        $this->assertFalse(Schema::hasTable('alternative_scores'));
    }

    public function test_saw_does_not_create_a_ranking_without_complete_respondent_ratings(): void
    {
        $result = app(SawCalculatorService::class)->calculate();

        $this->assertFalse($result['has_data']);
        $this->assertStringContainsString('belum ada responden dengan penilaian e-wallet', $result['message']);
        $this->assertDatabaseCount('calculation_results', 0);

        $this->get('/ranking')
            ->assertOk()
            ->assertSee($result['message']);

        $this->actingAs(User::where('role', 'admin')->firstOrFail())
            ->get('/proses-saw')
            ->assertOk()
            ->assertSee($result['message']);
    }

    public function test_saw_does_not_calculate_until_importance_responses_are_complete(): void
    {
        $respondent = Respondent::create([
            'name' => 'Responden Belum Lengkap',
            'status' => 'Mahasiswa',
            'institution' => 'Kampus',
        ]);
        $wallets = EWallet::all();
        $criteria = Criterion::all();

        foreach ($wallets as $wallet) {
            foreach ($criteria as $criterion) {
                Rating::create([
                    'respondent_id' => $respondent->id,
                    'e_wallet_id' => $wallet->id,
                    'criterion_id' => $criterion->id,
                    'score' => 4,
                ]);
            }
        }

        $result = app(SawCalculatorService::class)->calculate();

        $this->assertFalse($result['has_data']);
        $this->assertStringContainsString('tingkat kepentingan semua kriteria yang lengkap', $result['message']);
        $this->assertDatabaseCount('calculation_results', 0);
    }

    public function test_questionnaire_requires_importance_rating_for_every_criterion(): void
    {
        $criteria = Criterion::all();
        $wallets = EWallet::all();
        $ratingsData = [];

        foreach ($wallets as $wallet) {
            foreach ($criteria as $criterion) {
                $ratingsData[$wallet->id][$criterion->id] = 4;
            }
        }

        $this->post('/kuesioner', [
            'name' => 'Mahasiswa Uji',
            'research_consent' => '1',
            'institution' => 'Kampus',
            'major' => 'Informatika',
            'cohort_year' => now()->year - 1,
            'ratings' => $ratingsData,
        ])
            ->assertSessionHasErrors('importance');

        $this->assertDatabaseCount('respondents', 0);
        $this->assertDatabaseCount('criterion_importance_ratings', 0);
    }

    public function test_questionnaire_requires_research_consent(): void
    {
        $this->post('/kuesioner', [])
            ->assertSessionHasErrors(['name', 'research_consent', 'institution', 'major', 'cohort_year'])
            ->assertSessionDoesntHaveErrors(['student_confirmation']);

        $this->assertDatabaseCount('respondents', 0);
    }

    public function test_criteria_page_does_not_offer_manual_weight_editing(): void
    {
        $response = $this->actingAs(User::where('role', 'admin')->firstOrFail())
            ->get('/kriteria');

        $response->assertOk()
            ->assertSee('Bobot responden belum tersedia')
            ->assertDontSee('name="weight"');
    }

    public function test_about_page_explains_dynamic_weights_without_showing_placeholder_weights(): void
    {
        $this->actingAs(User::where('role', 'admin')->firstOrFail())
            ->get('/tentang')
            ->assertOk()
            ->assertSee('Hitung Bobot Kriteria')
            ->assertSee('Bobot dihitung setelah ada jawaban responden');
    }

    public function test_analytics_page_loads_successfully(): void
    {
        $response = $this->get('/analisis');
        $response->assertStatus(200);
        $response->assertSee('Matriks Analisis Komparatif');
    }

    public function test_ewallet_detail_page_loads_successfully(): void
    {
        $response = $this->get('/ewallet/gopay');
        $response->assertStatus(200);
        $response->assertSee('GoPay');
        $response->assertSee('Breakdown Penilaian Per Kriteria');
    }
}
