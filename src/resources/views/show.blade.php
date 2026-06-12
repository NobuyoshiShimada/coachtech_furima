@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('header')
    <form action="/" method="GET" class="header__search">
        <input type="text" name="keyword"class="header__search--input" placeholder="なにをお探しですか？" value="{{ request("keyword") }}">
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
    <main class="item-detail">
        <div class="item-detail__inner">
            {{-- 左側 --}}
            <div class="detail-image">
                <div class="detail-image__wrapper">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="detail-image__img">
                    @if ($item->status === 'sold')
                        <div class="detail-image__sold-badge">SOLD</div>
                    @endif
                </div>
            </div>
            {{-- 右側 --}}
            <div class="detail-content">
                <h1 class="detail-content__name">{{ $item->name }}</h1>
                <p class="detail-content__brand">{{ $item->brand ?? 'ブランドなし' }}</p>
                <p class="detail-content__price">¥{{ number_format($item->price) }} <span
                        class="detail-content__tax">(税込)</span></p>

                {{-- いいね、コメントのアイコン --}}
                <div class="action-buttons">
                    {{-- いいねアイコン --}}
                    <div class="action-buttons__item">
                        {{-- ログイン中 --}}
                        @auth
                            <button
                                class="action-buttons__btn {{ $item->likes()->where('user_id', Auth::id())->exists() ? 'action-buttons__btn--active' : '' }}"
                                id="like-button" data-item-id="{{ $item->id }}">
                                <svg id="like-icon" xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960"
                                    width="35px" fill="#e3e3e3">
                                    <path
                                        d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z" />
                                </svg>
                            </button>
                        @endauth

                        {{-- ログイン前 --}}
                        @guest
                            <a href="{{ route('login') }}" class="action-buttons__btn">
                                <svg id="like-icon "xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960"
                                    width="35px" fill="#e3e3e3">
                                    <path
                                        d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z" />
                                </svg>
                            </a>
                        @endguest

                        {{-- いいねのカウント総数 --}}
                        <span class="action-buttons__count" id="like-count">{{ $item->likes->count() ?? 0 }}</span>
                    </div>
                    {{-- コメントアイコン --}}
                    <div class="action-buttons__item">
                        <div class="action-buttons__btn">
                            <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960" width="35px"
                                fill="#e3e3e3">
                                <path
                                    d="M80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z" />
                            </svg>
                        </div>
                        <span class="action-buttons__count">{{ $item->comments->count() ?? 0 }}</span>
                    </div>
                </div>
                {{-- 購入手続きボタン --}}
                @if ($item->status === 'sold')
                    <button class="detail-content__purchase-btn detail-content__purchase-btn--sold" disabled>
                    </button>
                @else

                    {{-- ログイン中 --}}
                    @auth
                        <a href="{{ route('item.purchase', ['item' => $item->id]) }}"
                            class="detail-content__purchase-btn">購入手続きへ</a>
                    @endauth

                    {{-- ログイン前 --}}
                    @guest
                        <a href="{{ route('login') }}" class="detail-content__purchase-btn">購入手続きへ</a>
                    @endguest
                @endif

                {{-- 商品説明 --}}
                <section class="detail-section">
                    <h2 class="detail-section__title">商品説明</h2>
                    <div class="detail-section__text">{!! nl2br(e($item->description)) !!}</div>
                </section>

                {{-- 商品の情報 --}}
                <section class="detail-section">
                    <h2 class="detail-section__title">商品の情報</h2>
                    <div class="detail-info">
                        <div class="detail-info__label">カテゴリー</div>
                        <div class="detail-info__value">
                            @forelse ($item->categories as $category)
                                <span class="detail-info__badge">{{ $category->name }}</span>
                            @empty
                                <span class="detail-info__badge">未設定</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="detail-info">
                        <div class="detail-info__label">商品の状態</div>
                        <div class="detail-info__value">
                            {{ $item->condition->name ?? '未設定' }}
                        </div>
                    </div>
                </section>

                {{-- コメント --}}
                <section class="detail-section">
                    <h2 class="detail-section__title">コメント({{ $item->comments->count() ?? 0 }})</h2>
                    {{-- コメントリスト --}}
                    <div class="comment-list">
                        @forelse ($item->comments as $comment)
                            <div class="comment-item">
                                <div class="comment-item__user">
                                    <div class="comment-item__avatar"></div>
                                    <span class="comment-item__username">{{ $comment->user->name }}</span>
                                </div>
                                <div class="comment-item__content">{{ $comment->content }}
                                </div>
                            </div>
                        @empty
                            <div class="comment-item">
                                <div class="comment-item__user">
                                    <div class="comment-item__avatar"></div>
                                    <span class="comment-item__username">admin</span>
                                </div>
                                <div class="comment-item__content">こちらにコメントが入ります</div>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="detail-section">
                    <h3 class="detail-section__sub-title">商品へのコメント</h3>
                    {{-- ログイン中 --}}
                    @auth
                        <form action="{{ route('comments.store', ['item' => $item->id]) }}" method="POST"
                            class="comment-form">
                            @csrf
                            <textarea name="content" class="comment-form__textarea" rows="6"></textarea>
                            @error('content')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="comment-form__submit-btn">コメントを送信する</button>

                        </form>
                    @endauth

                    {{-- 未ログイン --}}
                    @guest
                        <div class="comment-form">
                            <textarea name="content" class="comment-form__textarea" rows="6" placeholder="コメントを入力してください"></textarea>
                            <a href="{{ route('login') }}" class="comment-form__submit-btn">コメントを送信する</a>
                        </div>
                    @endguest
                </section>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/like.js') }}"></script>
@endsection
