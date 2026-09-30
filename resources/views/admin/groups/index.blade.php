@extends('layouts.admin')

@section('title', 'Groupes')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Groupes</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $groups->total() }} groupe(s)</p>
    </div>
    <a href="{{ route('admin.groups.create') }}" class="inline-flex items-center gap-2 bg-[#003189] hover:bg-[#0047c8] text-white font-semibold px-4 py-2 rounded-xl transition-colors text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau groupe
    </a>
</div>

<form method="GET" class="mb-6 flex gap-3">
    <select name="organization_id" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white">
        <option value="">Toutes les organisations</option>
        @foreach($organizations as $org)
            <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
        @endforeach
    </select>
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
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Organisation</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Couleur</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Collaborateurs</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Statut</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($groups as $group)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $group->name }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $group->organization->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($group->primary_color)
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full border border-slate-200" style="background-color: {{ $group->primary_color }}"></div>
                                <span class="font-mono text-xs text-slate-400">{{ $group->primary_color }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Hérité</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $group->employees_count }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $group->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $group->status === 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.groups.edit', $group) }}" class="text-slate-400 hover:text-[#003189] transition-colors text-xs font-medium">Modifier</a>
                            <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" onsubmit="return confirm('Supprimer ce groupe ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors text-xs">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">Aucun groupe.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $groups->links() }}</div>
@endsection
