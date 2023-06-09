<!DOCTYPE html>
<html data-theme="cupcake" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Sendwich') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/icon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/icon//favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/icon//favicon-16x16.png">
        <link rel="manifest" href="/images/icon//site.webmanifest">
        <link rel="mask-icon" href="/images/icon//safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
    <script type="text/javascript">
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            jsPermissions: {!! auth()->check()?auth()->user()->jsPermissions():null !!}
        }
    </script>
</html>
