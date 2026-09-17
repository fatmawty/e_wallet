<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\EWallet;
use App\Models\Criterion;
use App\Models\Respondent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

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
    }

    public function test_questionnaire_page_loads_successfully(): void
    {
        $response = $this->get('/kuesioner');
        $response->assertStatus(200);
        $response->assertSee('Kuesioner Evaluasi E-Wallet');
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

        $postData = [
            'name' => 'Budi Penguji',
            'status' => 'Mahasiswa',
            'institution' => 'Universitas Indonesia',
            'major' => 'Sistem Informasi',
            'primary_e_wallet_id' => $shopeepay->id,
            'general_feedback' => 'ShopeePay sangat membantu untuk transaksi sehari-hari.',
            'ratings' => $ratingsData,
        ];

        $response = $this->post('/kuesioner', $postData);
        $response->assertRedirect('/');

        $this->assertDatabaseHas('respondents', [
            'name' => 'Budi Penguji',
            'institution' => 'Universitas Indonesia',
            'primary_e_wallet_id' => $shopeepay->id,
        ]);
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
