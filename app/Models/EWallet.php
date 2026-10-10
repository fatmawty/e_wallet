<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EWallet extends Model
{
    use HasFactory;

    protected $table = 'e_wallets';

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'color_hex',
        'badge_text',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'e_wallet_id');
    }

}
