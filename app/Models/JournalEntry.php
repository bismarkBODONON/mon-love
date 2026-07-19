<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'content',
        'mood',
        'photo',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('entry_date', 'desc')->orderBy('id', 'desc');
    }
}