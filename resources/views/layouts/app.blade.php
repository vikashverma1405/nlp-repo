<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel NLQ') }}</title>

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="min-h-screen py-10">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        @livewireScripts
    </body>
</html>
