@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
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
            <li class="header__nav-item"><a href="{{ route('items.create') }}" class="header__nav-listing">出品</a></li>
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
    <div class="item-page">

        {{-- タブ切り替え --}}
        <div class="tab-nav">
            <div class="tab-nav__inner">
                <a href="/?tab=recommend" class="tab-nav__link {{ request('tab') !== 'mylist' ? 'tab-nav__link--active' : '' }}">おすすめ</a>
                {{-- ログイン中 --}}
                @auth
                    <a href="/?tab=mylist" class="tab-nav__link {{ request('tab') === 'mylist' ? 'tab-nav__link--active' : ''}}">マイリスト</a>
                @endauth

                {{-- ログイン前 --}}
                @guest
                    <a href="{{ route('login') }}" class="tab-nav__link">マイリスト</a>
                @endguest
            </div>
        </div>

        {{-- 商品一覧 --}}
        <main class="item-grid">
            @foreach ($items as $item)
                <div class="item-card">
                    <a href="{{ route('items.show', ['item' => $item->id]) }}" class="item-card__link">
                        <div class="item-card__image-wrapper">
                            {{-- 商品画像 --}}
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="item-card__image">
                        </div>
                        <div class="item-card__content">
                            <p class="item-card__name">{{ $item->name }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </main>
    </div>
@endsection
