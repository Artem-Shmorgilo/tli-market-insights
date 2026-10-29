<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'system_name',
        'is_hidden',
        'image_path',
        'display_order',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
