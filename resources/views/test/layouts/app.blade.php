<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS (via CDN or compiled) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100 text-gray-900">
    <div id="app">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="container mx-auto flex justify-between items-center py-4 px-6">
                <a href="/" class="text-xl font-semibold text-gray-800">
                    {{ config('app.name', 'Laravel') }}
                </a>
                {{--  <div>
                    @guest
                        <a href="{{ route('register.form') }}" class="text-blue-600 hover:underline mr-4">Register</a>
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
                    @else
                        <span class="mr-4">Hello, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline">Logout</button>
                        </form>
                    @endguest
                </div>--}}
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
