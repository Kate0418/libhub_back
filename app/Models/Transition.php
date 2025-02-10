<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transition extends Model
{
    protected $fillable = [
        'source_image_id',
        'destination_image_id',
        'transition_count',
    ];

    public function publicImage(): BelongsTo
    {
        return $this->belongsTo(PublicImage::class, 'source_image_id', 'id');
    }
}
