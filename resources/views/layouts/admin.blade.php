<!DOCTYPE html>
<html lang="fr" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration') — MMI'e</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 font-['Plus_Jakarta_Sans',sans-serif]">

<nav class="bg-[#003189] bg-gradient-to-r from-[#003189] to-[#0047c8] text-white shadow-lg shadow-[#003189]/10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <span class="font-extrabold text-xl tracking-wide">MMI'e <span class="text-blue-300 font-medium text-sm ml-1">Admin</span></span>
            <div class="hidden sm:flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-white' : '' }}">Dashboard</a>
                <a href="{{ route('admin.employees.index') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.employees.*') ? 'text-white' : '' }}">Collaborateurs</a>
                <a href="{{ route('admin.imports.index') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.imports.*') ? 'text-white' : '' }}">Imports CSV</a>
                <a href="{{ route('admin.organizations.index') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.organizations.*') ? 'text-white' : '' }}">Organisations</a>
                <a href="{{ route('admin.groups.index') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.groups.*') ? 'text-white' : '' }}">Groupes</a>
                <a href="{{ route('admin.cards.index') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.cards.*') ? 'text-white' : '' }}">Cartes</a>
                <a href="{{ route('admin.media.index') }}" class="text-blue-100 hover:text-white font-medium text-sm transition-colors {{ request()->routeIs('admin.media.*') ? 'text-white' : '' }}">Médiathèque</a>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="text-blue-200 hover:text-white text-sm font-semibold transition-colors flex items-center gap-2">
                Déconnexion
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </form>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 text-sm font-medium flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 text-sm font-medium flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
