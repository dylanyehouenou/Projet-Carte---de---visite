@extends('layouts.admin')

@section('title', 'Modifier ' . $employee->fullName())

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.employees.show', $employee) }}" class="text-gray-400 hover:text-gray-600">← {{ $employee->fullName() }}</a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h1 class="text-xl font-bold text-gray-800 mb-6">Modifier {{ $employee->fullName() }}</h1>

    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none @error('first_name') border-red-400 @enderror">
                @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none @error('last_name') border-red-400 @enderror">
                @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                <input type="text" name="department" value="{{ old('department', $employee->department) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
            <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $employee->linkedin_url) }}"
                placeholder="https://linkedin.com/in/..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none @error('linkedin_url') border-red-400 @enderror">
            @error('linkedin_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Calendly</label>
            <input type="url" name="calendly_url" value="{{ old('calendly_url', $employee->calendly_url) }}"
                placeholder="https://calendly.com/..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#003189] focus:outline-none @error('calendly_url') border-red-400 @enderror">
            @error('calendly_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-100 file:text-gray-700 file:cursor-pointer hover:file:bg-gray-200">
            <p class="text-xs text-gray-400 mt-1">JPEG, PNG ou WebP — max 2 Mo</p>
            @error('photo')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="bg-[#003189] hover:bg-[#002070] text-white rounded-lg px-5 py-2.5 text-sm font-semibold transition">
                Enregistrer
            </button>
            <a href="{{ route('admin.employees.show', $employee) }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-semibold transition">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
