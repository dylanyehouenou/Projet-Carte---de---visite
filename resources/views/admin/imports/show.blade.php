@extends('layouts.admin')

@section('title', 'Rapport d\'import')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.imports.index') }}" class="text-gray-400 hover:text-gray-600">← Imports</a>
</div>

<h1 class="text-2xl font-bold text-gray-800 mb-1">Rapport d'import</h1>
<p class="text-gray-500 text-sm mb-6">{{ $import->filename }} — {{ $import->created_at->format('d/m/Y à H:i') }}</p>

{{-- Summary --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xs text-gray-400 uppercase mb-1">Total</p>
        <p class="text-3xl font-bold text-gray-700">{{ $import->rows_total }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xs text-green-500 uppercase mb-1">Créés</p>
        <p class="text-3xl font-bold text-green-600">{{ $import->rows_created }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xs text-blue-500 uppercase mb-1">Mis à jour</p>
        <p class="text-3xl font-bold text-blue-600">{{ $import->rows_updated }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xs text-orange-400 uppercase mb-1">Absents</p>
        <p class="text-3xl font-bold text-orange-500">{{ $import->rows_missing }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xs text-red-400 uppercase mb-1">Erreurs</p>
        <p class="text-3xl font-bold text-red-500">{{ $import->rows_failed }}</p>
    </div>
</div>

@php $report = $import->report_json ?? []; @endphp

{{-- Details --}}
@foreach(['created' => ['Créés', 'green'], 'updated' => ['Mis à jour', 'blue'], 'unchanged' => ['Inchangés', 'gray'], 'missing' => ['Absents du CSV', 'orange'], 'failed' => ['Erreurs', 'red']] as $key => [$label, $color])
    @if(!empty($report[$key]))
    <div class="bg-white rounded-xl shadow p-5 mb-4">
        <h2 class="font-semibold text-{{ $color }}-600 mb-3">{{ $label }} ({{ count($report[$key]) }})</h2>
        <ul class="space-y-1 text-sm text-gray-700">
            @foreach($report[$key] as $entry)
                <li class="flex gap-2">
                    @if(isset($entry['name']))
                        <span class="font-medium">{{ $entry['name'] }}</span>
                    @endif
                    @if(isset($entry['reason']))
                        <span class="text-gray-400">— {{ $entry['reason'] }}</span>
                    @endif
                    @if(isset($entry['row']))
                        <span class="text-gray-400">ligne {{ $entry['row'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    @endif
@endforeach
@endsection
