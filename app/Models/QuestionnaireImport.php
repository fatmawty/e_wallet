<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireImport extends Model
{
    use HasFactory;

    protected $table = 'questionnaire_imports';

    protected $fillable = [
        'filename',
        'total_rows',
        'valid_rows',
        'averages',
        'weights',
        'total_weight',
        'respondent_count',
    ];

    protected $casts = [
        'averages'         => 'array',
        'weights'          => 'array',
        'total_weight'     => 'float',
        'respondent_count' => 'integer',
        'total_rows'       => 'integer',
        'valid_rows'       => 'integer',
    ];
}
