@extends('layouts.admin')
@section('title', "Rapport d'import")
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.imports.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour aux imports
    </a>
</div>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Rapport d'importation</h1>
    <p class="text-sm font-medium text-slate-500 flex items-center gap-2">
        <span class="bg-white border border-slate-200 px-2.5 py-1 rounded-lg text-slate-700 font-mono text-xs">{{ $import->filename }}</span>
        Importé le {{ $import->created_at->format('d/m/Y à H:i') }}
    </p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-10">
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 p-5 text-center">
        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Total lu</p>
        <p class="text-4xl font-extrabold text-slate-800">{{ $import->rows_total }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-emerald-100 p-5 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-emerald-50/50"></div>
        <p class="text-[0.65rem] font-bold text-emerald-600 uppercase tracking-widest mb-2 relative z-10">Créés</p>
        <p class="text-4xl font-extrabold text-emerald-600 relative z-10">{{ $import->rows_created }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-blue-100 p-5 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-blue-50/50"></div>
        <p class="text-[0.65rem] font-bold text-blue-600 uppercase tracking-widest mb-2 relative z-10">Mis à jour</p>
        <p class="text-4xl font-extrabold text-blue-600 relative z-10">{{ $import->rows_updated }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-orange-100 p-5 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-orange-50/50"></div>
        <p class="text-[0.65rem] font-bold text-orange-600 uppercase tracking-widest mb-2 relative z-10">Absents</p>
        <p class="text-4xl font-extrabold text-orange-500 relative z-10">{{ $import->rows_missing }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-red-100 p-5 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-red-50/50"></div>
        <p class="text-[0.65rem] font-bold text-red-600 uppercase tracking-widest mb-2 relative z-10">Erreurs</p>
        <p class="text-4xl font-extrabold text-red-600 relative z-10">{{ $import->rows_failed }}</p>
    </div>
</div>

@php $report = $import->report_json ?? []; @endphp

<div class="space-y-6">
    @foreach(['created' => ['Créés', 'emerald'], 'updated' => ['Mis à jour', 'blue'], 'unchanged' => ['Inchangés', 'slate'], 'missing' => ['Absents du CSV', 'orange'], 'failed' => ['Erreurs', 'red']] as $key => [$label, $color])
        @if(!empty($report[$key]))
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.02)] border border-slate-100 overflow-hidden">
            <div class="bg-{{ $color }}-50/50 border-b border-{{ $color }}-100 px-6 py-4">
                <h2 class="text-sm font-extrabold text-{{ $color }}-700 flex items-center gap-2">
                    <span class="bg-{{ $color }}-200 text-{{ $color }}-800 text-xs px-2 py-0.5 rounded-full">{{ count($report[$key]) }}</span>
                    {{ $label }}
                </h2>
            </div>
            <ul class="divide-y divide-slate-50">
                @foreach($report[$key] as $entry)
                    <li class="px-6 py-3 flex flex-wrap items-center gap-x-4 gap-y-1 hover:bg-slate-50/50 transition-colors">
                        @if(isset($entry['name']))<span class="text-sm font-bold text-slate-800">{{ $entry['name'] }}</span>@endif
                        @if(isset($entry['reason']))<span class="text-sm font-medium text-slate-500 flex-1">{{ $entry['reason'] }}</span>@endif
                        @if(isset($entry['row']))<span class="text-xs font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">Ligne {{ $entry['row'] }}</span>@endif
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    @endforeach
</div>
@endsection
