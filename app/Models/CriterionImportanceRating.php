<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriterionImportanceRating extends Model
{
    protected $fillable = [
        'respondent_id',
        'criterion_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Respondent::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }
}
