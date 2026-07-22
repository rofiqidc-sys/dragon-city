<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrbOwning extends Model
{
    use HasFactory;

    protected $fillable = [
        'dragon_id',
        'account_id',
        'jumlah_orb',
    ];

    protected $casts = [
        'jumlah_orb' => 'integer',
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
