<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'is_favorite',
        'photos',
        'videos',
        'voice_notes',
        'sort_order',
    ];

    protected $casts = [
        'event_date'  => 'date',
        'is_favorite' => 'boolean',
        'photos'      => 'array', // Laravel sérialise/désérialise le JSON automatiquement
        'videos'      => 'array',
        'voice_notes' => 'array',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('event_date', 'asc')->orderBy('sort_order');
    }
}
