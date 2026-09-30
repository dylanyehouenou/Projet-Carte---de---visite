@extends('layouts.admin')

@section('title', 'Cartes')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Cartes</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $cards->total() }} carte(s)</p>
    </div>
    <a href="{{ route('admin.cards.create') }}" class="inline-flex items-center gap-2 bg-[#003189] hover:bg-[#0047c8] text-white font-semibold px-4 py-2 rounded-xl transition-colors text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle carte
    </a>
</div>

<form method="GET" class="mb-6">
    <select name="status" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white">
        <option value="">Tous les statuts</option>
        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
        <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Désactivé</option>
    </select>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Nom</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Organisation</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Groupe</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Statut</th>
                    <th class="text-left px-6 py-4 font-semibold text-slate-600">Créée le</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($cards as $card)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $card->name }}</td>
                    <td class="px-6 py-4 text-slate-500 text-xs">{{ $card->organization?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-500 text-xs">{{ $card->group?->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @php $statusMap = ['draft' => ['bg-amber-50 text-amber-700', 'Brouillon'], 'published' => ['bg-emerald-50 text-emerald-700', 'Publié'], 'disabled' => ['bg-slate-100 text-slate-500', 'Désactivé']]; @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $statusMap[$card->status][0] ?? '' }}">{{ $statusMap[$card->status][1] ?? $card->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $card->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.cards.builder', $card) }}" class="text-[#003189] hover:underline text-xs font-semibold">Éditer</a>
                            <a href="{{ route('admin.cards.preview', $card) }}" target="_blank" class="text-slate-400 hover:text-slate-600 text-xs">Aperçu</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">Aucune carte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $cards->links() }}</div>
@endsection
