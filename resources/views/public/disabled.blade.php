@extends('layouts.app')

@section('title', "Carte non disponible — MMI'e")

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#003189] to-[#0047c8] flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-10 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">Carte non disponible</h1>
        <p class="text-gray-500 text-sm">Cette carte de visite n'est plus active.</p>
        <p class="text-gray-400 text-xs mt-4">MMI'e</p>
    </div>
</div>
@endsection
