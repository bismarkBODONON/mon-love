<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserSettingController extends Controller
{
    public function show()
    {
        // App à usage unique/couple : une seule ligne de réglages
        return UserSetting::firstOrCreate(['id' => 1]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name'             => 'nullable|string|max:100',
            'theme_mode'             => 'sometimes|in:light,dark,system',
            'color_theme'            => 'sometimes|string|max:50',
            'font_family'            => 'sometimes|string|max:50',
            'preset_messages'        => 'nullable|array',
            'preset_messages.*'      => 'string|max:255',
            'music_enabled'          => 'boolean',
            'notifications_enabled' => 'boolean',
        ]);

        $settings = UserSetting::firstOrCreate(['id' => 1]);
        $settings->update($validated);

        return response()->json($settings);
    }

    public function uploadWallpaper(Request $request)
    {
        $request->validate([
            'wallpaper' => 'required|image|max:8192',
        ]);

        $path = $request->file('wallpaper')->store('wallpapers', 'public');

        $settings = UserSetting::firstOrCreate(['id' => 1]);

        // Supprime l'ancien fond d'écran s'il existe
        if ($settings->wallpaper) {
            Storage::disk('public')->delete($settings->wallpaper);
        }

        $settings->update(['wallpaper' => $path]);

        return response()->json([
            'wallpaper_url' => Storage::url($path),
        ]);
    }

    public function deleteWallpaper()
    {
        $settings = UserSetting::firstOrCreate(['id' => 1]);

        if ($settings->wallpaper) {
            Storage::disk('public')->delete($settings->wallpaper);
            $settings->update(['wallpaper' => null]);
        }

        return response()->json($settings);
    }
}