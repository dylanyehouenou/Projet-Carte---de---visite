@extends('layouts.admin')
@section('title', 'Modifier ' . $employee->fullName())
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.employees.show', $employee) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-slate-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour au profil
    </a>
</div>

<div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 max-w-2xl">
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-8">Modifier le profil</h1>

    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Prénom <span class="text-red-400">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all @error('first_name') border-red-400 ring-red-400/20 @enderror">
                @error('first_name')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Nom <span class="text-red-400">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all @error('last_name') border-red-400 @enderror">
                @error('last_name')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Fonction</label>
                <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Service</label>
                <input type="text" name="department" value="{{ old('department', $employee->department) }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all">
            </div>
        </div>

        <div>
            <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">LinkedIn</label>
            <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $employee->linkedin_url) }}" placeholder="https://linkedin.com/in/..."
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all @error('linkedin_url') border-red-400 @enderror">
            @error('linkedin_url')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Calendly</label>
            <input type="url" name="calendly_url" value="{{ old('calendly_url', $employee->calendly_url) }}" placeholder="https://calendly.com/..."
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all @error('calendly_url') border-red-400 @enderror">
            @error('calendly_url')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-[0.7rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Photo de profil</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#003189] hover:file:bg-blue-100 file:transition-colors file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1">
            <p class="text-[0.7rem] text-slate-400 font-medium mt-2">JPEG, PNG ou WebP — max 2 Mo</p>
            @error('photo')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap gap-4 pt-6 mt-4 border-t border-slate-100">
            <button type="submit" class="bg-[#003189] hover:bg-[#002266] text-white rounded-xl px-6 py-3 text-sm font-semibold shadow-md shadow-[#003189]/20 transition-all active:scale-[0.98]">
                Enregistrer les modifications
            </button>
            <a href="{{ route('admin.employees.show', $employee) }}" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-6 py-3 text-sm font-semibold shadow-sm transition-all active:scale-[0.98]">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
