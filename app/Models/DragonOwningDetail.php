<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\UnownedDragonSynchronizer;

class DragonOwningDetail extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(fn (self $detail) => UnownedDragonSynchronizer::sync($detail->dragon_id));
        static::deleted(fn (self $detail) => UnownedDragonSynchronizer::sync($detail->dragon_id));
    }

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
