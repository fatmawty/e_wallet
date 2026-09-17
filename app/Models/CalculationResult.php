<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'alternative_id',
        'final_score',
        'rank',
        'details',
    ];

    protected $casts = [
        'final_score' => 'float',
        'rank' => 'integer',
        'details' => 'array',
    ];

    public function alternative()
    {
        return $this->belongsTo(Alternative::class, 'alternative_id');
    }
}
