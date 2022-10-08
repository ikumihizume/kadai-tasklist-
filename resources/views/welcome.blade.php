@extends('layouts.app')

@section('content')
    @if (Auth::check())
        <div class="col-sm-8">
                {{-- 投稿一覧 --}}
                @include('tasks.index')
        </div>
    @else
        <div class="center jumbotron">
            <div class="text-center">
                <h1>Welcome to the tasklists</h1>
                {{-- ユーザ登録ページへのリンク --}}
                {!! link_to_route('signup.get', 'Sign up now!', [], ['class' => 'btn btn-lg btn-primary']) !!}
            </div>
        </div>
    @endif
@endsection

