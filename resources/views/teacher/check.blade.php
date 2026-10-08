@extends('layouts.app')

@section('title', 'Проверка ответа')

@section('content')
    <h1>Проверка ответа</h1>

    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Ученик:</strong> {{ $answer->user->name }}</p>
            <p><strong>Задание:</strong> {{ $answer->task->title }}</p>
            <p><strong>Транскрипция:</strong> {{ $answer->transcription ?? '—' }}</p>

            <form action="{{ route('teacher.save-check', $answer->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Оценка</label>
                    <input type="number" name="score" class="form-control" min="0" max="100" required>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>

    <a href="{{ route('teacher.index') }}" class="btn btn-secondary mt-4">Назад</a>
@endsection