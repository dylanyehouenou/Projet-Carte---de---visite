@extends('layouts.admin')

@section('title', 'Builder — ' . $card->name)

@section('content')
<div
    x-data="cardBuilder(@js($card->id), @js($config))"
    x-init="init()"
    class="flex flex-col"
    style="height: calc(100vh - 80px)"
>
    {{-- Toolbar --}}
    <div class="flex items-center justify-between bg-white border-b border-slate-100 px-6 py-3 flex-shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.cards.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="font-bold text-slate-900 text-sm">{{ $card->name }}</h1>
                <p class="text-xs text-slate-400" x-text="saveStatus"></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Add element dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-3 py-1.5 rounded-lg text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajouter
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute top-full left-0 mt-1 bg-white rounded-xl shadow-lg border border-slate-100 py-1 w-48 z-50">
                    @foreach(['text' => 'Texte', 'name' => 'Nom', 'job_title' => 'Poste', 'department' => 'Service', 'email' => 'Email', 'phone' => 'Téléphone', 'linkedin' => 'LinkedIn', 'image' => 'Image', 'logo' => 'Logo', 'photo' => 'Photo', 'qr' => 'QR Code', 'button' => 'Bouton', 'separator' => 'Séparateur'] as $type => $label)
                    <button @click="addElement('{{ $type }}'); open = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Undo/Redo --}}
            <div class="flex items-center gap-1">
                <button @click="undo()" :disabled="historyIndex <= 0" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                </button>
                <button @click="redo()" :disabled="historyIndex >= history.length - 1" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"/></svg>
                </button>
            </div>

            <a href="{{ route('admin.cards.preview', $card) }}" target="_blank" class="text-sm text-slate-500 hover:text-slate-700 font-medium border border-slate-200 px-3 py-1.5 rounded-lg transition-colors">Aperçu</a>

            <form method="POST" action="{{ route('admin.cards.publish', $card) }}">
                @csrf
                <button type="submit" class="bg-[#003189] hover:bg-[#0047c8] text-white font-semibold px-4 py-1.5 rounded-lg text-sm transition-colors">Publier</button>
            </form>
        </div>
    </div>

    {{-- Main area --}}
    <div class="flex flex-1 overflow-hidden">
        {{-- Layers sidebar --}}
        <div class="w-56 bg-white border-r border-slate-100 flex flex-col flex-shrink-0 overflow-y-auto">
            <div class="px-4 py-3 border-b border-slate-50">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Calques</h2>
            </div>
            <div class="flex-1 py-2">
                <template x-for="el in [...config.elements].reverse()" :key="el.id">
                    <div
                        @click="selectElement(el.id)"
                        :class="selectedId === el.id ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50'"
                        class="flex items-center gap-2 px-4 py-2 text-xs cursor-pointer transition-colors"
                    >
                        <span x-text="el.type" class="capitalize"></span>
                        <button @click.stop="toggleHidden(el.id)" class="ml-auto text-slate-300 hover:text-slate-500" :title="el.hidden ? 'Afficher' : 'Masquer'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="el.hidden ? 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Canvas area --}}
        <div class="flex-1 bg-slate-200 overflow-auto flex items-center justify-center p-8">
            <div
                id="card-canvas"
                class="relative shadow-2xl overflow-hidden flex-shrink-0"
                :style="`width: ${config.canvas.width}px; height: ${config.canvas.height}px; background: ${canvasBg}`"
                @click.self="selectedId = null"
            >
                <template x-for="el in config.elements" :key="el.id">
                    <div
                        :data-id="el.id"
                        @click.stop="selectElement(el.id)"
                        :class="selectedId === el.id ? 'outline outline-2 outline-blue-500' : ''"
                        :style="`
                            position: absolute;
                            left: ${el.x}px;
                            top: ${el.y}px;
                            width: ${el.width}px;
                            height: ${el.height}px;
                            transform: rotate(${el.rotation}deg);
                            opacity: ${el.opacity};
                            z-index: ${el.z_index};
                            display: ${el.hidden ? 'none' : 'block'};
                            cursor: pointer;
                            font-size: ${el.style?.font_size ?? 16}px;
                            font-weight: ${el.style?.font_weight ?? '400'};
                            color: ${el.style?.color ?? '#111827'};
                            text-align: ${el.style?.text_align ?? 'left'};
                            background-color: ${el.style?.background_color ?? 'transparent'};
                            border-radius: ${el.style?.border_radius ?? 0}px;
                        `"
                    >
                        <span x-text="el.data?.content || el.type" class="text-xs truncate block p-1"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Properties panel --}}
        <div class="w-72 bg-white border-l border-slate-100 flex flex-col flex-shrink-0 overflow-y-auto">
            <div class="px-4 py-3 border-b border-slate-50 flex items-center justify-between">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Propriétés</h2>
                <template x-if="selectedId">
                    <button @click="deleteSelected()" class="text-red-400 hover:text-red-600 text-xs">Supprimer</button>
                </template>
            </div>

            <template x-if="!selectedId">
                <div class="flex-1 flex items-center justify-center text-slate-400 text-sm p-8 text-center">
                    Sélectionnez un élément pour modifier ses propriétés
                </div>
            </template>

            <template x-if="selectedId && selectedElement">
                <div class="p-4 space-y-4">
                    {{-- Position & size --}}
                    <div>
                        <h3 class="text-xs font-semibold text-slate-500 mb-2">Position & taille</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-slate-500">X</label>
                                <input type="number" :value="selectedElement.x" @change="updateProp('x', +$event.target.value)" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Y</label>
                                <input type="number" :value="selectedElement.y" @change="updateProp('y', +$event.target.value)" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Largeur</label>
                                <input type="number" :value="selectedElement.width" @change="updateProp('width', +$event.target.value)" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Hauteur</label>
                                <input type="number" :value="selectedElement.height" @change="updateProp('height', +$event.target.value)" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                            </div>
                        </div>
                    </div>

                    {{-- Typography --}}
                    <div>
                        <h3 class="text-xs font-semibold text-slate-500 mb-2">Typographie</h3>
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-slate-500">Taille</label>
                                    <input type="number" :value="selectedElement.style?.font_size ?? 16" @change="updateStyle('font_size', +$event.target.value)" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500">Couleur</label>
                                    <input type="color" :value="selectedElement.style?.color ?? '#111827'" @input="updateStyle('color', $event.target.value)" class="w-full h-8 rounded-lg border border-slate-200 cursor-pointer">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Alignement</label>
                                <div class="flex gap-1 mt-1">
                                    @foreach(['left', 'center', 'right'] as $align)
                                    <button @click="updateStyle('text_align', '{{ $align }}')" :class="selectedElement.style?.text_align === '{{ $align }}' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600'" class="flex-1 py-1 rounded text-xs font-medium transition-colors">{{ ucfirst($align) }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Content (for text elements) --}}
                    <template x-if="selectedElement.type === 'text' || selectedElement.type === 'footer'">
                        <div>
                            <label class="text-xs text-slate-500 block mb-1">Contenu</label>
                            <textarea :value="selectedElement.data?.content" @input="updateData('content', $event.target.value)" rows="3" class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs resize-none"></textarea>
                        </div>
                    </template>

                    {{-- Opacity --}}
                    <div>
                        <label class="text-xs text-slate-500">Opacité</label>
                        <input type="range" min="0" max="1" step="0.05" :value="selectedElement.opacity ?? 1" @input="updateProp('opacity', +$event.target.value)" class="w-full mt-1">
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cardBuilder(cardId, initialConfig) {
    return {
        cardId,
        config: initialConfig && initialConfig.elements ? initialConfig : {
            version: 1,
            canvas: { width: 390, height: 844, background: { type: 'color', value: '#003189' } },
            elements: []
        },
        selectedId: null,
        saveStatus: 'Enregistré',
        saveTimer: null,
        history: [],
        historyIndex: -1,

        init() {
            this.pushHistory();
        },

        get selectedElement() {
            if (!this.selectedId) return null;
            return this.config.elements.find(e => e.id === this.selectedId);
        },

        get canvasBg() {
            const bg = this.config.canvas?.background ?? {};
            if (bg.type === 'gradient') {
                return `linear-gradient(to bottom right, ${bg.gradient?.from ?? '#003189'}, ${bg.gradient?.to ?? '#0047c8'})`;
            }
            return bg.value ?? '#003189';
        },

        addElement(type) {
            const defaults = {
                text: { content: 'Texte' },
                name: {},
                job_title: {},
                department: {},
                email: {},
                phone: {},
                linkedin: {},
                image: { media_id: null },
                logo: { media_id: null },
                photo: {},
                qr: {},
                button: { label: 'Bouton', url: '#', action: 'url' },
                separator: {},
            };
            const el = {
                id: 'el_' + Date.now(),
                type,
                x: 20, y: 20,
                width: 350, height: type === 'separator' ? 2 : 40,
                rotation: 0, opacity: 1, z_index: this.config.elements.length + 1,
                hidden: false,
                data: defaults[type] ?? {},
                style: { font_size: 16, font_weight: '400', color: '#ffffff', text_align: 'left', background_color: 'transparent', border_radius: 0 },
            };
            this.config.elements.push(el);
            this.selectedId = el.id;
            this.pushHistory();
            this.debouncedSave();
        },

        selectElement(id) {
            this.selectedId = id;
        },

        deleteSelected() {
            if (!this.selectedId) return;
            this.config.elements = this.config.elements.filter(e => e.id !== this.selectedId);
            this.selectedId = null;
            this.pushHistory();
            this.debouncedSave();
        },

        toggleHidden(id) {
            const el = this.config.elements.find(e => e.id === id);
            if (el) { el.hidden = !el.hidden; this.debouncedSave(); }
        },

        updateProp(key, value) {
            const el = this.selectedElement;
            if (el) { el[key] = value; this.debouncedSave(); }
        },

        updateStyle(key, value) {
            const el = this.selectedElement;
            if (el) { el.style = el.style ?? {}; el.style[key] = value; this.debouncedSave(); }
        },

        updateData(key, value) {
            const el = this.selectedElement;
            if (el) { el.data = el.data ?? {}; el.data[key] = value; this.debouncedSave(); }
        },

        pushHistory() {
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push(JSON.parse(JSON.stringify(this.config)));
            this.historyIndex = this.history.length - 1;
        },

        undo() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                this.config = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
                this.debouncedSave();
            }
        },

        redo() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                this.config = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
                this.debouncedSave();
            }
        },

        debouncedSave() {
            this.saveStatus = 'Modification en cours…';
            clearTimeout(this.saveTimer);
            this.saveTimer = setTimeout(() => this.save(), 1500);
        },

        async save() {
            try {
                const res = await fetch(`/admin/cards/${this.cardId}/config`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ config: this.config }),
                });
                if (res.ok) {
                    this.saveStatus = 'Enregistré';
                    this.pushHistory();
                } else {
                    this.saveStatus = 'Erreur de sauvegarde';
                }
            } catch {
                this.saveStatus = 'Erreur réseau';
            }
        }
    };
}
</script>
@endpush
@endsection
