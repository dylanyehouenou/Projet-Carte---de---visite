@props(['name', 'label', 'value' => '#003189'])

<div x-data="{ color: '{{ $value }}' }">
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1">{{ $label }}</label>
    <div class="flex items-center gap-3">
        <div class="relative">
            <input
                type="color"
                id="{{ $name }}_picker"
                x-model="color"
                @input="$refs.hex.value = color"
                class="w-10 h-10 rounded-lg border border-slate-200 cursor-pointer p-0.5"
                aria-label="{{ $label }}"
            >
        </div>
        <input
            type="text"
            id="{{ $name }}"
            name="{{ $name }}"
            x-ref="hex"
            x-model="color"
            @input="$refs.picker ? $refs.picker.value = color : null"
            placeholder="#000000"
            pattern="^#[0-9A-Fa-f]{6}$"
            class="w-32 border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
    </div>
</div>
