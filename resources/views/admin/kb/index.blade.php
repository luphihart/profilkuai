@extends('layouts.app')

@section('title', 'Knowledge Base')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Home AI</a>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Knowledge Base</h1>
            <p class="text-slate-400 text-sm mt-1">Mengelola domain bimbingan sekolah, indikator psikologis, kata kunci pencocokan, dan bobot bukti.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left: Form Tambah Domain -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 h-fit space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Domain Baru</h3>
            
            <form action="{{ route('admin.kb.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Domain (e.g. Bullying)</label>
                    <input type="text" name="name" required 
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="e.g. Manajemen Waktu">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kategori Domain</label>
                    <select name="category" required 
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        <option value="personality">Personality (Kepribadian)</option>
                        <option value="interest">Interest (Minat Hobi)</option>
                        <option value="problem">Problem (Hambatan/Masalah)</option>
                        <option value="academic">Academic (Akademis Sekolah)</option>
                        <option value="career">Career (Tujuan Karir)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Deskripsi Lengkap</label>
                    <textarea name="description" required rows="3" 
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 placeholder-slate-500 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="Jelaskan definisi dari domain ini..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Indikator Perilaku (Pisahkan dengan koma)</label>
                    <input type="text" name="indicators" required 
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="e.g. Tepat waktu, Membuat jadwal belajar, Menepati janji">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kata Kunci Obrolan (Pisahkan dengan koma)</label>
                    <input type="text" name="keywords" required 
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="e.g. waktu, jam, jadwal, terlambat, nunda">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Bobot Bukti Default (0.1 - 2.0)</label>
                    <input type="number" name="evidence_weight" step="0.1" min="0.1" max="2" value="1.0" required
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-2 rounded-xl text-sm transition">
                    Simpan Domain Baru
                </button>

            </form>
        </div>

        <!-- Right: Daftar Domain (2 Cols) -->
        <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-lg font-bold text-white">Daftar Domain Aktif</h3>
            
            <div class="space-y-4 max-h-[560px] overflow-y-auto pr-2">
                @forelse($domains as $d)
                    <div class="bg-slate-900/60 p-4.5 rounded-2xl border border-slate-805 space-y-3 relative group">
                        
                        <!-- Delete absolute button -->
                        <form action="{{ route('admin.kb.delete', $d->id) }}" method="POST" class="absolute top-4 right-4">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus domain ini?')" 
                                class="text-rose-500 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-950 transition" title="Hapus">
                                🗑️
                            </button>
                        </form>

                        <div class="flex gap-2 items-center">
                            <h4 class="font-extrabold text-sm text-white">{{ $d->name }}</h4>
                            <span class="bg-slate-950 border border-slate-805 px-2 py-0.5 rounded text-xs uppercase font-bold text-emerald-400">
                                {{ $d->category }}
                            </span>
                            <span class="text-xs text-slate-500">Bobot: {{ $d->evidence_weight }}</span>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed">{{ $d->description }}</p>

                        <div class="text-xs space-y-1 text-slate-500 border-t border-slate-850 pt-2">
                            <div>
                                <strong class="text-slate-400">Indikator:</strong> 
                                {{ is_array($d->indicators) ? implode(', ', $d->indicators) : $d->indicators }}
                            </div>
                            <div>
                                <strong class="text-slate-400">Kata Kunci:</strong> 
                                <span class="font-mono text-xs bg-slate-950 px-1 py-0.5 rounded text-teal-400">
                                    {{ is_array($d->keywords) ? implode(', ', $d->keywords) : $d->keywords }}
                                </span>
                            </div>
                        </div>

                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic py-8 text-center">Belum ada domain di Knowledge Base.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
