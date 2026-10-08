@extends('layouts.app')

@section('title', 'Результат')

@section('content')
    <h1>Результат</h1>

    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Задание:</strong> {{ $answer->task->title }}</p>
            <p><strong>Оценка:</strong> {{ $answer->score ?? 'На проверке' }}</p>
            <p><strong>Произношение:</strong> {{ $answer->pronunciation_score ?? '—' }}</p>
            <p><strong>Грамматика:</strong> {{ $answer->grammar_score ?? '—' }}</p>
            <p><strong>Формат ЕГЭ:</strong> {{ $answer->format_score ?? '—' }}</p>
            <p><strong>Статус:</strong> {{ $answer->status }}</p>
        </div>
    </div>

    <a href="{{ route('student.index') }}" class="btn btn-primary mt-4">Следующее задание</a>
@endsection