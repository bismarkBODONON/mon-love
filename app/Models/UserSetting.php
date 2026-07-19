<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'first_name',
        'theme_mode',
        'color_theme',
        'font_family',
        'wallpaper',
        'preset_messages',
        'music_enabled',
        'notifications_enabled',
    ];

    protected $casts = [
        'preset_messages'        => 'array',
        'music_enabled'          => 'boolean',
        'notifications_enabled' => 'boolean',
    ];
}