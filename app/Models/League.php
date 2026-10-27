<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    protected $fillable = [
        'name',
        'system_name',
        'started_at',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
