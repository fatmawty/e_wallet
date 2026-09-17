<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'brand_color',
        'logo_icon',
        'status',
    ];

    public function scores()
    {
        return $this->hasMany(AlternativeScore::class, 'alternative_id');
    }

    public function calculationResult()
    {
        return $this->hasOne(CalculationResult::class, 'alternative_id');
    }
}
