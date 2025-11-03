<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Patrick\'s Snack') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(120deg, #EF3B2D, #FDC830, #EF3B2D);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0%,100% {background-position:0% 50%;}
            50% {background-position:100% 50%;}
        }

        @keyframes logo-entry {
            0% { opacity: 0; transform: translateY(-80px) scale(0.8); }
            70% { transform: translateY(10px) scale(1.05); opacity: 1; }
            100% { transform: translateY(0) scale(1); }
        }
        .animate-logo-entry {
            animation: logo-entry 1s forwards;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased flex items-center justify-center min-h-screen">
    <div class="flex flex-col items-center w-full px-4">
        @yield('content')
    </div>
</body>
</html>
