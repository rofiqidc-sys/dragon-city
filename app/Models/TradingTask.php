<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TradingTask extends Model
{
    use HasFactory;

    public const STATUSES = ['on Going','recalling', 'ready', 'done'];

    protected $fillable = [
        'dragon_id',
        'trader_id',
        'reciever_id',
        'jumlah_orb',
        'status_trade',
    ];

    protected $casts = [
        'jumlah_orb' => 'integer',
    ];

    public function dragon(): BelongsTo
    {
        return $this->belongsTo(Dragon::class);
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'trader_id');
    }

    public function reciever(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'reciever_id');
    }
}