@extends('layouts.app')

@section('title', $employee->fullName() . " — MMI'e")

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#003189] to-[#0047c8] flex items-start justify-center py-8 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">

        {{-- Header MMI'e --}}
        <div class="bg-[#003189] px-6 pt-8 pb-6 text-white text-center">
            @if($employee->photo_path && Storage::disk('local')->exists($employee->photo_path))
                <img
                    src="{{ route('card.photo', $employee->slug) }}"
                    alt="{{ $employee->fullName() }}"
                    class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border-4 border-white/30"
                >
            @else
                <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                    {{ strtoupper(mb_substr($employee->first_name, 0, 1)) }}{{ strtoupper(mb_substr($employee->last_name, 0, 1)) }}
                </div>
            @endif

            <h1 class="text-xl font-bold">{{ $employee->fullName() }}</h1>
            @if($employee->job_title)
                <p class="text-blue-200 text-sm mt-1">{{ $employee->job_title }}</p>
            @endif
            @if($employee->department)
                <p class="text-blue-300 text-xs mt-0.5">{{ $employee->department }}</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-6 py-5 space-y-3">

            @if($employee->email)
            <a href="mailto:{{ $employee->email }}"
               class="flex items-center gap-3 w-full bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 transition">
                <svg class="w-5 h-5 text-[#003189] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>{{ $employee->email }}</span>
            </a>
            @endif

            @if($employee->phone)
            <a href="tel:{{ $employee->phone }}"
               class="flex items-center gap-3 w-full bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 transition">
                <svg class="w-5 h-5 text-[#003189] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>{{ $employee->phone }}</span>
            </a>
            @endif

            <a href="{{ route('card.vcard', $employee->slug) }}"
               class="flex items-center justify-center gap-2 w-full bg-[#003189] hover:bg-[#002070] text-white rounded-xl px-4 py-3 text-sm font-semibold transition">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter à mes contacts
            </a>

            @if($employee->calendly_url)
            <a href="{{ $employee->calendly_url }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 w-full bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 transition">
                <svg class="w-5 h-5 text-[#003189] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Prendre rendez-vous
            </a>
            @endif

            @if($employee->linkedin_url)
            <a href="{{ $employee->linkedin_url }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 w-full bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 transition">
                <svg class="w-5 h-5 text-[#0077B5] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
                LinkedIn
            </a>
            @endif

        </div>

        {{-- QR Code --}}
        <div class="border-t border-gray-100 px-6 py-5 text-center">
            <p class="text-xs text-gray-400 mb-3">QR code de cette carte</p>
            <img
                src="{{ route('card.qr', $employee->slug) }}"
                alt="QR code de {{ $employee->fullName() }}"
                class="w-32 h-32 mx-auto"
            >
            <a
                href="{{ route('card.qr', $employee->slug) }}"
                download="qr-{{ $employee->slug }}.png"
                class="inline-block mt-3 text-xs text-[#003189] hover:underline"
            >
                Télécharger le QR
            </a>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 px-6 py-3 text-center">
            @if($employee->website)
                <a href="{{ $employee->website }}" target="_blank" rel="noopener" class="text-xs text-[#003189] hover:underline">
                    {{ $employee->website }}
                </a>
            @endif
        </div>

    </div>
</div>
@endsection
