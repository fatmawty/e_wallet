<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\QuestionnaireImport;
use App\Services\SawCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportKuesionerController extends Controller
{
    /**
     * Definisi 7 Kriteria, nama resmi, jenis, dan variasi pertanyaan Google Form.
     */
    protected array $criteriaDefinitions = [
        'C1' => [
            'name' => 'Keamanan',
            'type' => 'benefit',
            'questions' => [
                'Seberapa penting keamanan akun dan transaksi dalam memilih e-wallet?',
                'keamanan akun dan transaksi',
                'keamanan akun',
                'keamanan',
            ]
        ],
        'C2' => [
            'name' => 'Kemudahan Penggunaan',
            'type' => 'benefit',
            'questions' => [
                'Seberapa penting kemudahan penggunaan aplikasi dalam memilih e-wallet?',
                'kemudahan penggunaan aplikasi',
                'kemudahan penggunaan',
                'kemudahan pengguna',
            ]
        ],
        'C3' => [
            'name' => 'Promo dan Cashback',
            'type' => 'benefit',
            'questions' => [
                'Seberapa penting promo dan cashback dalam memilih e-wallet?',
                'promo dan cashback',
                'promo & cashback',
                'promo',
            ]
        ],
        'C4' => [
            'name' => 'Biaya Transaksi',
            'type' => 'cost',
            'questions' => [
                'Seberapa penting rendahnya biaya transaksi dalam memilih e-wallet?',
                'rendahnya biaya transaksi',
                'biaya transaksi',
                'biaya admin',
            ]
        ],
        'C5' => [
            'name' => 'Kelengkapan Fitur',
            'type' => 'benefit',
            'questions' => [
                'Seberapa penting kelengkapan fitur dalam memilih e-wallet?',
                'kelengkapan fitur',
                'fitur layanan',
                'fitur',
            ]
        ],
        'C6' => [
            'name' => 'Kemudahan Top Up',
            'type' => 'benefit',
            'questions' => [
                'Seberapa penting kemudahan melakukan top up dalam memilih e-wallet?',
                'kemudahan melakukan top up',
                'kemudahan top up',
                'top up',
            ]
        ],
        'C7' => [
            'name' => 'Merchant/Layanan',
            'type' => 'benefit',
            'questions' => [
                'Seberapa penting banyaknya merchant atau layanan yang menerima e-wallet dalam memilih e-wallet?',
                'banyaknya merchant atau layanan',
                'merchant atau layanan',
                'merchant/layanan',
                'merchant',
            ]
        ],
    ];

    /**
     * Konversi jawaban teks / angka ke Skala Likert (1 - 5 atau 1 - 4).
     */
    protected array $scoreScaleMap = [
        'sangat tidak penting' => 1,
        'tidak penting'        => 1,
        'kurang penting'       => 2,
        'cukup penting'        => 2,
        'penting'              => 3,
        'sangat penting'       => 4,
        '1'                    => 1,
        '2'                    => 2,
        '3'                    => 3,
        '4'                    => 4,
        '5'                    => 5,
    ];

    /**
     * Tampilkan halaman Import Kuesioner.
     */
    public function index()
    {
        $latestImport = QuestionnaireImport::latest()->first();
        $criteria     = Criterion::orderBy('code')->get();

        return view('import-kuesioner.index', [
            'latestImport' => $latestImport,
            'criteria'     => $criteria,
        ]);
    }

    /**
     * Proses upload, parsing CSV, pembobotan, dan kalkulasi ulang SAW.
     */
    public function store(Request $request, SawCalculatorService $sawService)
    {
        // ── 1. Validasi File Upload ─────────────────────────────────────────
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csv_file.required' => 'File CSV wajib dipilih.',
            'csv_file.mimes'    => 'Format file harus berupa CSV (.csv).',
            'csv_file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $file     = $request->file('csv_file');
        $filename = $file->getClientOriginalName();
        $sourcePath = tempnam(sys_get_temp_dir(), 'csv_');

        if ($sourcePath === false || file_put_contents($sourcePath, $file->getContent()) === false) {
            return back()->withErrors(['csv_file' => 'File CSV yang diunggah tidak dapat dibaca. Silakan coba lagi.']);
        }

        $uploadDirectory = public_path('uploads');
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0775, true);
        }

        $destinationFile = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($destinationFile)) {
            unlink($destinationFile);
        }

        $file->move($uploadDirectory, $filename);
        $filePath = 'uploads/' . $filename;

        // ── 2. Baca Header CSV ─────────────────────────────────────────────
        $handle = fopen($sourcePath, 'r');
        $rawHeaders = fgetcsv($handle);

        if (!$rawHeaders) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'File CSV kosong atau tidak memiliki header.']);
        }

        // Clean UTF-8 BOM and whitespace
        $headers = array_map(function ($h) {
            $cleaned = str_replace("\xEF\xBB\xBF", '', $h);
            return trim($cleaned);
        }, $rawHeaders);

        // ── 3. Deteksi Kolom C1 - C7 & Profile Column Headers ──────────────
        $criteriaColumnIndex = [];

        foreach ($this->criteriaDefinitions as $code => $def) {
            foreach ($headers as $colIndex => $headerText) {
                $headerClean = strtolower(trim(preg_replace('/\s+/', ' ', $headerText)));

                foreach ($def['questions'] as $qCandidate) {
                    $qClean = strtolower(trim(preg_replace('/\s+/', ' ', $qCandidate)));

                    if (stripos($headerClean, $qClean) !== false) {
                        $criteriaColumnIndex[$code] = $colIndex;
                        break 2; // Matched this criterion, proceed to next
                    }
                }
            }
        }

        // Dynamic detection for Respondent profile columns
        $nameColIdx   = null;
        $statusColIdx = null;
        $walletColIdx = null;
        $ageColIdx    = null;

        foreach ($headers as $colIdx => $hText) {
            $hClean = strtolower(trim($hText));
            if ($nameColIdx === null && (str_contains($hClean, 'nama') || str_contains($hClean, 'name'))) {
                $nameColIdx = $colIdx;
            }
            if ($ageColIdx === null && (str_contains($hClean, 'usia') || str_contains($hClean, 'umur') || str_contains($hClean, 'age'))) {
                $ageColIdx = $colIdx;
            }
            if ($statusColIdx === null && (str_contains($hClean, 'status') || str_contains($hClean, 'pekerjaan') || str_contains($hClean, 'profesi'))) {
                $statusColIdx = $colIdx;
            }
            if ($walletColIdx === null && (str_contains($hClean, 'e-wallet') || str_contains($hClean, 'dompet') || str_contains($hClean, 'sering digunakan'))) {
                $walletColIdx = $colIdx;
            }
        }

        // Cek jika ada kriteria yang tidak ditemukan
        $missingCodes = array_diff(array_keys($this->criteriaDefinitions), array_keys($criteriaColumnIndex));

        if (!empty($missingCodes)) {
            fclose($handle);
            $missingNames = array_map(fn($c) => $c . ' (' . $this->criteriaDefinitions[$c]['name'] . ')', $missingCodes);
            return back()->withErrors([
                'csv_file' => 'Kolom kriteria berikut tidak dapat ditemukan pada header CSV: '
                    . implode(', ', $missingNames)
                    . '. Pastikan header CSV memuat pertanyaan kriteria yang sesuai.',
            ]);
        }

        // ── 4. Baca dan Validasi Data Responden ───────────────────────────
        $sums               = array_fill_keys(array_keys($this->criteriaDefinitions), 0.0);
        $totalRows          = 0;
        $validRows          = 0;
        $invalidRows        = 0;
        $invalidLineNumbers = [];
        $validRespondentRecords = [];
        $lineNum            = 1; // Line 1 is the header row

        while (($row = fgetcsv($handle)) !== false) {
            $lineNum++;

            // Cek jika baris kosong / null
            if (empty(array_filter($row, fn($val) => trim($val) !== ''))) {
                continue;
            }

            // Cek apakah baris ini merupakan Header Pengulangan (repeated header)
            $isHeaderRepeat = false;
            foreach ($criteriaColumnIndex as $code => $colIdx) {
                $val = isset($row[$colIdx]) ? strtolower(trim($row[$colIdx])) : '';
                if (stripos($val, 'seberapa penting') !== false || (stripos($val, 'keamanan') !== false && stripos($val, 'transaksi') !== false)) {
                    $isHeaderRepeat = true;
                    break;
                }
            }

            if ($isHeaderRepeat) {
                continue; // Skip repeated header row
            }

            $totalRows++;
            $rowValues = [];
            $rowValid  = true;

            foreach ($criteriaColumnIndex as $code => $colIdx) {
                $rawVal = isset($row[$colIdx]) ? strtolower(trim($row[$colIdx])) : '';

                if (isset($this->scoreScaleMap[$rawVal])) {
                    $rowValues[$code] = $this->scoreScaleMap[$rawVal];
                } else {
                    $rowValid = false;
                    break;
                }
            }

            if ($rowValid) {
                $validRows++;
                foreach ($rowValues as $code => $val) {
                    $sums[$code] += $val;
                }

                $extractedName = '';
                if ($nameColIdx !== null && isset($row[$nameColIdx]) && trim($row[$nameColIdx]) !== '') {
                    $extractedName = trim($row[$nameColIdx]);
                } elseif ($ageColIdx !== null && isset($row[$ageColIdx]) && trim($row[$ageColIdx]) !== '') {
                    $extractedName = "Responden #" . $validRows . " (Usia " . trim($row[$ageColIdx]) . ")";
                } else {
                    $extractedName = "Responden #" . $validRows;
                }

                $extractedStatus = '';
                if ($statusColIdx !== null && isset($row[$statusColIdx]) && trim($row[$statusColIdx]) !== '') {
                    $extractedStatus = trim($row[$statusColIdx]);
                } else {
                    $extractedStatus = 'Mahasiswa / Umum';
                }

                $extractedWallet = '';
                if ($walletColIdx !== null && isset($row[$walletColIdx]) && trim($row[$walletColIdx]) !== '') {
                    $extractedWallet = trim($row[$walletColIdx]);
                }

                $validRespondentRecords[] = [
                    'name'     => $extractedName,
                    'status'   => $extractedStatus,
                    'e_wallet' => $extractedWallet,
                ];
            } else {
                $invalidRows++;
                $invalidLineNumbers[] = $lineNum;
            }
        }

        fclose($handle);
        unlink($sourcePath);

        if ($validRows === 0) {
            return back()->withErrors([
                'csv_file' => "Tidak ada data responden yang valid dari total {$totalRows} baris. "
                    . "Pastikan jawaban kriteria menggunakan teks 'sangat penting', 'penting', 'cukup penting', atau 'tidak penting'.",
            ]);
        }

        // ── 5. Hitung Rata-rata dan Bobot Ternormalisasi (W) ───────────────
        $averages   = [];
        $totalAvg   = 0.0;

        foreach ($sums as $code => $sum) {
            $avg             = $sum / $validRows;
            $averages[$code] = round($avg, 6);
            $totalAvg       += $avg;
        }

        $weights = [];
        foreach ($averages as $code => $avg) {
            $weights[$code] = round($avg / $totalAvg, 6);
        }

        // Adjust rounding to make sum exact 1.0
        $totalWeight = round(array_sum($weights), 6);

        // ── 6. Simpan Ke Database (Criteria, QuestionnaireImport & Respondent) ────────
        DB::transaction(function () use ($filename, $totalRows, $validRows, $invalidRows, $averages, $weights, $totalWeight, $validRespondentRecords) {
            // Update / Create Questionnaire Import Record
            QuestionnaireImport::create([
                'filename'         => $filename,
                'total_rows'       => $totalRows,
                'valid_rows'       => $validRows,
                'averages'         => $averages,
                'weights'          => $weights,
                'total_weight'     => $totalWeight,
                'respondent_count' => $validRows,
            ]);

            // Save individual respondents to respondents table
            $wallets = \App\Models\EWallet::all();
            foreach ($validRespondentRecords as $respData) {
                $walletId = null;
                if (!empty($respData['e_wallet'])) {
                    $matchedWallet = $wallets->first(function ($w) use ($respData) {
                        return stripos($w->name, $respData['e_wallet']) !== false || stripos($respData['e_wallet'], $w->name) !== false;
                    });
                    if ($matchedWallet) {
                        $walletId = $matchedWallet->id;
                    }
                }

                \App\Models\Respondent::create([
                    'name'                => $respData['name'],
                    'status'              => $respData['status'],
                    'institution'         => 'Kuesioner Google Form',
                    'major'               => null,
                    'primary_e_wallet_id' => $walletId ?? $wallets->first()?->id,
                    'general_feedback'    => 'Di-import dari file CSV: ' . $filename,
                ]);
            }

            // Update Criteria Table with Official Names & Calculated Weights
            foreach ($this->criteriaDefinitions as $code => $def) {
                Criterion::updateOrCreate(
                    ['code' => $code],
                    [
                        'name'   => $def['name'],
                        'type'   => $def['type'],
                        'weight' => $weights[$code],
                    ]
                );
            }
        });

        // ── 7. Jalankan Ulang Perhitungan SAW ──────────────────────────────
        $sawService->calculate();

        return redirect()->route('import.index')->with('success_import', [
            'message'       => 'Import Data Kuesioner Berhasil!',
            'filename'      => $filename,
            'file_path'     => $filePath,
            'total_rows'    => $totalRows,
            'valid_rows'    => $validRows,
            'invalid_rows'  => $invalidRows,
            'invalid_lines' => $invalidLineNumbers,
            'weights'       => $weights,
            'averages'      => $averages,
        ]);
    }
}
