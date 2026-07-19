<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeCapsule;
use Illuminate\Http\Request;

class TimeCapsuleController extends Controller
{
    public function index()
    {
        return TimeCapsule::ordered()->get()->map(function (TimeCapsule $capsule) {
            $isLocked = now()->startOfDay()->lt($capsule->open_date);

            return [
                'id'        => $capsule->id,
                'title'     => $capsule->title,
                'open_date' => $capsule->open_date->format('Y-m-d'),
                'is_locked' => $isLocked,
                // Le contenu ne part jamais côté client tant que la capsule est verrouillée
                'content'   => $isLocked ? null : $capsule->content,
            ];
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'open_date' => 'required|date|after:today',
        ]);

        $capsule = TimeCapsule::create($validated);

        return response()->json($capsule, 201);
    }

    public function destroy(TimeCapsule $timeCapsule)
    {
        $timeCapsule->delete();

        return response()->json(null, 204);
    }
}