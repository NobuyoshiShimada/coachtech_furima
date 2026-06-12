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
            <button type="button" class="email-auth__button">認証はこちらから</button>
        </div>

        <form class="resend-form" method="POST" action="{{ route('email.send') }}">
            @csrf
            <button type="submit" class="resend-link__button">認証メールを再送する</button>
        </form>
    </div>
@endsection
