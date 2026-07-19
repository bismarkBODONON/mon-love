<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimelineEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TimelineController extends Controller
{
    public function index()
    {
        return TimelineEvent::ordered()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date'  => 'required|date',
            'location'    => 'nullable|string|max:255',
            'is_favorite' => 'boolean',
            'photos.*'    => 'nullable|image|max:10240', // 10 Mo max par photo
            'videos.*'    => 'nullable|mimes:mp4,mov,avi,webm|max:51200', // 50 Mo max par vidéo
        ]);

        $event = TimelineEvent::create($validated);

        $updates = [];

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                $paths[] = $photo->store('timeline', 'public');
            }
            $updates['photos'] = $paths;
        }

        if ($request->hasFile('videos')) {
            $videoPaths = [];
            foreach ($request->file('videos') as $video) {
                $videoPaths[] = $video->store('timeline/videos', 'public');
            }
            $updates['videos'] = $videoPaths;
        }

        if (!empty($updates)) {
            $event->update($updates);
        }

        return response()->json($event, 201);
    }

    public function update(Request $request, TimelineEvent $timelineEvent)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'event_date'  => 'sometimes|date',
            'location'    => 'nullable|string|max:255',
            'is_favorite' => 'boolean',
        ]);

        $timelineEvent->update($validated);

        return response()->json($timelineEvent);
    }

    public function destroy(TimelineEvent $timelineEvent)
    {
        foreach ((array) $timelineEvent->photos as $path) {
            Storage::disk('public')->delete($path);
        }

        foreach ((array) $timelineEvent->videos as $path) {
            Storage::disk('public')->delete($path);
        }

        $timelineEvent->delete();

        return response()->json(null, 204);
    }
}