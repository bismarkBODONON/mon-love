<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function galleryItems()
    {
        return $this->hasMany(GalleryItem::class);
    }
}