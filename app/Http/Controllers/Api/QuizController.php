<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        return Quiz::latest()->get();
    }

    public function show(Quiz $quiz)
    {
        return $quiz;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'questions'                => 'required|array|min:1',
            'questions.*.question'     => 'required|string',
            'questions.*.choices'      => 'required|array|min:2',
            'questions.*.correct_index'=> 'required|integer|min:0',
        ]);

        $quiz = Quiz::create($validated);

        return response()->json($quiz, 201);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title'     => 'sometimes|string|max:255',
            'questions' => 'sometimes|array|min:1',
        ]);

        $quiz->update($validated);

        return response()->json($quiz);
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return response()->json(null, 204);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'nullable|integer',
        ]);

        $questions = $quiz->questions;
        $total = count($questions);
        $correct = 0;

        foreach ($questions as $index => $question) {
            if (($validated['answers'][$index] ?? null) === $question['correct_index']) {
                $correct++;
            }
        }

        return response()->json([
            'score'      => $correct,
            'total'      => $total,
            'percentage' => $total > 0 ? round(($correct / $total) * 100) : 0,
        ]);
    }
}