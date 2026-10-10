<?php

namespace App\Http\Controllers;

use App\Models\Respondent;
use Illuminate\View\View;

class RespondentController extends Controller
{
    public function index(): View
    {
        return view('respondents.index', [
            'respondents' => Respondent::latest()
                ->paginate(20),
        ]);
    }
}