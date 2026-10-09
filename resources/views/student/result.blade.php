@extends('layouts.app')

@section('title', 'Результат')

@section('content')
    <div class="container">
        <h1>Результат</h1>

        <div class="card mt-4">
            <div class="card-body">
                <p><strong>Задание:</strong> {{ $answer->task->title ?? 'Задание не найдено' }}</p>
                <p><strong>Оценка:</strong> {{ $answer->score ?? 'На проверке' }}</p>
                <p><strong>Произношение:</strong> {{ $answer->pronunciation_score ?? '—' }}</p>
                <p><strong>Грамматика:</strong> {{ $answer->grammar_score ?? '—' }}</p>
                <p><strong>Формат ЕГЭ:</strong> {{ $answer->format_score ?? '—' }}</p>
                <p><strong>Статус:</strong> {{ $answer->status }}</p>

                <hr>

                <p><strong>Транскрипция:</strong></p>
                <p>{{ $answer->transcription ?? '—' }}</p>
            </div>
        </div>

        <a href="{{ route('student.index') }}" class="btn btn-primary mt-4">Следующее задание</a>
    </div>
@endsection