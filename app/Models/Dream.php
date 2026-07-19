<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dream extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'target_date',
        'is_achieved',
        'image',
    ];

    protected $casts = [
        'target_date' => 'date',
        'is_achieved' => 'boolean',
    ];

    public function scopeOrdered($query)
    {
        // Les rêves non réalisés d'abord, triés par date cible la plus proche.
        return $query->orderBy('is_achieved', 'asc')->orderBy('target_date', 'asc');
    }
}