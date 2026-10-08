<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Task;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $answers = Answer::where('status', 'pending')->with('user', 'task')->get();
        return view('teacher.index', compact('answers'));
    }

    public function createTask()
    {
        return view('teacher.create-task');
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|integer',
            'difficulty' => 'required|integer',
        ]);

        Task::create($request->all());
        return redirect()->route('teacher.index');
    }

    public function check(Answer $answer)
    {
        return view('teacher.check', compact('answer'));
    }

    public function saveCheck(Request $request, Answer $answer)
    {
        $answer->score = $request->score;
        $answer->status = 'checked';
        $answer->save();

        return redirect()->route('teacher.index');
    }
}
