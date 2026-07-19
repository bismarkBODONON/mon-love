<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function index()
    {
        return Reason::orderBy('created_at', 'desc')->get();
    }

    // Une raison aléatoire différente chaque jour, mais stable pour
    // toute la journée (le seed est basé sur la date, pas sur l'heure).
    // Ainsi, si l'utilisateur rouvre l'app 5 fois dans la journée,
    // il voit toujours la même raison du jour — pas une nouvelle à chaque fois.
    public function daily()
    {
        $count = Reason::count();

        if ($count === 0) {
            return response()->json(null);
        }

        $seed = (int) date('Ymd');
        $index = $seed % $count;

        $reason = Reason::orderBy('id')->skip($index)->first();

        return response()->json($reason);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $reason = Reason::create($validated);

        return response()->json($reason, 201);
    }

    public function update(Request $request, Reason $reason)
    {
        $validated = $request->validate([
            'content'     => 'sometimes|string|max:1000',
            'is_favorite' => 'sometimes|boolean',
        ]);

        $reason->update($validated);

        return response()->json($reason);
    }

    public function destroy(Reason $reason)
    {
        $reason->delete();

        return response()->json(null, 204);
    }
}