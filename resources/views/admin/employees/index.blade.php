@extends('layouts.admin')
@section('title', 'Collaborateurs')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Collaborateurs</h1>
    <a href="{{ route('admin.imports.create') }}" class="inline-flex items-center gap-2 bg-[#003189] hover:bg-[#002266] text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-md shadow-[#003189]/20 transition-all active:scale-[0.98]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Importer CSV
    </a>
</div>

<form method="GET" class="flex flex-wrap gap-3 mb-8 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email, fonction…"
        class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all min-w-[200px]">
    <select name="status" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all">
        <option value="">Tous les statuts</option>
        <option value="active" @selected(request('status') === 'active')>Actifs</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Désactivés</option>
    </select>
    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white rounded-xl px-6 py-2.5 text-sm font-semibold transition-colors">Filtrer</button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.employees.index') }}" class="text-sm font-medium text-slate-400 hover:text-slate-600 self-center px-2 transition-colors">Réinitialiser</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-slate-400 text-[0.65rem] font-bold uppercase tracking-widest border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Nom</th>
                    <th class="px-6 py-4">Fonction</th>
                    <th class="px-6 py-4">Service</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 text-center">Statut</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($employees as $emp)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $emp->fullName() }}</td>
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $emp->job_title ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $emp->department ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $emp->email ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($emp->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Actif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">Désactivé</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('card.show', $emp->slug) }}" target="_blank" class="text-sm font-semibold text-[#003189] hover:text-[#002266] mr-4 transition-colors">Carte</a>
                        <a href="{{ route('admin.employees.show', $emp) }}" class="text-sm font-semibold text-slate-400 hover:text-slate-800 transition-colors">Gérer</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">Aucun collaborateur trouvé avec ces critères.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $employees->links() }}</div>
@endsection
