<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicTag extends Model
{
    protected $fillable = [
        'image_id',
        'name',
    ];
}
