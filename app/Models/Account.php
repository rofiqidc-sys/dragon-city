<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrbOwning;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'fb_mail',
        'gmail',
        'ms_mail',
        'account_status',
    ];

    public function dragonOwnings(): HasMany
    {
        return $this->hasMany(DragonOwning::class);
    }

    public function dragonOwningDetails(): HasMany
    {
        return $this->hasMany(DragonOwningDetail::class);
    }

    public function orbOwnings(): HasMany
    {
        return $this->hasMany(OrbOwning::class);
    }
}
