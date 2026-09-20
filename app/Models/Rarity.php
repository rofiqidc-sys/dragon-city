<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rarity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias',
        'key_need_to_summon',
        'orb_per_trade',
    ];

    protected $casts = [
        'orb_per_trade' => 'integer',
    ];
}
