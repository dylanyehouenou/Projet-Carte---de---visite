@extends('layouts.admin')
@section('title', 'Imports CSV')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Historique des imports</h1>
    <a href="{{ route('admin.imports.create') }}" class="inline-flex items-center gap-2 bg-[#003189] hover:bg-[#002266] text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-md shadow-[#003189]/20 transition-all active:scale-[0.98]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel import
    </a>
</div>

<div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-slate-400 text-[0.65rem] font-bold uppercase tracking-widest border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Fichier</th>
                    <th class="px-6 py-4">Importé par</th>
                    <th class="px-6 py-4 text-center">Créés</th>
                    <th class="px-6 py-4 text-center">Mis à jour</th>
                    <th class="px-6 py-4 text-center">Absents</th>
                    <th class="px-6 py-4 text-center">Erreurs</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($imports as $run)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4 font-bold text-slate-800">{{ $run->filename }}</td>
                    <td class="px-6 py-4 text-slate-500 font-medium">{{ $run->importedBy?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-center font-bold text-emerald-600 bg-emerald-50/30">{{ $run->rows_created }}</td>
                    <td class="px-6 py-4 text-center font-bold text-blue-600 bg-blue-50/30">{{ $run->rows_updated }}</td>
                    <td class="px-6 py-4 text-center font-bold text-orange-500 bg-orange-50/30">{{ $run->rows_missing }}</td>
                    <td class="px-6 py-4 text-center font-bold text-red-500 bg-red-50/30">{{ $run->rows_failed }}</td>
                    <td class="px-6 py-4 text-slate-400 text-xs font-semibold">{{ $run->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.imports.show', $run) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#003189] hover:text-[#002266] transition-colors">
                            Rapport
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-slate-400 font-medium">Aucun import effectué pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $imports->links() }}</div>
@endsection
