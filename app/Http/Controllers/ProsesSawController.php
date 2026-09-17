<?php

namespace App\Http\Controllers;

use App\Services\SawCalculatorService;
use Illuminate\Http\Request;

class ProsesSawController extends Controller
{
    protected SawCalculatorService $sawService;

    public function __construct(SawCalculatorService $sawService)
    {
        $this->sawService = $sawService;
    }

    public function index()
    {
        $sawData = $this->sawService->calculate();

        return view('proses_saw.index', compact('sawData'));
    }

    public function process()
    {
        $sawData = $this->sawService->calculate();

        return redirect()->route('proses-saw.index')->with('success', 'Perhitungan metode Simple Additive Weighting (SAW) berhasil dijalankan secara dinamis dari database!');
    }
}
