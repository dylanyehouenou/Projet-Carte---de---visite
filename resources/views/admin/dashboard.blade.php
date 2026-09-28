@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-8">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-500">Total collaborateurs</p>
        <p class="text-4xl font-bold text-[#003189] mt-1">{{ $total }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-500">Cartes actives</p>
        <p class="text-4xl font-bold text-green-600 mt-1">{{ $active }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-500">Cartes désactivées</p>
        <p class="text-4xl font-bold text-gray-400 mt-1">{{ $inactive }}</p>
    </div>
</div>

@if($lastImport)
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="font-semibold text-gray-700 mb-2">Dernier import CSV</h2>
    <p class="text-sm text-gray-600">
        <span class="font-medium">{{ $lastImport->filename }}</span>
        — {{ $lastImport->created_at->diffForHumans() }}
    </p>
    <p class="text-xs text-gray-400 mt-1">
        {{ $lastImport->rows_created }} créés · {{ $lastImport->rows_updated }} mis à jour
        · {{ $lastImport->rows_missing }} absents · {{ $lastImport->rows_failed }} erreurs
    </p>
    <a href="{{ route('admin.imports.show', $lastImport) }}" class="text-xs text-[#003189] hover:underline mt-2 inline-block">
        Voir le rapport →
    </a>
</div>
@endif

<div class="flex gap-4">
    <a href="{{ route('admin.employees.index') }}"
       class="bg-[#003189] hover:bg-[#002070] text-white rounded-lg px-5 py-2.5 text-sm font-semibold transition">
        Gérer les collaborateurs
    </a>
    <a href="{{ route('admin.imports.create') }}"
       class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-semibold transition">
        Importer un CSV
    </a>
</div>
@endsection
