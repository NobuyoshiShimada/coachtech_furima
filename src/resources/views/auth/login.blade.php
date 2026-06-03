
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <div class="login">
        <h2 class="login__title">ログイン</h2>

        <form class="login__form" method="POST" action="{{ route('login') }}">
            @csrf

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

            {{-- ログインボタン --}}
            <button class="btn__submit" type="submit">ログインする</button>

            <div class="register__link">
                <a href="{{ route('register') }}">会員登録はこちら</a>
            </div>
        </form>

    </div>
@endsection
