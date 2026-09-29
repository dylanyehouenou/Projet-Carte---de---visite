@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<h1 class="text-3xl font-extrabold text-slate-900 mb-8 tracking-tight">Vue d'ensemble</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 flex flex-col justify-between">
        <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Total collaborateurs</p>
        <p class="text-5xl font-extrabold text-[#003189] mt-3">{{ $total }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 flex flex-col justify-between">
        <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Cartes actives</p>
        <p class="text-5xl font-extrabold text-emerald-500 mt-3">{{ $active }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 flex flex-col justify-between">
        <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Cartes désactivées</p>
        <p class="text-5xl font-extrabold text-slate-300 mt-3">{{ $inactive }}</p>
    </div>
</div>

@if($lastImport)
<div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 mb-10">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800 mb-1">Dernier import CSV</h2>
            <p class="text-sm text-slate-500 font-medium">
                <span class="text-slate-700">{{ $lastImport->filename }}</span> — {{ $lastImport->created_at->diffForHumans() }}
            </p>
            <div class="flex flex-wrap gap-2 mt-4">
                <span class="bg-emerald-50 text-emerald-600 text-xs font-bold px-3 py-1 rounded-lg">{{ $lastImport->rows_created }} créés</span>
                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-lg">{{ $lastImport->rows_updated }} mis à jour</span>
                <span class="bg-orange-50 text-orange-600 text-xs font-bold px-3 py-1 rounded-lg">{{ $lastImport->rows_missing }} absents</span>
                <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-lg">{{ $lastImport->rows_failed }} erreurs</span>
            </div>
        </div>
        <a href="{{ route('admin.imports.show', $lastImport) }}" class="flex items-center gap-2 text-sm font-semibold text-[#003189] hover:text-[#002266] bg-blue-50/50 hover:bg-blue-50 px-4 py-2 rounded-xl transition-colors">
            Voir le rapport
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
@endif

<div class="flex flex-wrap gap-4">
    <a href="{{ route('admin.employees.index') }}"
       class="bg-[#003189] hover:bg-[#002266] text-white rounded-xl px-6 py-3 text-sm font-semibold shadow-md shadow-[#003189]/20 transition-all active:scale-[0.98]">
        Gérer les collaborateurs
    </a>
    <a href="{{ route('admin.imports.create') }}"
       class="bg-white border border-slate-200 hover:border-[#003189]/30 hover:bg-slate-50 text-slate-700 rounded-xl px-6 py-3 text-sm font-semibold shadow-sm transition-all active:scale-[0.98]">
        Importer un nouveau CSV
    </a>
</div>
@endsection
