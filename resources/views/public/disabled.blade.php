@extends('layouts.app')

@section('title', "Carte non disponible — MMI'e")

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6">
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] w-full max-w-sm p-10 text-center border border-slate-100">

        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-slate-100 shadow-sm relative overflow-hidden">
            <svg class="w-8 h-8 text-slate-400 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-2 tracking-tight">Carte indisponible</h1>

        <p class="text-slate-500 text-sm font-medium leading-relaxed">
            Cette carte de visite numérique n'est plus active ou n'existe pas.
        </p>

        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-slate-400 text-[0.7rem] font-bold tracking-widest uppercase">
                MMI'e
            </p>
        </div>

    </div>
</div>
@endsection
