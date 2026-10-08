<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Comment;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $tasks = Task::all();
        $comments = Comment::all();

        return view('admin.index', compact('users', 'tasks', 'comments'));
    }
}
