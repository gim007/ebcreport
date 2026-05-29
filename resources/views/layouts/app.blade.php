<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DISC Report')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

<nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <a href="{{ route('participant.organizations') }}" class="text-lg font-bold text-blue-700">DISC Report</a>
    <div class="flex items-center gap-4 text-sm">
        @auth
            <a href="{{ route('participant.account') }}" class="text-gray-600 hover:text-blue-600">My Account</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-600 hover:text-red-600">Sign out</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600">Sign in</a>
        @endauth
    </div>
</nav>

<main class="max-w-5xl mx-auto px-4 py-8 flex-1 w-full">
    @if (session('status'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-300 text-green-800 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-300 text-red-800 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

@include('partials.footer')

@livewireScripts
@stack('scripts')
</body>
</html>
