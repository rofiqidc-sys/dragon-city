<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dragon extends Model
{
    use HasFactory;

    protected $fillable = [
        'dragon_book',
        'alias',
        'dragon_name',
        'rarity_id',
        'element_1_id',
        'element_2_id',
        'element_3_id',
        'element_4_id',
        'summon_time',
        'orb_to_summon',
        'hatching_time',
    ];

    protected $casts = [
        'summon_time' => 'integer',
        'orb_to_summon' => 'integer',
        'hatching_time' => 'integer',
    ];

    public function rarity(): BelongsTo
    {
        return $this->belongsTo(Rarity::class);
    }

    public function element1(): BelongsTo
    {
        return $this->belongsTo(Element::class, 'element_1_id');
    }

    public function element2(): BelongsTo
    {
        return $this->belongsTo(Element::class, 'element_2_id');
    }

    public function element3(): BelongsTo
    {
        return $this->belongsTo(Element::class, 'element_3_id');
    }

    public function element4(): BelongsTo
    {
        return $this->belongsTo(Element::class, 'element_4_id');
    }

    public function dragonOwningDetails(): HasMany
    {
        return $this->hasMany(DragonOwningDetail::class);
    }
}
