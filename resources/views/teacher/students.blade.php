@extends('layouts.app')

@section('title', 'Прогресс учеников')

@section('content')
    <div class="container">
        <h1>Прогресс учеников</h1>

        <a href="{{ route('teacher.index') }}" class="btn btn-secondary mb-3">← Назад к панели</a>

        @if($students->isEmpty())
            <p>Учеников пока нет.</p>
        @else
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Ученик</th>
                        <th>Email</th>
                        <th>Ответов</th>
                        <th>Средний балл</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->answers_count }}</td>
                            <td>{{ $student->avg_score }}</td>
                            <td>
                                <a href="{{ route('teacher.student-detail', $student->id) }}" class="btn btn-sm btn-primary">
                                    Подробнее
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection