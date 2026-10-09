<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Маршруты для ученика
Route::middleware(['auth'])->group(function () {
    Route::get('/student', [StudentController::class, 'index'])->name('student.index');
    Route::get('/student/next', [StudentController::class, 'next'])->name('student.next');
    Route::post('/student/answer/{task}', [StudentController::class, 'answer'])->name('student.answer');
    Route::get('/student/result/{answer}', [StudentController::class, 'result'])->name('student.result');
    Route::get('/student/progress', [StudentController::class, 'progress'])->name('student.progress');
});

// Маршруты для преподавателя
Route::middleware(['auth'])->prefix('teacher')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('teacher.index');
    Route::get('/create-task', [TeacherController::class, 'createTask'])->name('teacher.create-task');
    Route::post('/store-task', [TeacherController::class, 'storeTask'])->name('teacher.store-task');
    Route::get('/check/{answer}', [TeacherController::class, 'check'])->name('teacher.check');
    Route::post('/check/{answer}', [TeacherController::class, 'saveCheck'])->name('teacher.save-check');
    Route::get('/students', [TeacherController::class, 'students'])->name('teacher.students');
    Route::get('/student/{user}', [TeacherController::class, 'studentDetail'])->name('teacher.student-detail');
    Route::post('/mark-expert/{answer}', [TeacherController::class, 'markExpert'])->name('teacher.mark-expert');
});

// Маршруты для администратора
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
});