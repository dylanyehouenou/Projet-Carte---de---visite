@props(['category' => null, 'name' => 'media_id', 'value' => null, 'label' => 'Choisir un média'])

<div x-data="mediaPicker('{{ $category }}', {{ $value ?? 'null' }})" x-init="init()">
    {{-- Hidden input --}}
    <input type="hidden" name="{{ $name }}" :value="selectedId">

    {{-- Trigger button --}}
    <div class="flex items-center gap-3">
        <div x-show="selectedId" class="w-16 h-16 rounded-xl border border-slate-200 overflow-hidden bg-slate-50 flex-shrink-0">
            <img :src="selectedUrl" alt="Média sélectionné" class="w-full h-full object-contain p-1">
        </div>
        <button type="button" @click="open = true" class="text-sm font-medium text-[#003189] hover:underline">
            {{ $label }}
        </button>
        <button x-show="selectedId" type="button" @click="clear()" class="text-slate-400 hover:text-red-500 text-xs transition-colors">Retirer</button>
    </div>

    {{-- Modal --}}
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @keydown.escape.window="open = false" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[80vh] flex flex-col" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Médiathèque</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Upload inline --}}
            <div class="px-5 py-3 border-b border-slate-50 bg-slate-50">
                <form @submit.prevent="uploadInline($event)" class="flex items-end gap-3 flex-wrap">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Fichier</label>
                        <input type="file" name="file" accept="image/png,image/jpeg,image/webp" class="text-xs text-slate-500 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700">
                    </div>
                    <input type="hidden" name="category" :value="filterCategory || 'image'">
                    <button type="submit" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-3 py-1.5 rounded-lg transition-colors">Uploader</button>
                </form>
            </div>

            {{-- Grid --}}
            <div class="flex-1 overflow-y-auto p-5">
                <div x-show="loading" class="flex items-center justify-center py-12 text-slate-400">Chargement…</div>
                <div x-show="!loading" class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                    <template x-for="item in items" :key="item.id">
                        <div
                            @click="select(item)"
                            :class="selectedId === item.id ? 'ring-2 ring-[#003189]' : 'hover:ring-2 hover:ring-blue-300'"
                            class="relative bg-slate-50 rounded-xl cursor-pointer overflow-hidden aspect-square border border-slate-100 transition-all"
                        >
                            <img :src="item.url" :alt="item.name" class="w-full h-full object-contain p-1">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <p class="text-white text-xs truncate" x-text="item.name"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="!loading && items.length === 0" class="col-span-full py-8 text-center text-slate-400 text-sm">Aucun média.</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mediaPicker(filterCategory, initialId) {
    return {
        open: false,
        loading: false,
        items: [],
        selectedId: initialId,
        selectedUrl: null,
        filterCategory,

        init() {
            this.$watch('open', v => { if (v) this.load(); });
            if (initialId) {
                this.selectedUrl = `/admin/media/${initialId}/thumb`;
            }
        },

        async load() {
            this.loading = true;
            try {
                const url = new URL('/admin/media', window.location.origin);
                if (this.filterCategory) url.searchParams.set('category', this.filterCategory);
                const res = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.items = data.data ?? [];
            } catch (e) {
                this.items = [];
            }
            this.loading = false;
        },

        select(item) {
            this.selectedId = item.id;
            this.selectedUrl = item.url;
            this.$dispatch('selected', { id: item.id, url: item.url, name: item.name });
            this.open = false;
        },

        clear() {
            this.selectedId = null;
            this.selectedUrl = null;
            this.$dispatch('selected', null);
        },

        async uploadInline(e) {
            const form = new FormData(e.target);
            const res = await fetch('/admin/media', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: form,
            });
            if (res.ok) {
                const data = await res.json();
                this.items.unshift(data);
                this.select(data);
            }
        }
    };
}
</script>
@endpush
