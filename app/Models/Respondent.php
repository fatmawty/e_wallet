<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Respondent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'institution',
        'major',
        'cohort_year',
        'general_feedback',
        'consented_at',
    ];

    protected $casts = [
        'cohort_year' => 'integer',
        'consented_at' => 'datetime',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'respondent_id');
    }

    public function criterionImportanceRatings(): HasMany
    {
        return $this->hasMany(CriterionImportanceRating::class, 'respondent_id');
    }
}
