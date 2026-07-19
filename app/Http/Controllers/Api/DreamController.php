<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DreamController extends Controller
{
    public function index()
    {
        return Dream::ordered()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:50',
            'target_date' => 'nullable|date',
            'image'       => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('dreams', 'public');
        }

        $dream = Dream::create($validated);

        return response()->json($dream, 201);
    }

    public function update(Request $request, Dream $dream)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:50',
            'target_date' => 'nullable|date',
            'is_achieved' => 'sometimes|boolean',
        ]);

        $dream->update($validated);

        return response()->json($dream);
    }

    public function destroy(Dream $dream)
    {
        if ($dream->image) {
            Storage::disk('public')->delete($dream->image);
        }

        $dream->delete();

        return response()->json(null, 204);
    }
}