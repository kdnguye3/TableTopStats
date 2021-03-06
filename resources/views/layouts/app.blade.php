<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{url('/img/chess_game_grey_3Jh_icon.ico')}}" type="image/x-icon" rel="shortcut icon"  />

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TableTopStats</title>
    @yield('head')
</head>
<body id="app-layout">
    <div id="app">
        <nav class="navbar is-primary" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <div class="navbar-item">
                    <img src="{{url('/img/Chess-Game-grey.png')}}" alt="BG League" height="20" />
                    <div class="site-title" style="padding-left: 5px">
                        Game Stats
                    </div>
                </div>
                <button class="button navbar-burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            <div class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="/players">Players</a>
                    <a class="navbar-item" href="/games">Games</a>

                </div>
            </div>
        </nav>
        <div id="content">
            @yield('content')
        </div>

    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>

</body>

</html>
