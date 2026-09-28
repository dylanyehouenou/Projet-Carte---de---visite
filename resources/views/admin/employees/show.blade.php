@extends('layouts.admin')

@section('title', $employee->fullName())

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.employees.index') }}" class="text-gray-400 hover:text-gray-600">← Collaborateurs</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Infos --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $employee->fullName() }}</h1>
                @if($employee->job_title)<p class="text-gray-500 mt-0.5">{{ $employee->job_title }}</p>@endif
                @if($employee->department)<p class="text-gray-400 text-sm">{{ $employee->department }}</p>@endif
            </div>
            <div class="flex items-center gap-2">
                @if($employee->is_active)
                    <span class="bg-green-100 text-green-700 text-xs rounded-full px-3 py-1">Actif</span>
                @else
                    <span class="bg-gray-100 text-gray-500 text-xs rounded-full px-3 py-1">Désactivé</span>
                @endif
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            @if($employee->email)
            <div><dt class="text-gray-400 text-xs uppercase mb-0.5">Email</dt><dd class="text-gray-800">{{ $employee->email }}</dd></div>
            @endif
            @if($employee->phone)
            <div><dt class="text-gray-400 text-xs uppercase mb-0.5">Téléphone</dt><dd class="text-gray-800">{{ $employee->phone }}</dd></div>
            @endif
            @if($employee->postal_address)
            <div class="sm:col-span-2"><dt class="text-gray-400 text-xs uppercase mb-0.5">Adresse</dt><dd class="text-gray-800">{{ $employee->postal_address }}</dd></div>
            @endif
            @if($employee->linkedin_url)
            <div><dt class="text-gray-400 text-xs uppercase mb-0.5">LinkedIn</dt><dd><a href="{{ $employee->linkedin_url }}" target="_blank" class="text-[#003189] hover:underline truncate block">{{ $employee->linkedin_url }}</a></dd></div>
            @endif
            @if($employee->calendly_url)
            <div><dt class="text-gray-400 text-xs uppercase mb-0.5">Calendly</dt><dd><a href="{{ $employee->calendly_url }}" target="_blank" class="text-[#003189] hover:underline truncate block">{{ $employee->calendly_url }}</a></dd></div>
            @endif
        </dl>

        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.employees.edit', $employee) }}"
               class="bg-[#003189] hover:bg-[#002070] text-white rounded-lg px-4 py-2 text-sm font-semibold transition">
                Modifier
            </a>
            <a href="{{ route('card.show', $employee->slug) }}" target="_blank"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold transition">
                Voir la carte
            </a>
            <form method="POST" action="{{ route('admin.employees.toggle', $employee) }}" class="ml-auto">
                @csrf
                <button type="submit"
                    class="{{ $employee->is_active ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} border border-current rounded-lg px-4 py-2 text-sm font-semibold transition"
                    onclick="return confirm('{{ $employee->is_active ? 'Désactiver cette carte ?' : 'Activer cette carte ?' }}')"
                >
                    {{ $employee->is_active ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
        </div>
    </div>

    {{-- QR + URL --}}
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-sm font-medium text-gray-600 mb-4">QR Code</p>
        <img src="{{ route('card.qr', $employee->slug) }}" alt="QR" class="w-40 h-40 mx-auto mb-4">
        <a href="{{ route('card.qr', $employee->slug) }}"
           download="qr-{{ $employee->slug }}.png"
           class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg py-2 text-sm font-semibold transition mb-3">
            Télécharger PNG
        </a>
        <p class="text-xs text-gray-400 break-all">{{ $employee->publicUrl() }}</p>
    </div>

</div>
@endsection
