<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtech_furima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header__inner">
                <a href="/" class="header-logo">
                    <img src="{{ asset('images/coachtech_header_logo.png') }}" alt="coachtech_furima"
                        class="header-logo__link">
                </a>
                @yield('header')
            </div>
        </header>

        @yield('content')
    </div>

</body>

</html>
