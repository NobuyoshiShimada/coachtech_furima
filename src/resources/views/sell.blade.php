@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('header')
    <form action="/" method="GET" class="header__search">
        <input type="text" name="keyword"class="header__search--input" placeholder="なにをお探しですか？">
    </form>
    <ul class="header__nav">
        {{-- ログイン中 --}}
        @auth
            {{-- ログアウトボタン --}}
            <form action="{{ route('logout') }}" method="post" class="header__nav-item">
                @csrf
                <button type="submit" class="header__nav--logout">ログアウト</button>
            </form>
            {{-- マイページボタン --}}
            <li class="header__nav-item"><a href="{{ route('mypage') }}" class="header__nav-mypage">マイページ</a></li>
            {{-- 出品ボタン --}}
            <li class="header__nav-item"><a href="{{ route('sell.create') }}" class="header__nav-listing">出品</a></li>
        @endauth

        {{-- 未ログイン --}}
        @guest
            {{-- ログインボタン --}}
            <li class="header__nav-item"><a href="{{ route('login') }}" class="header__nav-mypage">ログイン</a></li>
            {{-- マイページボタン --}}
            <li class="header__nav-item"><a href="{{ route('login') }}" class="header__nav-mypage">マイページ</a></li>
            {{-- 出品ボタン --}}
            <li class="header__nav-item"><a href="{{ route('login') }}" class="header__nav-listing">出品</a></li>
        @endguest
    </ul>
@endsection

@section('content')
    {{-- profile.blade.phpを流用 --}}
    <div class="profile">
        <h2 class="profile__title">商品の出品</h2>

        <form action="{{ route('sell.store') }}" method="post" class="profile__form" enctype="multipart/form-data">
            @csrf

            {{-- 商品画像 --}}
            <div class="form-group">
                <label class="form-group__label">商品画像</label>
                <div class="image-upload-box">
                    <input type="file" name="image" id="item-image" class="image-upload-box__input" accept="image/*"
                        required>
                    <label for="item-image" class="item-image-box__button">画像を選択する</label>
                </div>
                @error('image')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            <h3 class="sell-title">商品の詳細</h3>

            {{-- カテゴリー選択 --}}
            <div class="form-group">
                <label class="form-group__label">カテゴリー</label>
                <div class="category-grid">
                    @foreach ($categories as $category)
                        <label class="category-item">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                class="category-item__checkbox"
                                {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'checked' : '' }}>
                            <span class="category-item__badge">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('categories')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 商品の状態 --}}
            <div class="form-group">
                <label for="condition_id" class="form-group__label">商品の状態</label>
                <select name="condition_id" id="condition_id" class="form-group__input" required>
                    <option value="" disabled selected>選択してください</option>
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition->id }}"
                            {{ old('condition_id') == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                    @endforeach
                </select>
                @error('condition_id')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            <h3 class="sell-title">商品名と説明</h3>

            {{-- 商品名 --}}
            <div class="form-group">
                <label for="name" class="form-group__label">商品名</label>
                <input type="text" name="name" id="name" class="form-group__input" value="{{ old('name') }}"
                    required>
                @error('name')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- ブランド名 nullable --}}
            <div class="form-group">
                <label for="brand" class="form-group__label">ブランド名</label>
                <input type="text" name="brand" id="brand" class="form-group__input" value="{{ old('brand') }}">
            </div>

            {{-- 商品の説明 --}}
            <div class="form-group">
                <label for="description" class="form-group__label">商品の説明</label>
                <textarea name="description" id="description" class="form-group__input" rows="6" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 販売価格 --}}
            <div class="form-group">
                <label for="price" class="form-group__label">販売価格</label>
                <div class="sell-price">
                    <span class="sell-price__yen">¥</span>
                    <input type="number" name="price" id="price" class="form-group__input"
                        value="{{ old('price') }}" required>
                </div>
                @error('price')
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 出品ボタン --}}
            <button type="submit" class="profile__submit-button">出品する</button>

        </form>
    </div>
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection
