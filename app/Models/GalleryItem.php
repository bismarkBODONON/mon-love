<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'album_id',
        'type',
        'path',
        'caption',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}