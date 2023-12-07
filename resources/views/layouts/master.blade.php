<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <title>KW - @yield('title')</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="{{ URL::to('src/css/styles.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ URL::to('src/css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::to('src/css/layout.css') }}">
    <link rel="stylesheet" href="{{ URL::to('src/css/colours-default.css') }}">
</head>
<body>
    @include('includes.header')
    <div class="container text-center">
        @yield('content')
    </div>

    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="{{ URL::to('src/js/app.js') }}"></script>
    <script src="{{ URL::to('src/js/confirm.js') }}"></script>
    <script src="{{ URL::to('src/js/like.js') }}"></script>
    <script src="{{ URL::to('src/js/post.js') }}"></script>
    <script src="{{ URL::to('src/js/friends.js') }}"></script>
</body>
</html>
