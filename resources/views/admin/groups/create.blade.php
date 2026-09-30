@extends('layouts.admin')

@section('title', 'Nouveau groupe')

@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('admin.groups.index') }}" class="text-sm text-slate-400 hover:text-slate-600 flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Groupes
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Nouveau groupe</h1>
    </div>

    <form method="POST" action="{{ route('admin.groups.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-5">
            <div>
                <label for="organization_id" class="block text-sm font-medium text-slate-700 mb-1">Organisation <span class="text-red-400">*</span></label>
                <select id="organization_id" name="organization_id" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('organization_id') border-red-400 @enderror">
                    <option value="">Sélectionner une organisation</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
                @error('organization_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nom <span class="text-red-400">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-admin.color-picker name="primary_color" label="Couleur primaire" :value="old('primary_color', '')" />
                    <p class="text-xs text-slate-400 mt-1">Laissez vide pour hériter de l'organisation.</p>
                </div>
                <div>
                    <x-admin.color-picker name="secondary_color" label="Couleur secondaire" :value="old('secondary_color', '')" />
                    <p class="text-xs text-slate-400 mt-1">Laissez vide pour hériter de l'organisation.</p>
                </div>
            </div>

            <div>
                <label for="default_card_template_id" class="block text-sm font-medium text-slate-700 mb-1">Template de carte par défaut</label>
                <select id="default_card_template_id" name="default_card_template_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Aucun</option>
                    @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}" {{ old('default_card_template_id') == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Statut</label>
                <select id="status" name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-[#003189] hover:bg-[#0047c8] text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                Créer le groupe
            </button>
            <a href="{{ route('admin.groups.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">Annuler</a>
        </div>
    </form>
</div>
@endsection
