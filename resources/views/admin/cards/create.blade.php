@extends('layouts.admin')

@section('title', 'Nouvelle carte')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900">Nouvelle carte</h1>
    <p class="text-slate-500 mt-1">Comment souhaitez-vous commencer ?</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12" x-data="{ mode: null }">

    {{-- Mode: Automatique --}}
    <div @click="mode = 'auto'" :class="mode === 'auto' ? 'border-[#003189] shadow-blue-100 shadow-md' : 'border-slate-200 hover:border-blue-300'" class="bg-white rounded-2xl border-2 cursor-pointer transition-all p-6 flex flex-col gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-xl">✨</div>
        <div>
            <h2 class="font-bold text-slate-900">Automatique</h2>
            <p class="text-sm text-slate-500 mt-1">Généré depuis vos paramètres organisation et groupe.</p>
        </div>

        <div x-show="mode === 'auto'" x-transition class="mt-2">
            <form method="POST" action="{{ route('admin.cards.generate') }}" class="space-y-3">
                @csrf
                <select name="organization_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white">
                    <option value="">Aucune organisation</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
                <select name="group_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white">
                    <option value="">Aucun groupe</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" placeholder="Nom de la carte" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                <button type="submit" class="w-full bg-[#003189] text-white font-semibold py-2.5 rounded-xl text-sm hover:bg-[#0047c8] transition-colors">
                    Générer la carte
                </button>
            </form>
        </div>
    </div>

    {{-- Mode: Modèle --}}
    <a href="{{ route('admin.cards.templates') }}" class="bg-white rounded-2xl border-2 border-slate-200 hover:border-blue-300 transition-all p-6 flex flex-col gap-4 no-underline">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-xl">📋</div>
        <div>
            <h2 class="font-bold text-slate-900">Modèle</h2>
            <p class="text-sm text-slate-500 mt-1">Partir d'un modèle existant. La carte créée est indépendante.</p>
        </div>
        <div class="mt-auto pt-2">
            <span class="inline-block text-xs font-semibold text-[#003189]">Voir les modèles →</span>
        </div>
    </a>

    {{-- Mode: De zéro --}}
    <div class="bg-white rounded-2xl border-2 border-slate-200 hover:border-blue-300 transition-all p-6 flex flex-col gap-4">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-xl">⬜</div>
        <div>
            <h2 class="font-bold text-slate-900">De zéro</h2>
            <p class="text-sm text-slate-500 mt-1">Canvas vierge. Composition libre.</p>
        </div>
        <div class="mt-auto pt-2">
            <form method="POST" action="{{ route('admin.cards.store') }}">
                @csrf
                <input type="hidden" name="name" value="Nouvelle carte">
                <button type="submit" class="w-full bg-slate-800 text-white font-semibold py-2.5 rounded-xl text-sm hover:bg-slate-700 transition-colors">
                    Créer et ouvrir l'éditeur
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
