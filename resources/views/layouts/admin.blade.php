<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration') — MMI'e</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-[#003189] text-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <span class="font-bold text-lg tracking-wide">MMI'e Admin</span>
            <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white text-sm">Dashboard</a>
            <a href="{{ route('admin.employees.index') }}" class="text-white/80 hover:text-white text-sm">Collaborateurs</a>
            <a href="{{ route('admin.imports.index') }}" class="text-white/80 hover:text-white text-sm">Imports CSV</a>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="text-white/70 hover:text-white text-sm">Déconnexion</button>
        </form>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 py-8">
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
