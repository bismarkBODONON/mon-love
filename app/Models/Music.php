<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    protected $table = 'musics';

    protected $fillable = [
        'title', 'artist', 'cover_path', 'audio_path',
        'lyrics', 'duration', 'is_favorite', 'position',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];
}