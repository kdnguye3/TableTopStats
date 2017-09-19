<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <div id="content">
            @yield('content')
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>

</body>

</html>
