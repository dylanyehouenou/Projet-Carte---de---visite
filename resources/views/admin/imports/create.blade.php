@extends('layouts.admin')
@section('title', 'Importer un CSV')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.imports.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour aux imports
    </a>
</div>

<div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 max-w-lg">
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Importer un CSV Signitic</h1>
    <p class="text-sm font-medium text-slate-500 leading-relaxed mb-8">
        Format CSV, séparateur point-virgule. Les collaborateurs existants seront mis à jour, les nouveaux créés. Aucun absent ne sera supprimé automatiquement.
    </p>

    <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <div class="border-2 border-dashed border-slate-200 bg-slate-50/50 rounded-2xl p-10 text-center hover:border-[#003189]/50 hover:bg-blue-50/20 transition-all group">
                <input type="file" name="csv_file" accept=".csv,.txt" required id="csv_input" class="hidden">
                <label for="csv_input" class="cursor-pointer block w-full h-full">
                    <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:text-[#003189] transition-transform text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-600 group-hover:text-[#003189] transition-colors">Cliquez pour sélectionner un fichier</p>
                    <p class="text-[0.7rem] font-semibold uppercase tracking-widest text-slate-400 mt-2">Max 10 Mo (.csv ou .txt)</p>
                </label>
                <p id="csv_name" class="text-sm font-bold text-[#003189] mt-4 py-2 px-4 bg-blue-50 rounded-lg inline-block hidden border border-blue-100"></p>
            </div>
            @error('csv_file')<p class="text-red-500 text-xs font-semibold mt-2">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full bg-[#003189] hover:bg-[#002266] text-white rounded-xl py-3.5 text-sm font-bold shadow-lg shadow-[#003189]/20 transition-all active:scale-[0.98]">
            Lancer l'importation
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
