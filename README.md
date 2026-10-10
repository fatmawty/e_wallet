# Sistem Pendukung Keputusan (SPK) Pemilihan E-Wallet Terbaik - Metode SAW

Aplikasi **Sistem Pendukung Keputusan (SPK) Pemilihan E-Wallet Terbaik** berbasis web yang dibangun menggunakan **Laravel Framework, PHP, dan Database SQLite / MySQL**. Aplikasi ini dirancang untuk tugas UAS Design dan berfungsi sebagai sistem pendukung keputusan yang benar-benar siap digunakan dengan pemrosesan algoritma matematis **Simple Additive Weighting (SAW)** secara dinamis.

---

## 📌 1. Tujuan Aplikasi
Aplikasi ini membantu pengguna menentukan alternatif e-wallet terbaik berdasarkan kriteria dan bobot penilaian yang ditentukan secara transparan dan terstruktur.

Alternatif E-Wallet yang dievaluasi:
1. **DANA** (A1)
2. **OVO** (A2)
3. **ShopeePay** (A3)
4. **GoPay** (A4)

---

## 🛠️ 2. Teknologi yang Digunakan
- **Framework Core**: Laravel 12.x (PHP 8.2+)
- **Database**: SQLite / MySQL (Eloquent ORM)
- **Frontend & UI/UX**: Blade Templating, HTML5, Vanilla CSS3, Bootstrap 5.3
- **Icons**: Bootstrap Icons (CDN)
- **Visualization**: Chart.js (Interactive Bar Charts)
- **Architecture**: Model-View-Controller (MVC) & Service Pattern (`SawCalculatorService`)

---

## 📐 3. Konsep Metodologi Simple Additive Weighting (SAW)

Metode SAW (penjumlahan terbobot) dilakukan dengan langkah-langhat matematis berikut:

### Kriteria Penilaian Default:
- **C1: Keamanan** (Benefit)
- **C2: Kemudahan Penggunaan** (Benefit)
- **C3: Promo dan Cashback** (Benefit)
- **C4: Keterjangkauan Biaya Transaksi** (Benefit)
- **C5: Kelengkapan Fitur** (Benefit)
- **C6: Kemudahan Top Up** (Benefit)
- **C7: Merchant/Layanan** (Benefit)

Responden mengisi tingkat kepentingan setiap kriteria pada skala 1–5. Sistem menghitung rata-rata tingkat kepentingan per kriteria, lalu menormalisasikannya menjadi bobot dengan total 1. Bobot tidak diinput atau dinilai oleh admin.

### Formula SAW:
1. **Bobot Kriteria ($W_j$)**: Mengambil rata-rata skor kepentingan responden per kriteria, kemudian membagi rata-rata setiap kriteria dengan jumlah rata-rata semua kriteria.
2. **Matriks Keputusan ($X_{ij}$)**: Mengambil rata-rata skor responden untuk setiap alternatif dan kriteria (Skala Likert 1–5). Tidak ada skor alternatif yang diinput admin.
3. **Normalisasi ($R_{ij}$)**:
   - **Kriteria Benefit**: $r_{ij} = \frac{x_{ij}}{\max(x_{ij})}$
   - **Kriteria Cost**: $r_{ij} = \frac{\min(x_{ij})}{x_{ij}}$
4. **Nilai Preferensi ($V_i$)**:
   $$V_i = \sum_{j=1}^n (w_j \times r_{ij})$$
5. **Perangkingan**: Mengurutkan alternatif berdasarkan nilai $V_i$ tertinggi ke terendah secara otomatis.

---

## 🗄️ 4. Struktur Database & Model

1. **`criteria`** (`Criterion.php`)
   - `id`, `code` (C1..C7), `name`, `type` (`benefit`/`cost`), `weight` (decimal 0.00-1.00), `description`
2. **`alternatives`** (`Alternative.php`)
   - `id`, `code` (A1..A4), `name`, `description`, `brand_color`, `logo_icon`, `status`
3. **`ratings`** (`Rating.php`)
   - `respondent_id`, `e_wallet_id`, `criterion_id`, `score` (1–5)
4. **`criterion_importance_ratings`** (`CriterionImportanceRating.php`)
   - `respondent_id`, `criterion_id`, `score` (1–5); menjadi sumber bobot kriteria.
5. **`calculation_results`** (`CalculationResult.php`)
   - `id`, `alternative_id`, `final_score`, `rank`, `details`

---

## 🚀 5. Cara Penginstalan & Menjalankan Aplikasi

### Syarat Perangkat Lunak:
- PHP >= 8.2
- Composer
- Node.js & NPM (Opsional jika ingin rebuild Vite)

### Langkah Instalasi:

1. **Clone / Buka Folder Project**:
   ```bash
   cd c:\xampp\htdocs\e_wallet
   ```

2. **Install Dependensi Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   File `.env` sudah dikonfigurasi secara default menggunakan SQLite database.
   *(Jika ingin menggunakan MySQL, atur `DB_CONNECTION=mysql`, `DB_DATABASE=spk_ewallet`, `DB_USERNAME=root`, `DB_PASSWORD=`).*

4. **Generate Key Aplikasi**:
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migration & Seeder**:
   Command ini akan membuat skema tabel dan mengisi data awal (kriteria dan alternatif):
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Jalankan Server Lokal Laravel**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di: `http://127.0.0.1:8000`

---

## 🗺️ 6. Alur Penggunaan Aplikasi (User Flow)

1. **Dashboard (`/`)**:
   - Menampilkan ringkasan statistik, rekomendasi e-wallet terbaik saat ini, grafik batang Chart.js, dan tabel pemeringkatan.
2. **Kelola Kriteria (`/kriteria`)**:
   - Menambah/mengubah/menghapus kriteria; bobot ditentukan otomatis dari jawaban responden.
3. **Kelola Alternatif (`/alternatif`)**:
   - Mengelola data e-wallet (DANA, OVO, ShopeePay, GoPay) beserta warna brand & deskripsi.
4. **Kuesioner (`/kuesioner`)**:
   - Mahasiswa aktif mengisi program studi, institusi, angkatan, persetujuan partisipasi, tingkat kepentingan seluruh kriteria, serta penilaian setiap e-wallet. Nama, NIM, nomor telepon, dan email tidak diminta. Hanya respons lengkap untuk kedua bagian yang digunakan dalam pembobotan dan matriks keputusan SAW.
5. **Proses SAW (`/proses-saw`)**:
   - Menampilkan seluruh tahapan matematis SAW (Matriks Keputusan, Normalisasi, Preferensi, dan Perangkingan) secara transparan.
6. **Hasil Ranking (`/hasil-ranking`)**:
   - Menampilkan podium pemenang, rekomendasi e-wallet terbaik (#1), dan tombol untuk melihat rincian kalkulasi per kriteria.
7. **Tentang Sistem (`/tentang`)**:
   - Berisi informasi teknis aplikasi, metodologi SAW, dan panduan langkah kerja sistem.

---
*Dibuat untuk Tugas UAS Design SPK - Sistem Pendukung Keputusan Pemilihan E-Wallet.*
