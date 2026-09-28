@extends('layouts.admin')

@section('title', 'Importer un CSV')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.imports.index') }}" class="text-gray-400 hover:text-gray-600">← Imports</a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <h1 class="text-xl font-bold text-gray-800 mb-2">Importer un CSV Signitic</h1>
    <p class="text-sm text-gray-500 mb-6">
        Le fichier doit être au format CSV (séparateur point-virgule).
        Les collaborateurs existants seront mis à jour, les nouveaux créés.
        Aucun collaborateur absent ne sera supprimé automatiquement.
    </p>

    <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier CSV</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#003189] transition">
                <input type="file" name="csv_file" accept=".csv,.txt" required id="csv_input" class="hidden">
                <label for="csv_input" class="cursor-pointer">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">Cliquer pour sélectionner un fichier CSV</p>
                    <p class="text-xs text-gray-400 mt-1">Max 10 Mo</p>
                </label>
                <p id="csv_name" class="text-sm text-[#003189] mt-2 hidden"></p>
            </div>
            @error('csv_file')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
            class="w-full bg-[#003189] hover:bg-[#002070] text-white rounded-lg py-2.5 text-sm font-semibold transition">
            Lancer l'import
        </button>
    </form>
</div>

<script>
document.getElementById('csv_input').addEventListener('change', function() {
    const label = document.getElementById('csv_name');
    if (this.files[0]) {
        label.textContent = this.files[0].name;
        label.classList.remove('hidden');
    }
});
</script>
@endsection
