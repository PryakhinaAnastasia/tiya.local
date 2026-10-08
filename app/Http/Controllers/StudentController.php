<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Answer;
use App\Models\Progress;
use App\Services\AdaptiveService;
use App\Services\AudioService;
use App\Services\SpeechRecognitionService;
use App\Services\GrammarCheckService;
use App\Services\PronunciationService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request, AdaptiveService $adaptive)
    {
        $type = $request->input('type'); // 1, 2, 3, 4 или null
        $currentStep = (int) $request->input('step', 1); // по умолчанию 1

        if ($type) {
            $task = Task::where('type', $type)->inRandomOrder()->first();
        } else {
            // Если "Все" — идём по порядку: 1 → 2 → 3 → 4
            if ($currentStep > 4) {
                $currentStep = 1;
            }
            $task = Task::where('type', $currentStep)->inRandomOrder()->first();
        }

        return view('student.index', compact('task', 'type', 'currentStep'));
    }

    public function next(Request $request)
    {
        $type = $request->input('type');
        $currentStep = (int) $request->input('step', 1);

        if ($type) {
            // Конкретный тип — рандом
            $task = Task::where('type', $type)->inRandomOrder()->first();
        } else {
            // "Все" — идём по порядку
            if ($currentStep > 4) {
                $currentStep = 1;
            }
            $task = Task::where('type', $currentStep)->inRandomOrder()->first();
        }

        return view('student.index', compact('task', 'type', 'currentStep'));
    }

    public function answer(
        Request $request,
        Task $task,
        AudioService $audioService,
        SpeechRecognitionService $speechService,
        GrammarCheckService $grammarService,
        PronunciationService $pronunciationService
    ) {
        $request->validate(['audio' => 'required|file|mimes:wav,mp3']);

        $audioPath = $audioService->saveAudio($request->file('audio'));
        $cleanPath = $audioService->applyNoiseReduction($audioPath);
        $transcription = $speechService->recognize($cleanPath);
        $grammarResult = $grammarService->check($transcription);
        $grammarScore = $grammarService->calculateScore($grammarResult);
        $pronunciationResult = $pronunciationService->evaluate($cleanPath);
        $pronunciationScore = $pronunciationResult['score'] ?? 0;

        $answer = new Answer();
        $answer->user_id = auth()->id();
        $answer->task_id = $task->id;
        $answer->audio_file = $cleanPath;
        $answer->transcription = $transcription;
        $answer->grammar_score = $grammarScore;
        $answer->pronunciation_score = $pronunciationScore;
        $answer->grammar_errors = json_encode($grammarResult['matches'] ?? []);
        $answer->status = 'pending';
        $answer->save();

        Progress::create([
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'score' => ($grammarScore + $pronunciationScore) / 2,
        ]);

        return redirect()->route('student.result', $answer->id);
    }

    public function result(Answer $answer)
    {
        return view('student.result', compact('answer'));
    }

    public function progress()
    {
        $progress = auth()->user()->progress()->with('task')->get();
        return view('student.progress', compact('progress'));
    }
}
