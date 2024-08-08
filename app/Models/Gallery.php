<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['name', 'images'];

    protected $casts = [
        'images' => 'array',
    ];
}

