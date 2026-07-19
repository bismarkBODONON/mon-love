<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationSchedule;
use Illuminate\Http\Request;

class NotificationScheduleController extends Controller
{
    public function index()
    {
        return NotificationSchedule::orderBy('send_time')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message'      => 'required|string|max:255',
            'send_time'    => 'required|date_format:H:i',
            'frequency'    => 'required|in:once,daily,weekly',
            'days_of_week' => 'nullable|array',
            'send_date'    => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        $schedule = NotificationSchedule::create($validated);

        return response()->json($schedule, 201);
    }

    public function update(Request $request, NotificationSchedule $notificationSchedule)
    {
        $validated = $request->validate([
            'message'      => 'sometimes|string|max:255',
            'send_time'    => 'sometimes|date_format:H:i',
            'frequency'    => 'sometimes|in:once,daily,weekly',
            'days_of_week' => 'nullable|array',
            'send_date'    => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        $notificationSchedule->update($validated);

        return response()->json($notificationSchedule);
    }

    public function destroy(NotificationSchedule $notificationSchedule)
    {
        $notificationSchedule->delete();

        return response()->json(null, 204);
    }
}