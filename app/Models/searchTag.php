<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class searchTag extends Model
{
    protected $fillable = [
        'name',
        'use_count',
    ];

    protected $attributes = [
        'use_count' => 0,
    ];
}
