@extends('layouts.admin')

@section('title', 'Collaborateurs')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Collaborateurs</h1>
    <a href="{{ route('admin.imports.create') }}" class="bg-[#003189] hover:bg-[#002070] text-white rounded-lg px-4 py-2 text-sm font-semibold transition">
        Importer CSV
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-6">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Rechercher nom, email, fonction…"
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003189]"
    >
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003189]">
        <option value="">Tous</option>
        <option value="active" @selected(request('status') === 'active')>Actifs</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Désactivés</option>
    </select>
    <button type="submit" class="bg-gray-800 text-white rounded-lg px-4 py-2 text-sm font-semibold">Filtrer</button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.employees.index') }}" class="text-sm text-gray-500 self-center hover:underline">Réinitialiser</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Nom</th>
                <th class="px-4 py-3 text-left">Fonction</th>
                <th class="px-4 py-3 text-left">Service</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-center">Statut</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($employees as $emp)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $emp->fullName() }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $emp->job_title ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $emp->department ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $emp->email ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    @if($emp->is_active)
                        <span class="inline-block bg-green-100 text-green-700 text-xs rounded-full px-2 py-0.5">Actif</span>
                    @else
                        <span class="inline-block bg-gray-100 text-gray-500 text-xs rounded-full px-2 py-0.5">Désactivé</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('card.show', $emp->slug) }}" target="_blank" class="text-[#003189] hover:underline text-xs mr-3">Carte</a>
                    <a href="{{ route('admin.employees.show', $emp) }}" class="text-gray-600 hover:underline text-xs">Voir</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-gray-400">Aucun collaborateur trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $employees->links() }}
</div>
@endsection
