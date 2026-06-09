@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
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
<div class="mypage">
    <div class="user-profile">
        <div class="user-profile__inner">
            <div class="user-profile__info">

                {{-- プロフィール画像 --}}
                <div class="user-profile__avatar">
                    @if ($user->profile && $user->profile->image_url)
                    <img src="{{ asset('storage/'. $user->profile->image_url) }}" alt="avatar" class="user-profile__avatar-img">
                    @endif
                </div>
                <h1 class="user-profile__name">{{ $user->name }}</h1>
            </div>
            <a href="{{ route('profile.edit') }}" class="user-profile__edit-btn">プロフィールを編集</a>
        </div>
    </div>

    {{-- 中央タブ切り替え --}}
    <div class="tab-nav">
        <div class="tab-nav__inner">
            <a href="/mypage?tab=sell" class="tab-nav__link {{ request('tab') !== 'buy' ? 'tab-nav__link--active' : '' }}">出品した商品</a>
            <a href="/mypage?tab=buy" class="tab-nav__link {{ request('tab') === 'buy' ? 'tab-nav__link--active' : '' }}">購入した商品</a>
        </div>
    </div>

    {{-- 商品一覧 --}}
    <main class="item-grid">
        @forelse ($items as $item)
        <div class="item-card">
            <a href="{{ route('item.purchase', ['item' => $item->id]) }}" class="item-card__link">
                <div class="item-card__image-wrapper">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="item-card__image">
                </div>
                <div class="item-card__content">
                    <p class="item-card__name">{{ $item->name }}</p>
                </div>
            </a>
        </div>

        @empty
        {{-- 商品が0件の時 --}}
        <div class="item-grid__empty">該当する商品はまだありません</div>
        @endforelse
    </main>
</div>
@endsection
