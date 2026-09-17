<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'respondent_id',
        'e_wallet_id',
        'criterion_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Respondent::class, 'respondent_id');
    }

    public function eWallet(): BelongsTo
    {
        return $this->belongsTo(EWallet::class, 'e_wallet_id');
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class, 'criterion_id');
    }
}
