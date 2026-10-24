<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'system_name',
        'is_hidden',
        'image_path',
    ];
}
