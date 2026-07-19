<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoveLetter;
use Illuminate\Http\Request;

class LoveLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = LoveLetter::query()->latest('written_at');

        // Filtre optionnel par catégorie : ?category=anniversaire
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Recherche texte simple : ?search=bonjour
        if ($request->filled('search')) {
            $term = $request->query('search');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            });
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);

        $letter = LoveLetter::create($validated);

        return response()->json($letter, 201);
    }

    public function show(LoveLetter $loveLetter)
    {
        return response()->json($loveLetter);
    }

    public function update(Request $request, LoveLetter $loveLetter)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'content'     => 'sometimes|string',
            'category'    => 'nullable|string|max:50',
            'is_favorite' => 'boolean',
        ]);

        $loveLetter->update($validated);

        return response()->json($loveLetter);
    }

    public function destroy(LoveLetter $loveLetter)
    {
        $loveLetter->delete();

        return response()->json(null, 204);
    }
}
