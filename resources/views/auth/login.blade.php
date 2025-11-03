@extends('layouts.guest')

@section('content')
    <!-- Logo -->
    <div class="mb-10">
        <img src="{{ asset('images/logo.png') }}" 
             alt="Patrick's Snack" 
             class="mx-auto w-60 md:w-96 animate-logo-entry">
    </div>

    <!-- Contenedor del formulario -->
    <div class="w-full sm:max-w-md px-8 py-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-2xl rounded-3xl">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500 transition" 
                              type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500 transition" 
                              type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center mb-6">
                <input id="remember_me" type="checkbox" class="rounded text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-700" name="remember">
                <label for="remember_me" class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</label>
            </div>

            <!-- Botón y olvido de contraseña
            <div class="flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif -->

                <x-primary-button class="bg-red-600 hover:bg-red-700 text-xl font-bold px-10 py-4 rounded-2xl transform transition duration-500 hover:scale-110 hover:shadow-2xl animate-pulse">
                    {{ __('Ingresar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection
