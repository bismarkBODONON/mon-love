<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSchedule extends Model
{
    protected $fillable = [
        'message',
        'send_time',
        'frequency',
        'days_of_week',
        'send_date',
        'is_active',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'send_date'    => 'date',
        'is_active'    => 'boolean',
    ];
}