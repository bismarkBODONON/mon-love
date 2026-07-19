<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\CalendarEvent;
use App\Models\Dream;
use App\Models\GalleryItem;
use App\Models\JournalEntry;
use App\Models\LoveLetter;
use App\Models\NotificationSchedule;
use App\Models\Quiz;
use App\Models\Quote;
use App\Models\Reason;
use App\Models\Relationship;
use App\Models\TimeCapsule;
use App\Models\TimelineEvent;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    // Adapte les noms de modèles ci-dessus à ceux réellement présents dans ton app
    protected function tables(): array
    {
        return [
            'relationship'          => Relationship::class,
            'timeline_events'       => TimelineEvent::class,
            'journal_entries'       => JournalEntry::class,
            'love_letters'          => LoveLetter::class,
            'reasons'               => Reason::class,
            'dreams'                => Dream::class,
            'quotes'                => Quote::class,
            'albums'                => Album::class,
            'gallery_items'         => GalleryItem::class,
            'calendar_events'       => CalendarEvent::class,
            'notification_schedules' => NotificationSchedule::class,
            'time_capsules'         => TimeCapsule::class,
            'quizzes'               => Quiz::class,
            'user_settings'         => UserSetting::class,
        ];
    }

    public function export()
    {
        $data = [
            'exported_at' => now()->toIso8601String(),
            'version'     => 1,
            'data'        => [],
        ];

        foreach ($this->tables() as $key => $modelClass) {
            $data['data'][$key] = $modelClass::all();
        }

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="notre-histoire-backup-' . now()->format('Y-m-d') . '.json"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'backup' => 'required|file|mimes:json,txt',
        ]);

        $content = json_decode(file_get_contents($request->file('backup')->getRealPath()), true);

        if (!isset($content['data']) || !isset($content['version'])) {
            return response()->json(['message' => 'Fichier de sauvegarde invalide.'], 422);
        }

        DB::transaction(function () use ($content) {
            foreach ($this->tables() as $key => $modelClass) {
                if (!isset($content['data'][$key])) continue;

                // On vide puis on réinsère (restauration complète)
                $modelClass::query()->delete();

                foreach ($content['data'][$key] as $row) {
                    unset($row['id']);
                    $modelClass::create($row);
                }
            }
        });

        return response()->json(['message' => 'Restauration terminée.']);
    }
}