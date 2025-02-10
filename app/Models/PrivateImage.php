<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateImage extends Model
{
    protected $fillable = [
        'user_id',
        'public_image_id',
        'is_mine',
    ];
    protected $attributes = [
        'is_mine' => false
    ];

    public function privateTags(): HasMany
    {
        return $this->hasMany(PrivateTag::class, 'image_id', 'id');
    }

    public function publicImage(): BelongsTo
    {
        return $this->belongsTo(PublicImage::class);
    }
}
