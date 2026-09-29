@extends('layouts.app')

@section('title', $employee->fullName() . " — MMI'e")

@section('content')
<div class="min-h-screen flex items-center justify-center py-10 px-4 sm:px-6">
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] w-full max-w-sm overflow-hidden border border-slate-100">

        {{-- Header / Cover (Bannière MMI'e avec logo) --}}
        <div class="h-32 bg-gradient-to-tr from-[#003189] via-[#0047c8] to-[#005deb] relative overflow-hidden flex justify-center pt-5">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-4 w-24 h-24 bg-black/10 rounded-full blur-xl"></div>
            <span class="text-white/90 font-extrabold tracking-widest text-lg drop-shadow-sm z-10">MMI'e</span>
        </div>

        {{-- Section Profil (Photo chevauchante) --}}
        <div class="px-6 pb-6 text-center relative -mt-16">
            @if($employee->photo_path && Storage::disk('local')->exists($employee->photo_path))
                <img
                    src="{{ route('card.photo', $employee->slug) }}"
                    alt="{{ $employee->fullName() }}"
                    class="w-32 h-32 rounded-full object-cover mx-auto border-[6px] border-white shadow-sm bg-white relative z-10"
                >
            @else
                <div class="w-32 h-32 rounded-full bg-slate-100 text-[#003189] flex items-center justify-center mx-auto border-[6px] border-white shadow-sm relative z-10 text-4xl font-extrabold tracking-tight">
                    {{ strtoupper(mb_substr($employee->first_name, 0, 1)) }}{{ strtoupper(mb_substr($employee->last_name, 0, 1)) }}
                </div>
            @endif

            <div class="mt-4">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $employee->fullName() }}</h1>
                @if($employee->job_title)
                    <p class="text-[#003189] font-semibold text-sm mt-1">{{ $employee->job_title }}</p>
                @endif
                @if($employee->department)
                    <p class="text-slate-400 text-[0.7rem] font-bold uppercase tracking-widest mt-2">{{ $employee->department }}</p>
                @endif
            </div>

            {{-- Action Principale --}}
            <div class="mt-6">
                <a href="{{ route('card.vcard', $employee->slug) }}"
                   class="flex items-center justify-center gap-2.5 w-full bg-[#003189] hover:bg-[#002266] text-white rounded-2xl px-5 py-3.5 text-sm font-semibold shadow-lg shadow-[#003189]/20 transition-all active:scale-[0.98]">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter à mes contacts
                </a>
            </div>
        </div>

        {{-- Détails de contact --}}
        <div class="px-6 py-2 flex flex-col gap-3">

            @if($employee->email)
                <a href="mailto:{{ $employee->email }}"
                   class="group flex items-center gap-4 w-full bg-slate-50 hover:bg-blue-50/50 border border-slate-100 hover:border-blue-100 rounded-2xl p-3 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-[#003189] group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left overflow-hidden">
                        <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Email</p>
                        <p class="text-sm text-slate-700 font-semibold truncate">{{ $employee->email }}</p>
                    </div>
                </a>
            @endif

            @if($employee->phone)
                <a href="tel:{{ $employee->phone }}"
                   class="group flex items-center gap-4 w-full bg-slate-50 hover:bg-blue-50/50 border border-slate-100 hover:border-blue-100 rounded-2xl p-3 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-[#003189] group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Téléphone</p>
                        <p class="text-sm text-slate-700 font-semibold">{{ $employee->phone }}</p>
                    </div>
                </a>
            @endif

            @if($employee->calendly_url)
                <a href="{{ $employee->calendly_url }}" target="_blank" rel="noopener"
                   class="group flex items-center gap-4 w-full bg-slate-50 hover:bg-blue-50/50 border border-slate-100 hover:border-blue-100 rounded-2xl p-3 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-[#003189] group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Disponibilité</p>
                        <p class="text-sm text-slate-700 font-semibold">Prendre rendez-vous</p>
                    </div>
                </a>
            @endif

            @if($employee->linkedin_url)
                <a href="{{ $employee->linkedin_url }}" target="_blank" rel="noopener"
                   class="group flex items-center gap-4 w-full bg-slate-50 hover:bg-[#f3f9ff] border border-slate-100 hover:border-[#0077B5]/30 rounded-2xl p-3 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-[#0077B5] shadow-sm flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Réseau pro</p>
                        <p class="text-sm text-slate-700 font-semibold">Profil LinkedIn</p>
                    </div>
                </a>
            @endif

        </div>

        {{-- Bas de carte --}}
        <div class="mt-4 border-t border-slate-100 bg-slate-50/50 p-6">
            <div class="flex flex-col items-center">
                <div class="bg-white p-2.5 rounded-2xl shadow-sm border border-slate-100 mb-3">
                    <img
                        src="{{ route('card.qr', $employee->slug) }}"
                        alt="QR code de {{ $employee->fullName() }}"
                        class="w-24 h-24 object-contain"
                    >
                </div>

                <a href="{{ route('card.qr', $employee->slug) }}" download="qr-{{ $employee->slug }}.png"
                   class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400 hover:text-[#003189] transition-colors flex items-center gap-1.5 mb-5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Sauvegarder le QR Code
                </a>

                @if($employee->website)
                    <a href="{{ $employee->website }}" target="_blank" rel="noopener"
                       class="text-sm font-semibold text-[#003189] hover:text-[#002266] hover:underline decoration-2 underline-offset-4 transition-all">
                        {{ $employee->website }}
                    </a>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
