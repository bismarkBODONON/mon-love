<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function index()
    {
        return CalendarEvent::ordered()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'event_date'          => 'required|date',
            'type'                => 'required|in:anniversary,important_date,event',
            'is_recurring_yearly' => 'boolean',
            'notify'              => 'boolean',
            'notify_time'         => 'nullable|date_format:H:i',
        ]);

        $event = CalendarEvent::create($validated);

        return response()->json($event, 201);
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $validated = $request->validate([
            'title'               => 'sometimes|string|max:255',
            'description'         => 'nullable|string',
            'event_date'          => 'sometimes|date',
            'type'                => 'sometimes|in:anniversary,important_date,event',
            'is_recurring_yearly' => 'boolean',
            'notify'              => 'boolean',
            'notify_time'         => 'nullable|date_format:H:i',
        ]);

        $calendarEvent->update($validated);

        return response()->json($calendarEvent);
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->delete();

        return response()->json(null, 204);
    }
}