@extends('layouts.app')
@section('title', 'Connexion — MMI\'e')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden">
    <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-blue-100 rounded-full blur-[100px] opacity-60"></div>
    <div class="absolute top-[60%] -right-[10%] w-[40%] h-[40%] bg-[#003189]/10 rounded-full blur-[120px]"></div>

    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-slate-100 w-full max-w-sm p-10 relative z-10">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-[#003189] tracking-tight">MMI'e</h1>
            <p class="text-slate-400 text-sm font-medium uppercase tracking-widest mt-2">Administration</p>
        </div>

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all shadow-sm @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mot de passe</label>
                <input type="password" name="password" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#003189]/20 focus:border-[#003189] transition-all shadow-sm">
            </div>
            <button type="submit"
                class="w-full bg-[#003189] hover:bg-[#002266] text-white font-bold rounded-xl py-3.5 text-sm shadow-lg shadow-[#003189]/20 transition-all active:scale-[0.98] mt-2">
                Se connecter
            </button>
        </form>
    </div>
</div>
@endsection
