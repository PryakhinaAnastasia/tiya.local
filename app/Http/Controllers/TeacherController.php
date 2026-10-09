<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Task;
use App\Models\Comment;
use App\Models\User;
use App\Models\Progress;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Список ответов, ожидающих проверки.
     */
    public function index()
    {
        $answers = Answer::where('status', 'pending')
            ->with('user', 'task')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalChecked = Answer::where('status', 'checked')->count();
        $totalPending = $answers->count();
        $totalExpert = Answer::where('status', 'expert')->count();

        return view('teacher.index', compact('answers', 'totalChecked', 'totalPending', 'totalExpert'));
    }

    /**
     * Форма создания нового задания.
     */
    public function createTask()
    {
        return view('teacher.create-task');
    }

    /**
     * Сохранение нового задания.
     */
    public function storeTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|integer|min:1|max:4',
            'difficulty' => 'required|integer|min:1|max:5',
            'topic' => 'nullable|string|max:255',
        ]);

        Task::create($request->all());

        return redirect()->route('teacher.index')
            ->with('success', 'Задание успешно создано');
    }

    /**
     * Просмотр ответа для проверки.
     */
    public function check(Answer $answer)
    {
        $answer->load('user', 'task', 'comments.user');

        return view('teacher.check', compact('answer'));
    }

    /**
     * Сохранение проверки: оценка + комментарий.
     */
    public function saveCheck(Request $request, Answer $answer)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'comment' => 'nullable|string|max:2000',
        ]);

        $answer->score = $request->score;
        $answer->status = 'checked';
        $answer->save();

        if ($request->filled('comment')) {
            Comment::create([
                'answer_id' => $answer->id,
                'user_id' => auth()->id(),
                'text' => $request->comment,
            ]);
        }

        // Обновляем прогресс ученика
        Progress::updateOrCreate(
            ['user_id' => $answer->user_id, 'task_id' => $answer->task_id],
            ['score' => $request->score]
        );

        return redirect()->route('teacher.index')
            ->with('success', 'Проверка сохранена');
    }

    /**
     * Список учеников с прогрессом.
     */
    public function students()
    {
        $students = User::where('role', 'student')
            ->withCount('answers')
            ->get();

        // Для каждого ученика — средний балл
        $students->each(function ($student) {
            $avg = Progress::where('user_id', $student->id)->avg('score');
            $student->avg_score = $avg ? round($avg, 1) : 0;
        });

        return view('teacher.students', compact('students'));
    }

    /**
     * Детальный прогресс одного ученика.
     */
    public function studentDetail(User $user)
    {
        $answers = Answer::where('user_id', $user->id)
            ->with('task')
            ->orderBy('created_at', 'desc')
            ->get();

        // Рекомендации по темам: темы, где ученик получал низкие оценки
        $weakTopics = [];
        $progress = Progress::where('user_id', $user->id)->get();
        foreach ($progress as $item) {
            if ($item->score < 60) {
                $task = Task::find($item->task_id);
                if ($task && $task->topic) {
                    $weakTopics[$task->topic] = ($weakTopics[$task->topic] ?? 0) + 1;
                }
            }
        }
        arsort($weakTopics);

        // Частые ошибки
        $allErrors = [];
        foreach ($answers as $answer) {
            $errors = json_decode($answer->grammar_errors ?? '[]', true) ?? [];
            foreach ($errors as $error) {
                $key = $error['rule']['category']['name'] ?? 'Прочее';
                $allErrors[$key] = ($allErrors[$key] ?? 0) + 1;
            }
        }
        arsort($allErrors);

        return view('teacher.student-detail', compact('user', 'answers', 'weakTopics', 'allErrors'));
    }

    /**
     * Отметить ответ как сложный случай для экспертной валидации.
     */
    public function markExpert(Answer $answer)
    {
        $answer->status = 'expert';
        $answer->save();

        return redirect()->route('teacher.index')
            ->with('success', 'Ответ отмечен как сложный случай');
    }
}
