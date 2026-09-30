@extends('layouts.admin')

@section('title', 'Organisations')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Organisations</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $organizations->total() }} organisation(s)</p>
    </div>
    <a href="{{ route('admin.organizations.create') }}" class="inline-flex items-center gap-2 bg-[#003189] hover:bg-[#0047c8] text-white font-semibold px-4 py-2 rounded-xl transition-colors text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle organisation
    </a>
</div>

<form method="GET" class="mb-6 flex gap-3">
    <select name="status" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white">
        <option value="">Tous les statuts</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
    </select>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Nom</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Couleur primaire</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Collaborateurs</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Statut</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($organizations as $org)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-900">{{ $org->name }}</div>
                        <div class="text-xs text-slate-400">{{ $org->slug }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full border border-slate-200 shadow-inner" style="background-color: {{ $org->primary_color }}"></div>
                            <span class="font-mono text-xs text-slate-500">{{ $org->primary_color }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $org->employees_count }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $org->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $org->status === 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.organizations.edit', $org) }}" class="text-slate-400 hover:text-[#003189] transition-colors text-xs font-medium">Modifier</a>
                            <form method="POST" action="{{ route('admin.organizations.destroy', $org) }}" onsubmit="return confirm('Désactiver cette organisation ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors text-xs" aria-label="Supprimer organisation {{ $org->name }}">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">Aucune organisation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $organizations->links() }}</div>
@endsection
