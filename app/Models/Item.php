<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'category_id',
        'image_path',
    ];
}
