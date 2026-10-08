@extends('layouts.app')

@section('title', 'Создать задание')

@section('content')
    <h1>Создать задание</h1>

    <form action="{{ route('teacher.store-task') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Тип (1–4)</label>
            <input type="number" name="type" class="form-control" min="1" max="4" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Сложность (1–5)</label>
            <input type="number" name="difficulty" class="form-control" min="1" max="5" required>
        </div>
        <button type="submit" class="btn btn-primary">Создать</button>
    </form>

    <a href="{{ route('teacher.index') }}" class="btn btn-secondary mt-4">Назад</a>
@endsection