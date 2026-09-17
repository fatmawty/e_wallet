<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativeScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'alternative_id',
        'criteria_id',
        'score',
    ];

    public function alternative()
    {
        return $this->belongsTo(Alternative::class, 'alternative_id');
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criteria_id');
    }
}
