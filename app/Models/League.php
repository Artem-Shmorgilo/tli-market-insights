<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'name',
        'system_name',
        'started_at',
    ];
}
