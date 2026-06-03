@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <div class="register">
        <h2 class="register__title">会員登録</h2>

        <form class="register__form" method="POST"action="{{ route('register') }}">
            @csrf

            {{--  ユーザー名 --}}
            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" autofocus>

                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- メールアドレス --}}
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="text" name="email" id="email" value="{{ old('email') }}" >

                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- パスワード --}}
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" >

                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- 確認用パスワード --}}
            <div class="form-group">
                <label for="password_confirmation">確認用パスワード</label>
                <input type="password" name="password_confirmation" id="password_confirmation" >
            </div>

            {{-- 登録ボタン --}}
            <button class="btn__submit" type="submit">登録する</button>

            <div class="login__link">
                <a href="{{ route('login') }}">ログインはこちら</a>
            </div>
        </form>

    </div>
@endsection
