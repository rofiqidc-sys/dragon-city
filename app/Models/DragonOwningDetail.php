<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DragonOwningDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'dragon_id',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function dragon(): BelongsTo
    {
        return $this->belongsTo(Dragon::class);
    }
}
