<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class QuestionnaireImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_csv_and_store_original_file(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        $csvContent = "Timestamp,Nama,Usia,Status,E-wallet yang sering digunakan,Seberapa penting keamanan akun dan transaksi dalam memilih e-wallet?,Seberapa penting kemudahan penggunaan aplikasi dalam memilih e-wallet?,Seberapa penting promo dan cashback dalam memilih e-wallet?,Seberapa penting rendahnya biaya transaksi dalam memilih e-wallet?,Seberapa penting kelengkapan fitur dalam memilih e-wallet?,Seberapa penting kemudahan melakukan top up dalam memilih e-wallet?,Seberapa penting banyaknya merchant atau layanan yang menerima e-wallet dalam memilih e-wallet?\n";
        $csvContent .= "2024-01-01 08:00:00,Responden 1,20,Mahasiswa,GoPay,sangat penting,sangat penting,penting,cukup penting,sangat penting,sangat penting,sangat penting\n";
        $csvContent .= "2024-01-01 08:05:00,Responden 2,21,Mahasiswa,OVO,penting,sangat penting,penting,tidak penting,penting,penting,penting\n";

        $uploadedFile = UploadedFile::fake()->createWithContent(
            'sample_kuesioner.csv',
            $csvContent
        );

        $response = $this->actingAs($admin)->post('/import-kuesioner', [
            'csv_file' => $uploadedFile,
        ]);

        $response->assertRedirect(route('import.index'));
        $this->assertDatabaseHas('questionnaire_imports', [
            'filename' => 'sample_kuesioner.csv',
        ]);
        $this->assertDatabaseHas('questionnaire_imports', [
            'file_path' => 'uploads/sample_kuesioner.csv',
        ]);
        $this->assertFileExists(public_path('uploads/sample_kuesioner.csv'));
    }
}
