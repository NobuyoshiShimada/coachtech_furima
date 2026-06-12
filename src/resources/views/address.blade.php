@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('header')
    <form action="/" method="GET" class="header__search">
        <input type="text" name="keyword"class="header__search--input" placeholder="なにをお探しですか？" value="{{ request("keyword") }}">
    </form>
    <ul class="header__nav">
        {{-- ログアウトボタン --}}
        <li class="header__nav-item">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" class="header__nav--logout">ログアウト</button>
            </form>
        </li>
        {{-- マイページボタン --}}
        <li class="header__nav-item"><a href="{{ route('mypage') }}" class="header__nav-mypage">マイページ</a></li>
        {{-- 出品ボタン --}}
        <li class="header__nav-item"><a href="{{ route('sell.create') }}" class="header__nav-listing">出品</a></li>
    </ul>
@endsection

@section('content')
    <div class="profile">
        <h2 class="profile__title">住所の変更</h2>

        <form action="{{ route('address.update', ['item' => $item->id]) }}" method="post" class="profile__form" >
            @csrf

            {{-- 郵便番号 --}}
            <div class="form-group__address">
                <label for="postcode" class="form-group__label">郵便番号</label>
                <input type="text" name="postcode" id="postcode" class="form-group__input"
                    value="{{ old('postcode', Auth::user()->profile->postcode ?? '') }}">
                @error('postcode')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror

            </div>

            {{-- 住所 --}}
            <div class="form-group__address">
                <label for="address" class="form-group__label">住所</label>
                <input type="text" name="address" id="address" class="form-group__input"
                    value="{{ old('address', Auth::user()->profile->address ?? '') }}">
                @error('address')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 建物名 --}}
            <div class="form-group__address">
                <label for="building" class="form-group__label">建物名</label>
                <input type="text" name="building" id="building" class="form-group__input"
                    value="{{ old('building', Auth::user()->profile->building ?? '') }}">
                @error('building')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="profile__submit-button__address">更新する</button>

        </form>
    </div>
@endsection
