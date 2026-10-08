@extends('layouts.app')

@section('title', 'Прогресс')

@section('content')
    <div class="container">
        <h1>Мой прогресс</h1>

        @if($progress->isEmpty())
            <p>У вас пока нет прогресса. Выполните хотя бы одно задание.</p>
        @else
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Задание</th>
                        <th>Оценка</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($progress as $item)
                        <tr>
                            <td>{{ $item->task->title ?? '—' }}</td>
                            <td>{{ $item->score }}</td>
                            <td>{{ $item->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('student.index') }}" class="btn btn-primary mt-3">К заданию</a>
    </div>
@endsection