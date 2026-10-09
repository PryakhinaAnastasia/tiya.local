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
        $type = $request->input('type');
        $currentStep = (int) $request->input('step', 1);

        if ($type) {
            $task = Task::where('type', $type)->inRandomOrder()->first();
        } else {
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
            $task = Task::where('type', $type)->inRandomOrder()->first();
        } else {
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
        $request->validate(['audio' => 'required|file|max:102400']);

        // 1. Сохраняем аудио
        $audioPath = $audioService->saveAudio($request->file('audio'));

        // 2. Шумоподавление — НЕ применяем автоматически.
        //    Оставляем на будущее как опциональный модуль.
        $cleanPath = $audioPath;

        // 3. Распознавание речи через Whisper
        $transcription = $speechService->recognize($cleanPath);

        // 4. Проверка грамматики через LanguageTool
        $grammarResult = $grammarService->check($transcription);
        $grammarScore = $grammarService->calculateScore($grammarResult);

        // 5. Оценка произношения (fabiosuizu)
        $pronunciationResult = $pronunciationService->evaluate($cleanPath);
        $pronunciationScore = $pronunciationResult['score'] ?? 0;

        // 6. Сохраняем ответ
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

        // 7. Прогресс
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
