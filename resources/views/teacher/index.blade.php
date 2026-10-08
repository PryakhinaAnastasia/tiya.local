@extends('layouts.app')

@section('title', 'Панель преподавателя')

@section('content')
    <h1>Ответы на проверку</h1>

    <a href="{{ route('teacher.create-task') }}" class="btn btn-success mb-3">Создать задание</a>

    <table class="table">
        <thead>
            <tr>
                <th>Ученик</th>
                <th>Задание</th>
                <th>Статус</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            @foreach($answers as $answer)
                <tr>
                    <td>{{ $answer->user->name }}</td>
                    <td>{{ $answer->task->title }}</td>
                    <td>{{ $answer->status }}</td>
                    <td>
                        <a href="{{ route('teacher.check', $answer->id) }}" class="btn btn-sm btn-primary">Проверить</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection