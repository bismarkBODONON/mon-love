<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoveLetter extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'is_favorite',
        'voice_note_path',
        'photos',
        'written_at',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'photos'      => 'array',
        'written_at'  => 'datetime',
    ];
}
