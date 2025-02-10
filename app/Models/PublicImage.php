<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicImage extends Model
{
    protected $fillable = [
        'url',
        'view_count',
    ];
    protected $attributes = [
        'view_count' => 0,
    ];

    public function privateImages(): HasMany
    {
        return $this->hasMany(PrivateImage::class);
    }

    public function publicTags(): HasMany
    {
        return $this->hasMany(PublicTag::class, 'image_id');
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(Transition::class, 'source_image_id');
    }
}
