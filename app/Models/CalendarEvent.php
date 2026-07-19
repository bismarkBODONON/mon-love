<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'type',
        'is_recurring_yearly',
        'notify',
        'notify_time',
    ];

    protected $casts = [
        'event_date'           => 'date',
        'is_recurring_yearly'  => 'boolean',
        'notify'               => 'boolean',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('event_date', 'asc');
    }
}