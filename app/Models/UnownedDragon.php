<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnownedDragon extends Model
{
    protected $fillable = [
        'dragon_id',
    ];

    public function dragon(): BelongsTo
    {
        return $this->belongsTo(Dragon::class);
    }
}
