<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryItemController extends Controller
{
    public function store(Request $request, Album $album)
    {
        $validated = $request->validate([
            'photos.*' => 'nullable|image|max:10240',
            'videos.*' => 'nullable|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        $created = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('gallery', 'public');
                $created[] = $album->galleryItems()->create([
                    'type' => 'photo',
                    'path' => $path,
                ]);
            }
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $path = $video->store('gallery/videos', 'public');
                $created[] = $album->galleryItems()->create([
                    'type' => 'video',
                    'path' => $path,
                ]);
            }
        }

        return response()->json($created, 201);
    }

    public function update(Request $request, GalleryItem $galleryItem)
    {
        $validated = $request->validate([
            'caption'     => 'nullable|string|max:255',
            'is_favorite' => 'sometimes|boolean',
        ]);

        $galleryItem->update($validated);

        return response()->json($galleryItem);
    }

    public function destroy(GalleryItem $galleryItem)
    {
        Storage::disk('public')->delete($galleryItem->path);
        $galleryItem->delete();

        return response()->json(null, 204);
    }
}