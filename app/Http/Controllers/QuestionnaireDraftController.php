<?php

namespace App\Http\Controllers;

use App\Services\QuestionnaireDraftService;
use Illuminate\Http\Request;

class QuestionnaireDraftController extends Controller
{
    public function store(Request $request, string $instrument, QuestionnaireDraftService $service)
    {
        abort_unless(in_array($instrument, ['DASS21', 'PHQ9', 'GAD7'], true), 404);
        $student = $request->user()->persona?->estudiante;
        abort_unless($student, 403);
        $data = $request->validate(['answers' => ['present', 'array', 'max:21'], 'draft_version' => ['required', 'integer', 'min:0']]);
        $draft = $service->save($student, $instrument, $data['answers'], (int) $data['draft_version']);

        return response()->json(['version' => $draft->version, 'saved_at' => $draft->updated_at->toIso8601String()]);
    }
}
