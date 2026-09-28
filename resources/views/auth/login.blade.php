@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#003189]">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#003189]">MMI'e</h1>
            <p class="text-gray-500 text-sm mt-1">Administration des cartes</p>
        </div>

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required autofocus
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003189] @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003189]"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#003189] hover:bg-[#002070] text-white font-semibold rounded-lg py-2.5 text-sm transition"
            >
                Se connecter
            </button>
        </form>
    </div>
</div>
@endsection
