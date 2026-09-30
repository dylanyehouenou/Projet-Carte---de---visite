@extends('layouts.admin')

@section('title', 'Galerie de templates')

@section('content')
<div x-data="{ preview: null }">

    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.cards.create') }}" class="text-sm text-slate-400 hover:text-slate-600 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Nouvelle carte
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Galerie de templates</h1>
        </div>
    </div>

    {{-- Category filters --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('admin.cards.templates') }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-[#003189] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-300' }}">Tous</a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.cards.templates', ['category' => $cat]) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ request('category') === $cat ? 'bg-[#003189] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-300' }}">{{ ucfirst($cat) }}</a>
        @endforeach
    </div>

    {{-- Templates grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($templates as $tpl)
        <div class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden cursor-pointer">
            <div class="aspect-[390/844] bg-gradient-to-br from-[#003189] to-[#0047c8] overflow-hidden relative">
                @if($tpl->thumbnail)
                    <img src="{{ route('admin.media.thumb', $tpl->thumbnail) }}" alt="Template {{ $tpl->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white/30 text-4xl">📇</div>
                @endif

                {{-- Hover overlay --}}
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-3 p-4">
                    <form method="POST" action="{{ route('admin.cards.from-template') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="template_id" value="{{ $tpl->id }}">
                        <button type="submit" aria-label="Utiliser template {{ $tpl->name }}" class="w-full bg-white text-slate-900 font-semibold py-2 rounded-xl text-sm hover:bg-slate-100 transition-colors">
                            Utiliser ce modèle
                        </button>
                    </form>
                    <button @click.stop="preview = {{ $tpl->id }}" aria-label="Aperçu template {{ $tpl->name }}" class="w-full bg-white/20 text-white font-medium py-2 rounded-xl text-sm hover:bg-white/30 transition-colors">
                        Aperçu
                    </button>
                </div>
            </div>

            <div class="p-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-900">{{ $tpl->name }}</span>
                    @if($tpl->is_system)
                        <span class="text-xs px-1.5 py-0.5 bg-amber-50 text-amber-700 rounded font-medium">Système</span>
                    @endif
                </div>
                <span class="text-xs text-slate-400">{{ ucfirst($tpl->category) }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center text-slate-400">Aucun template disponible.</div>
        @endforelse
    </div>

    {{-- Preview modal --}}
    <div x-show="preview !== null" x-transition.opacity class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" @click.self="preview = null" role="dialog" aria-modal="true" @keydown.escape.window="preview = null">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-900">Aperçu</h3>
                <button @click="preview = null" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-auto max-h-[70vh]">
                <template x-if="preview">
                    <iframe :src="'/admin/cards/' + preview + '/preview'" class="w-full" style="height: 600px; border: none;" loading="lazy"></iframe>
                </template>
            </div>
            <div class="p-4 border-t border-slate-100">
                <form method="POST" action="{{ route('admin.cards.from-template') }}">
                    @csrf
                    <input type="hidden" name="template_id" :value="preview">
                    <button type="submit" class="w-full bg-[#003189] text-white font-semibold py-2.5 rounded-xl text-sm hover:bg-[#0047c8] transition-colors">
                        Utiliser ce modèle
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
