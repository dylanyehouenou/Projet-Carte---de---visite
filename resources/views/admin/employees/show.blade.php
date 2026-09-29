@extends('layouts.admin')
@section('title', $employee->fullName())
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour aux collaborateurs
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-8 pb-8 border-b border-slate-100">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $employee->fullName() }}</h1>
                @if($employee->job_title)<p class="text-lg font-semibold text-[#003189] mt-1">{{ $employee->job_title }}</p>@endif
                @if($employee->department)<p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">{{ $employee->department }}</p>@endif
            </div>
            <div>
                @if($employee->is_active)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Statut : Actif</span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-slate-100 text-slate-500 border border-slate-200">Statut : Désactivé</span>
                @endif
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
            @if($employee->email)
            <div>
                <dt class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email</dt>
                <dd class="text-sm font-semibold text-slate-800">{{ $employee->email }}</dd>
            </div>
            @endif
            @if($employee->phone)
            <div>
                <dt class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Téléphone</dt>
                <dd class="text-sm font-semibold text-slate-800">{{ $employee->phone }}</dd>
            </div>
            @endif
            @if($employee->postal_address)
            <div class="sm:col-span-2">
                <dt class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Adresse</dt>
                <dd class="text-sm font-semibold text-slate-800">{{ $employee->postal_address }}</dd>
            </div>
            @endif
            @if($employee->linkedin_url)
            <div>
                <dt class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-1.5">LinkedIn</dt>
                <dd><a href="{{ $employee->linkedin_url }}" target="_blank" class="text-sm font-semibold text-[#003189] hover:underline truncate block">{{ $employee->linkedin_url }}</a></dd>
            </div>
            @endif
            @if($employee->calendly_url)
            <div>
                <dt class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Calendly</dt>
                <dd><a href="{{ $employee->calendly_url }}" target="_blank" class="text-sm font-semibold text-[#003189] hover:underline truncate block">{{ $employee->calendly_url }}</a></dd>
            </div>
            @endif
        </dl>

        <div class="flex flex-wrap items-center gap-3 mt-10 pt-6 border-t border-slate-100">
            <a href="{{ route('admin.employees.edit', $employee) }}"
               class="bg-[#003189] hover:bg-[#002266] text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-md shadow-[#003189]/20 transition-all active:scale-[0.98]">
               Modifier le profil
            </a>
            <a href="{{ route('card.show', $employee->slug) }}" target="_blank"
               class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm transition-all active:scale-[0.98]">
               Voir la carte
            </a>
            <form method="POST" action="{{ route('admin.employees.toggle', $employee) }}" class="ml-auto">
                @csrf
                <button type="submit"
                    class="{{ $employee->is_active ? 'text-red-600 bg-red-50 border-red-100 hover:bg-red-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100 hover:bg-emerald-100' }} border rounded-xl px-5 py-2.5 text-sm font-bold transition-all active:scale-[0.98]"
                    onclick="return confirm('{{ $employee->is_active ? 'Désactiver cette carte ?' : 'Activer cette carte ?' }}')">
                    {{ $employee->is_active ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 text-center h-fit">
        <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-6">QR Code de la carte</p>
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 inline-block mb-6">
            <img src="{{ route('card.qr', $employee->slug) }}" alt="QR" class="w-40 h-40 mx-auto">
        </div>
        <a href="{{ route('card.qr', $employee->slug) }}" download="qr-{{ $employee->slug }}.png"
           class="flex justify-center items-center gap-2 w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl py-3 text-sm font-semibold transition-all active:scale-[0.98] mb-4">
           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
           Télécharger (PNG)
        </a>
        <p class="text-[0.65rem] font-mono text-slate-400 break-all bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">{{ $employee->publicUrl() }}</p>
    </div>
</div>
@endsection
