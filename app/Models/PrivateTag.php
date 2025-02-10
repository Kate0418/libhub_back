<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateTag extends Model
{
    protected $fillable = [
        'image_id',
        'name',
    ];

    public function privateImage(): BelongsTo
    {
        return $this->belongsTo(PrivateImage::class, 'image_id');
    }
}
