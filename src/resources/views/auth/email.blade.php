@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/email.css') }}">
@endsection

@section('content')
    <div class="email-auth">
        <p class="email-auth__text">登録していただいたメールアドレスに認証メールを送付しました。
            <br>メール認証を完了してください。
        </p>

        <div class="email-auth__action">
            <a href="http://localhost:8025" target="_blank" rel="noopener noreferrer" class="email-auth__button">認証はこちらから</a>
        </div>

        <form class="resend-form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="resend-link__button">認証メールを再送する</button>
        </form>
    </div>
@endsection
