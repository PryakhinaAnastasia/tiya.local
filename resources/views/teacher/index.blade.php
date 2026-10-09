@extends('layouts.app')

@section('title', 'Панель преподавателя')

@section('content')
    <div class="container">
        <h1>Панель преподавателя</h1>

        {{-- Статистика --}}
        <div class="row mt-4 mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">На проверке</h5>
                        <p class="card-text display-6">{{ $totalPending }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Проверено</h5>
                        <p class="card-text display-6">{{ $totalChecked }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Сложные случаи</h5>
                        <p class="card-text display-6">{{ $totalExpert }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Кнопки --}}
        <div class="mb-3">
            <a href="{{ route('teacher.students') }}" class="btn btn-info">Прогресс учеников</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h3>Ответы на проверку</h3>

        @if($answers->isEmpty())
            <p>Нет ответов, ожидающих проверки.</p>
        @else
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Ученик</th>
                        <th>Задание</th>
                        <th>Тип</th>
                        <th>Транскрипция</th>
                        <th>Дата</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($answers as $answer)
                        <tr>
                            <td>{{ $answer->user->name ?? '—' }}</td>
                            <td>{{ $answer->task->title ?? 'Задание не найдено' }}</td>
                            <td>{{ $answer->task->type ?? '—' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($answer->transcription ?? '—', 80) }}</td>
                            <td>{{ $answer->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('teacher.check', $answer->id) }}" class="btn btn-sm btn-primary">
                                    Проверить
                                </a>
                                <form action="{{ route('teacher.mark-expert', $answer->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Сложный</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection