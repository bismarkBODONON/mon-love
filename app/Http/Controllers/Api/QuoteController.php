<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        return Quote::orderBy('created_at', 'desc')->get();
    }

    // Même logique que pour "reasons" : une citation stable pour
    // toute la journée, basée sur la date du jour.
    public function daily()
    {
        $count = Quote::count();

        if ($count === 0) {
            return response()->json(null);
        }

        $seed = (int) date('Ymd');
        $index = $seed % $count;

        $quote = Quote::orderBy('id')->skip($index)->first();

        return response()->json($quote);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'author'  => 'nullable|string|max:255',
        ]);

        $quote = Quote::create($validated);

        return response()->json($quote, 201);
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'content'     => 'sometimes|string|max:1000',
            'author'      => 'nullable|string|max:255',
            'is_favorite' => 'sometimes|boolean',
        ]);

        $quote->update($validated);

        return response()->json($quote);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return response()->json(null, 204);
    }
}