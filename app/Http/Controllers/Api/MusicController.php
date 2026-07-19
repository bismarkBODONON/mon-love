<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Music;
use Illuminate\Http\Request;
use getID3;

class MusicController extends Controller
{
    public function index(Request $request)
    {
        $query = Music::query()->orderBy('position')->orderBy('created_at');

        if ($request->boolean('favorites_only')) {
            $query->where('is_favorite', true);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('artist', 'like', "%{$request->search}%");
            });
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'lyrics' => 'nullable|string',
            'cover' => 'nullable|image|max:5120',
            'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp4,audio/x-wav,audio/wav,audio/x-m4a,audio/aac,video/mp4|max:20480',
        ]);

        $data = [
            'title' => $validated['title'],
            'artist' => $validated['artist'] ?? null,
            'lyrics' => $validated['lyrics'] ?? null,
        ];

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('music/covers', 'public');
        }

        $data['audio_path'] = $request->file('audio')->store('music/tracks', 'public');

        $music = Music::create($data);

        return $music;
    }

    public function update(Request $request, Music $music)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'artist' => 'nullable|string|max:255',
            'lyrics' => 'nullable|string',
            'is_favorite' => 'sometimes|boolean',
            'position' => 'sometimes|integer',
        ]);

        $music->update($validated);

        return $music;
    }

    public function destroy(Music $music)
    {
        $music->delete();

        return response()->noContent();
    }
}