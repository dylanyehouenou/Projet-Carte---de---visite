@extends('layouts.admin')

@section('title', 'Imports CSV')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Imports CSV</h1>
    <a href="{{ route('admin.imports.create') }}" class="bg-[#003189] hover:bg-[#002070] text-white rounded-lg px-4 py-2 text-sm font-semibold transition">
        Nouvel import
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Fichier</th>
                <th class="px-4 py-3 text-left">Importé par</th>
                <th class="px-4 py-3 text-center">Créés</th>
                <th class="px-4 py-3 text-center">Mis à jour</th>
                <th class="px-4 py-3 text-center">Absents</th>
                <th class="px-4 py-3 text-center">Erreurs</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($imports as $run)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $run->filename }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $run->importedBy?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center text-green-700">{{ $run->rows_created }}</td>
                <td class="px-4 py-3 text-center text-blue-700">{{ $run->rows_updated }}</td>
                <td class="px-4 py-3 text-center text-orange-600">{{ $run->rows_missing }}</td>
                <td class="px-4 py-3 text-center text-red-600">{{ $run->rows_failed }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $run->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.imports.show', $run) }}" class="text-[#003189] hover:underline text-xs">Rapport</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">Aucun import.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $imports->links() }}</div>
@endsection
