@extends('layouts.admin')

@section('title', 'Médiathèque')

@section('content')
<div x-data="mediaLibrary()" x-init="init()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Médiathèque</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $media->total() }} fichier(s)</p>
        </div>
        <button @click="showUpload = !showUpload" class="inline-flex items-center gap-2 bg-[#003189] hover:bg-[#0047c8] text-white font-semibold px-4 py-2 rounded-xl transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Uploader un média
        </button>
    </div>

    {{-- Upload form --}}
    <div x-show="showUpload" x-transition class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-slate-900 mb-4">Nouveau média</h2>
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="flex flex-wrap gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Fichier</label>
                <input type="file" name="file" accept="image/png,image/jpeg,image/webp" required class="block text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-slate-400 mt-1">PNG, JPEG, WebP — max 10 Mo</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catégorie</label>
                <select name="category" required class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white">
                    <option value="image">Image</option>
                    <option value="logo">Logo</option>
                    <option value="banner">Bannière</option>
                    <option value="photo">Photo</option>
                    <option value="other">Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nom (optionnel)</label>
                <input type="text" name="name" placeholder="Nom du fichier" class="border border-slate-200 rounded-xl px-3 py-2 text-sm w-48">
            </div>
            <button type="submit" class="bg-[#003189] text-white font-semibold px-5 py-2 rounded-xl text-sm hover:bg-[#0047c8] transition-colors">
                Uploader
            </button>
        </form>
    </div>

    {{-- Filters --}}
    <form method="GET" class="mb-6 flex flex-wrap gap-3">
        @foreach(['' => 'Tous', 'logo' => 'Logos', 'banner' => 'Bannières', 'photo' => 'Photos', 'image' => 'Images', 'other' => 'Autres'] as $val => $label)
            <button type="submit" name="category" value="{{ $val }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ request('category', '') === $val ? 'bg-[#003189] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-300' }}">
                {{ $label }}
            </button>
        @endforeach
        <div class="ml-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="border border-slate-200 rounded-xl px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4">
        @forelse($media as $item)
        <div class="group relative bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="aspect-square bg-slate-50 overflow-hidden">
                <img src="{{ route('admin.media.thumb', $item) }}" alt="{{ $item->name }}" class="w-full h-full object-contain p-2">
            </div>
            <div class="p-2">
                <p class="text-xs font-medium text-slate-700 truncate">{{ $item->name }}</p>
                <p class="text-xs text-slate-400">{{ $item->formattedSize() }}</p>
                <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-xs bg-slate-100 text-slate-500">{{ $item->category }}</span>
            </div>
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <a href="{{ route('admin.media.show', $item) }}" target="_blank" class="bg-white text-slate-700 rounded-lg p-1.5 hover:bg-slate-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Supprimer ce média ?')">
                    @csrf @method('DELETE')
                    <button type="submit" aria-label="Supprimer {{ $item->name }}" class="bg-white text-red-500 rounded-lg p-1.5 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center text-slate-400">Aucun média.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $media->links() }}</div>
</div>

@push('scripts')
<script>
function mediaLibrary() {
    return { showUpload: {{ $errors->any() ? 'true' : 'false' }}, init() {} };
}
</script>
@endpush
@endsection
