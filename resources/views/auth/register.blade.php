@extends('layouts.app')

@section('title', 'Registrasi Murid')

@section('content')
<div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 glass-panel p-8 sm:p-10 rounded-3xl border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <!-- Decorative Background Gradients -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center">
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">Buat Akun Murid</span>
            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white">Gabung Profilku AI</h2>
            <p class="mt-2 text-sm text-slate-400">
                Temukan minat, hobi, dan potensi masa depanmu bersama mentor AI.
            </p>
        </div>

        <form class="mt-8 space-y-5" action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required 
                        class="appearance-none relative block w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/60 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm" 
                        placeholder="Budi Santoso" value="{{ old('name') }}">
                    @error('name')
                        <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-400 mb-1.5">Alamat Email</label>
                    <input id="email" name="email" type="email" required 
                        class="appearance-none relative block w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/60 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm" 
                        placeholder="budi@murid.sch.id" value="{{ old('email') }}">
                    @error('email')
                        <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="class_id" class="block text-sm font-semibold text-slate-400 mb-1.5">Kelas & Jurusan</label>
                    <select id="class_id" name="class_id" required 
                        class="appearance-none relative block w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/60 text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm">
                        <option value="">Pilih Kelas Anda</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} - {{ $c->major->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-400 mb-1.5">Kata Sandi</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none relative block w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/60 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm" 
                        placeholder="Minimal 6 karakter">
                    @error('password')
                        <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-400 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                        class="appearance-none relative block w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/60 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm" 
                        placeholder="Ketik ulang kata sandi">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-slate-950 bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 focus:ring-emerald-500 transition shadow-lg shadow-emerald-500/10">
                    Daftar Sekarang
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-sm">
            <span class="text-slate-500">Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="font-bold text-emerald-400 hover:text-emerald-300 ml-1 transition">Masuk di sini</a>
        </div>

    </div>
</div>
@endsection
