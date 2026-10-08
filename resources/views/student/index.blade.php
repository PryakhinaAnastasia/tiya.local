@extends('layouts.app')

@section('title', 'Текущее задание')

@section('content')
    <div class="container">
        <h1>Текущее задание</h1>

        {{-- Переключатель типов --}}
        <div class="mb-4">
            <a href="{{ route('student.index') }}" class="btn btn-sm {{ !isset($type) || !$type ? 'btn-primary' : 'btn-outline-primary' }}">Все</a>
            <a href="{{ route('student.index', ['type' => 1]) }}" class="btn btn-sm {{ isset($type) && $type == 1 ? 'btn-primary' : 'btn-outline-primary' }}">Задание 1</a>
            <a href="{{ route('student.index', ['type' => 2]) }}" class="btn btn-sm {{ isset($type) && $type == 2 ? 'btn-primary' : 'btn-outline-primary' }}">Задание 2</a>
            <a href="{{ route('student.index', ['type' => 3]) }}" class="btn btn-sm {{ isset($type) && $type == 3 ? 'btn-primary' : 'btn-outline-primary' }}">Задание 3</a>
            <a href="{{ route('student.index', ['type' => 4]) }}" class="btn btn-sm {{ isset($type) && $type == 4 ? 'btn-primary' : 'btn-outline-primary' }}">Задание 4</a>
        </div>

        @if($task)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $task->title }}</h5>

                    {{-- Картинки --}}
                    @if($task->image_file)
                        <div class="mb-3">
                            <img src="{{ Storage::url($task->image_file) }}" alt="Задание" class="img-fluid">
                        </div>
                    @endif

                    @if($task->image_file_2)
                        <div class="mb-3">
                            <img src="{{ Storage::url($task->image_file_2) }}" alt="Задание 2" class="img-fluid">
                        </div>
                    @endif

                    {{-- Аудио --}}
                    @if($task->audio_file)
                        <div class="mb-3">
                            <audio controls>
                                <source src="{{ Storage::url($task->audio_file) }}" type="audio/mpeg">
                                Ваш браузер не поддерживает аудио.
                            </audio>
                        </div>
                    @endif

                    {{-- Описание --}}
                    <p class="card-text">{!! $task->description !!}</p>

                    <p><strong>Тип:</strong> {{ $task->type }}</p>
                    <p><strong>Сложность:</strong> {{ $task->difficulty }}</p>

                    {{-- Форма ответа --}}
                    <form action="{{ route('student.answer', $task->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="audio" class="form-label">Загрузите аудиоответ</label>
                            <input type="file" name="audio" id="audio" class="form-control" accept=".wav,.mp3" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
                </div>
            </div>

            {{-- Кнопки --}}
            @if(!$type)
                @if($currentStep < 4)
                    <a href="{{ route('student.next', ['step' => $currentStep + 1]) }}" class="btn btn-secondary mt-3">Следующее задание</a>
                @else
                    <a href="{{ route('student.index', ['step' => 1]) }}" class="btn btn-success mt-3">Начать заново</a>
                @endif
            @else
                <a href="{{ route('student.next', ['type' => $type]) }}" class="btn btn-secondary mt-3">Следующее задание</a>
            @endif
        @else
            <p>Задания пока нет.</p>
        @endif

        <a href="{{ route('student.progress') }}" class="btn btn-outline-secondary mt-3">Мой прогресс</a>
    </div>
@endsection