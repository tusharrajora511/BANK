<!DOCTYPE html>
<html>
    <head>
        <title>Bank Management System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
    <body>
        @include('partials.header')
        <div class="container mt-4">
            @yield('content')
</div>
@include('partials.footer')
    </body>
</html>