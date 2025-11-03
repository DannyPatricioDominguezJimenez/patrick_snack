<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patrick's Snack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen overflow-hidden relative">

    <!-- Fondo animado -->
    <div class="absolute inset-0 bg-gradient-to-r from-red-400 via-yellow-300 to-red-500 animate-gradient-x blur-3xl opacity-30"></div>

    <div class="relative z-10 text-center">
        <!-- Logo gigante, sin rebote -->
        <img src="{{ asset('images/logo.png') }}" 
             alt="Patrick's Snack" 
             class="mx-auto w-96 md:w-[600px] mb-16">

        <!-- Botón animado -->
        <a href="{{ route('login') }}" 
           class="px-12 py-6 bg-red-600 text-white text-3xl font-bold rounded-3xl shadow-lg transform transition duration-500 hover:scale-110 hover:bg-red-700 hover:shadow-2xl animate-pulse">
            Ingresar
        </a>
    </div>

    <!-- Tailwind custom animation -->
    <style>
        @keyframes gradient-x {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient-x {
            background-size: 200% 200%;
            animation: gradient-x 10s ease infinite;
        }
    </style>
</body>
</html>
