@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
    <h1>Админ-панель</h1>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Пользователи</h5>
                    <p>{{ $users->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Задания</h5>
                    <p>{{ $tasks->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Комментарии</h5>
                    <p>{{ $comments->count() }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection