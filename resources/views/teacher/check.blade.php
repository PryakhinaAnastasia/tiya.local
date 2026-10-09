@extends('layouts.app')

@section('title', 'Проверка ответа')

@section('content')
    <div class="container">
        <h1>Проверка ответа</h1>

        <div class="card mt-4">
            <div class="card-body">
                <p><strong>Ученик:</strong> {{ $answer->user->name ?? '—' }}</p>
                <p><strong>Задание:</strong> {{ $answer->task->title ?? 'Задание не найдено' }}</p>
                <p><strong>Тип:</strong> {{ $answer->task->type ?? '—' }}</p>
                <p><strong>Тема:</strong> {{ $answer->task->topic ?? '—' }}</p>

                <hr>

                @if($answer->audio_file)
                    <div class="mb-3">
                        <label class="form-label"><strong>Аудиоответ:</strong></label>
                        <audio controls class="w-100">
                            <source src="{{ Storage::url($answer->audio_file) }}" type="audio/mpeg">
                            Ваш браузер не поддерживает аудио.
                        </audio>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label"><strong>Транскрипция:</strong></label>
                    <div class="border rounded p-3 bg-light">
                        {{ $answer->transcription ?? 'Транскрипция отсутствует' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Грамматика:</strong> {{ $answer->grammar_score ?? '—' }}</div>
                    <div class="col-md-4"><strong>Произношение:</strong> {{ $answer->pronunciation_score ?? '—' }}</div>
                    <div class="col-md-4"><strong>Формат ЕГЭ:</strong> {{ $answer->format_score ?? '—' }}</div>
                </div>

                @if($answer->grammar_errors)
                    @php
                        $errors = json_decode($answer->grammar_errors, true) ?? [];
                    @endphp
                    @if(count($errors) > 0)
                        <div class="mb-3">
                            <label class="form-label"><strong>Найденные ошибки:</strong></label>
                            <ul class="list-group">
                                @foreach($errors as $error)
                                    <li class="list-group-item">
                                        {{ $error['message'] ?? 'Ошибка' }}
                                        @if(isset($error['context']))
                                            <br><small class="text-muted">{{ $error['context'] }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                {{-- Комментарии --}}
                @if($answer->comments->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label"><strong>Комментарии:</strong></label>
                        @foreach($answer->comments as $comment)
                            <div class="border rounded p-2 mb-2">
                                <small class="text-muted">{{ $comment->user->name ?? '—' }} ({{ $comment->created_at->format('d.m.Y H:i') }}):</small>
                                <p class="mb-0">{{ $comment->text }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <hr>

                <form action="{{ route('teacher.save-check', $answer->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="score" class="form-label"><strong>Оценка (0–100):</strong></label>
                        <input type="number" name="score" id="score" class="form-control" min="0" max="100"
                               value="{{ old('score', $answer->score) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label"><strong>Комментарий для ученика:</strong></label>
                        <textarea name="comment" id="comment" class="form-control" rows="4"
                                  placeholder="Что стоит улучшить...">{{ old('comment') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Сохранить проверку</button>
                </form>
            </div>
        </div>

        <a href="{{ route('teacher.index') }}" class="btn btn-secondary mt-4">Назад к списку</a>
    </div>
@endsection