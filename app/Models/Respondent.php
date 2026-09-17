<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Respondent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'institution',
        'major',
        'primary_e_wallet_id',
        'general_feedback',
    ];

    public function primaryEWallet(): BelongsTo
    {
        return $this->belongsTo(EWallet::class, 'primary_e_wallet_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'respondent_id');
    }
}
