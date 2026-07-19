<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    public function index()
    {
        return JournalEntry::ordered()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content'    => 'required|string',
            'mood'       => 'nullable|string|max:10',
            'entry_date' => 'required|date',
            'photo'      => 'nullable|image|max:10240', // 10 Mo max
        ]);

        $entry = JournalEntry::create($validated);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('journal', 'public');
            $entry->update(['photo' => $path]);
        }

        return response()->json($entry, 201);
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $validated = $request->validate([
            'content'    => 'sometimes|string',
            'mood'       => 'nullable|string|max:10',
            'entry_date' => 'sometimes|date',
        ]);

        $journalEntry->update($validated);

        return response()->json($journalEntry);
    }

    public function destroy(JournalEntry $journalEntry)
    {
        if ($journalEntry->photo) {
            Storage::disk('public')->delete($journalEntry->photo);
        }

        $journalEntry->delete();

        return response()->json(null, 204);
    }
}