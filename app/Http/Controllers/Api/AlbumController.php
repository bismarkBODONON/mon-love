<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function index()
    {
        // On charge juste 1 item par album (le plus ancien) pour servir
        // de couverture, plutôt que de charger tout le contenu de chaque
        // album ici — ça reste léger pour l'écran de liste.
        return Album::withCount('galleryItems')
            ->with(['galleryItems' => function ($query) {
                $query->orderBy('created_at')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show(Album $album)
    {
        $album->load(['galleryItems' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return response()->json($album);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $album = Album::create($validated);

        return response()->json($album, 201);
    }

    public function update(Request $request, Album $album)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $album->update($validated);

        return response()->json($album);
    }

    public function destroy(Album $album)
    {
        foreach ($album->galleryItems as $item) {
            Storage::disk('public')->delete($item->path);
        }

        $album->delete(); // Les gallery_items liés sont supprimés en cascade (voir migration).

        return response()->json(null, 204);
    }
}