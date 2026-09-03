<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_name',
        'gem_reward',
        'dragon_reward_id',
        'achievement',
    ];

    protected $casts = [
        'achievement' => 'decimal:2',
    ];

    protected $appends = ['total_member'];

    public function dragons(): BelongsToMany
    {
        return $this->belongsToMany(Dragon::class, 'collection_dragon_members');
    }

    public function dragonReward(): BelongsTo
    {
        return $this->belongsTo(Dragon::class, 'dragon_reward_id');
    }

    public function getTotalMemberAttribute(): int
    {
        return $this->dragons()->count();
    }
}
