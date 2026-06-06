@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
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
    <main class="purchase-page">
        <div class="purchase-page__inner">

            {{-- 左 --}}
            <div class="purchase-main">

                {{-- 商品情報 --}}
                <div class="purchase-section">
                    <div class="purchase-item">
                        <div class="purchase-item__image">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="purchase-item__image__img">
                        </div>
                        <div class="purchase-item__info">
                            <h1 class="purchase-item__name">{{ $item->name }}</h1>
                            <p class="purchase-item__price">¥{{ number_format($item->price) }}</p>
                        </div>
                    </div>
                </div>

                {{-- 支払い方法 --}}
                <div class="purchase-section">
                    <div class="purchase-section__header">
                        <h2 class="purchase-section__title">支払い方法</h2>
                    </div>
                    <div class="purchase-section__content">
                        <select name="payment_method" class="purchase-select" id="payment-select"
                            onchange="updatePaymentDisplay()" form="purchase-form" >
                            <option value="" disabled selected>選択してください</option>
                            <option value="konbini" {{ old('payment_method') === 'konbini' ? 'selected' : '' }}>コンビニ払い
                            </option>
                            <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>クレジットカード払い
                            </option>
                        </select>
                        @error('payment_method')
                            <div class="form-group__error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 配送先 --}}
                <div class="purchase-section">
                    <div class="purchase-section__header">
                        <h2 class="purchase-section__title">配送先</h2>
                        <a href="{{ route('address.edit', ['item' => $item->id]) }}"
                            class="purchase-section__link">変更する</a>
                    </div>
                    <div class="purchase-section__content">
                        <div class="shipping-address">
                            @if ($user->profile && $user->profile->postcode)
                                <p class="shipping-address__text">〒{{ $user->profile->postcode }}</p>
                                <p class="shipping-address__text">{{ $user->profile->address }}
                                    {{ $user->profile->building }}</p>
                            @else
                                <p class="shipping-address__text shipping-address__text--alert">
                                    配送先が登録されていません。プロフィールから登録してください。</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 右 --}}
            <div class="purchase-side">
                <form action="{{ route('item.checkout', ['item' => $item->id]) }}" method="POST" class="summary-card"
                    id="purchase-form">
                    @csrf

                    <div class="summary">
                        <table class="summary-table">
                            <tr class="summary-table__row">
                                <th class="summary-table__cell summary-table__cell--label">商品代金</th>
                                <td class="summary-table__cell summary-table__cell--value">
                                    ¥{{ number_format($item->price) }}
                                </td>
                            </tr>
                            <tr class="summary-table__row">
                                <th class="summary-table__cell summary-table__cell--label">支払い方法</th>
                                <td class="summary-table__cell summary-table__cell--value" id="payment-display">-</td>
                            </tr>
                        </table>
                    </div>
                    {{-- 購入確定ボタン --}}
                    <button type="submit" class="summary__submit-btn">購入する</button>
                </form>
            </div>
        </div>
    </main>
    <script src="{{ asset('js/purchase.js') }}"></script>
@endsection
