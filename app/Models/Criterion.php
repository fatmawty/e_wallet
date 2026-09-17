<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'code',
        'name',
        'type',
        'weight',
        'description',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function alternativeScores()
    {
        return $this->hasMany(AlternativeScore::class, 'criteria_id');
    }
}
