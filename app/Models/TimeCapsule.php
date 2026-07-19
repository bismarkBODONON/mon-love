<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeCapsule extends Model
{
    protected $fillable = ['title', 'content', 'open_date'];

    protected $casts = [
        'open_date' => 'date',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('open_date', 'asc');
    }
}