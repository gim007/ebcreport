<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Instructor Portal — DISC Report')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

<nav class="bg-blue-800 text-white px-6 py-3 flex items-center justify-between">
    <a href="{{ route('instructor.dashboard') }}" class="font-bold text-lg">DISC Report &mdash; Instructor Portal</a>
    <div class="flex items-center gap-4 text-sm">
        @auth
            <span class="text-blue-200">{{ Auth::user()->instructor?->full_name }}</span>
            <a href="{{ route('instructor.courses.index') }}" class="hover:text-white text-blue-200">Courses</a>
            <a href="{{ route('instructor.account') }}" class="hover:text-white text-blue-200">My Account</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-blue-200 hover:text-white">Sign out</button>
            </form>
        @endauth
    </div>
</nav>

@auth
    @if (Auth::user()->instructor && ! Auth::user()->instructor->isApproved())
        <div class="bg-yellow-50 border-b border-yellow-200 px-6 py-2 text-sm text-yellow-800">
            Your account is pending administrator approval. Some features are restricted until approved.
        </div>
    @endif
@endauth

<main class="max-w-6xl mx-auto px-4 py-8">
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

</body>
</html>
