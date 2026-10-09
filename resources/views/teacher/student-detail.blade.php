@extends('layouts.app')

@section('title', 'Прогресс ученика')

@section('content')
    <div class="container">
        <h1>Прогресс: {{ $user->name }}</h1>

        <a href="{{ route('teacher.students') }}" class="btn btn-secondary mb-3">← К списку учеников</a>

        {{-- Рекомендации по темам --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Слабые темы (рекомендуется повторить)</strong></div>
                    <div class="card-body">
                        @if(empty($weakTopics))
                            <p class="text-muted">Слабых тем пока нет.</p>
                        @else
                            <ul>
                                @foreach($weakTopics as $topic => $count)
                                    <li>{{ $topic }} — {{ $count }} раз(а)</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Частые ошибки</strong></div>
                    <div class="card-body">
                        @if(empty($allErrors))
                            <p class="text-muted">Ошибок пока нет.</p>
                        @else
                            <ul>
                                @foreach($allErrors as $type => $count)
                                    <li>{{ $type }} — {{ $count }} раз(а)</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- История ответов --}}
        <h3 class="mt-4">История ответов</h3>

        @if($answers->isEmpty())
            <p>Ответов пока нет.</p>
        @else
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Задание</th>
                        <th>Тип</th>
                        <th>Оценка</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($answers as $answer)
                        <tr>
                            <td>{{ $answer->task->title ?? '—' }}</td>
                            <td>{{ $answer->task->type ?? '—' }}</td>
                            <td>{{ $answer->score ?? '—' }}</td>
                            <td>{{ $answer->status }}</td>
                            <td>{{ $answer->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('teacher.check', $answer->id) }}" class="btn btn-sm btn-primary">
                                    Открыть
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection