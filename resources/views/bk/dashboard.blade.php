@extends('layouts.app')

@section('title', 'Dashboard Guru BK')

@section('content')
<div class="space-y-8">
    
    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400">Workspace Guru BK</span>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Priority Dashboard</h1>
            <p class="text-slate-400 text-sm mt-1">Pantau perkembangan murid, analisis problem psikologis, dan berikan bimbingan terarah.</p>
        </div>
        
        <!-- Filter Kelas -->
        <form action="{{ route('bk.dashboard') }}" method="GET" class="flex gap-2 items-center w-full md:w-auto">
            <select name="class_id" onchange="this.form.submit()" 
                class="appearance-none block w-full md:w-48 px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->major->code }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Count Bento Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card p-5 rounded-2xl border border-slate-800">
            <span class="text-xs uppercase font-bold text-slate-400 block">Total Murid Dipantau</span>
            <span class="text-3xl font-extrabold text-white mt-1 block">{{ $totalStudents }}</span>
        </div>
        <div class="glass-card p-5 rounded-2xl border border-slate-800">
            <span class="text-xs uppercase font-bold text-slate-400 block">Sesi Konseling Berjalan</span>
            <span class="text-3xl font-extrabold text-emerald-400 mt-1 block">{{ $totalConversations }}</span>
        </div>
        <div class="glass-card p-5 rounded-2xl border border-slate-800">
            <span class="text-xs uppercase font-bold text-slate-400 block">Total Masalah Terdeteksi</span>
            <span class="text-3xl font-extrabold text-rose-400 mt-1 block">{{ $totalProblemsLogged }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Left: Priority Students List (2 Cols) -->
        <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-white">Murid Prioritas (Butuh Perhatian)</h3>
                    <p class="text-sm text-slate-400">Murid dengan indikasi stres tinggi, bullying, atau kehilangan motivasi belajar berat.</p>
                </div>
                <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold uppercase px-2.5 py-1 rounded-full">
                    {{ $priorityStudents->count() }} Terdeteksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                            <th class="py-3 px-2">Nama Murid</th>
                            <th class="py-3 px-2">Kelas</th>
                            <th class="py-3 px-2">Indikasi Masalah</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($priorityStudents as $st)
                            <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                                <td class="py-4.5 px-2">
                                    <div class="font-bold text-white">{{ $st->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $st->email }}</div>
                                </td>
                                <td class="py-4.5 px-2">
                                    <span class="bg-slate-900 border border-slate-800 px-2 py-1 rounded text-slate-300 font-semibold">{{ $st->schoolClass->name }}</span>
                                </td>
                                <td class="py-4.5 px-2">
                                    <div class="flex flex-wrap gap-1.5">
                                        @php
                                            // Dapatkan domain bermasalah
                                            $stProblems = $st->confidenceScores->filter(fn($sc) => $sc->domain->category === 'problem' && $sc->score > 55);
                                        @endphp
                                        @forelse($stProblems as $sp)
                                            <span class="bg-rose-500/10 text-rose-400 border border-rose-500/10 px-2 py-0.5 rounded text-xs font-bold">
                                                {{ $sp->domain->name }} ({{ $sp->score }}%)
                                            </span>
                                        @empty
                                            <span class="bg-amber-500/10 text-amber-400 border border-amber-500/10 px-2 py-0.5 rounded text-xs font-bold">
                                                Motivasi Rendah
                                            </span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-4.5 px-2 text-right">
                                    <a href="{{ route('bk.student.detail', $st->id) }}" 
                                        class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-3 py-2 rounded-lg text-xs transition inline-block min-h-[36px] leading-normal">
                                        Periksa Detail AI
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 italic">Tidak ada murid berstatus prioritas saat ini. Kerja bagus!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Problem & Career Charts/Heatmaps (1 Col) -->
        <div class="space-y-6">
            
            <!-- Heatmap Problem -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Heatmap Keluhan Murid</h3>
                <div class="space-y-3.5">
                    @foreach($problemDistribution as $prob)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-300 font-medium">{{ $prob['name'] }}</span>
                                <span class="font-bold text-slate-400">{{ $prob['count'] }} Murid</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-1.5">
                                <div class="bg-rose-500 h-1.5 rounded-full" 
                                    style="width: {{ $totalStudents > 0 ? ($prob['count'] / $totalStudents) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Career Distribution -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Peta Rencana Karir</h3>
                <div class="space-y-3">
                    @foreach($careerCounts as $label => $val)
                        <div class="flex justify-between items-center text-xs bg-slate-900/50 p-2.5 rounded-xl border border-slate-800">
                            <span class="font-semibold text-slate-400">{{ $label }}</span>
                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded text-xs font-bold">
                                {{ $val }} Murid
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>

    </div>

    <!-- Complete Student List Table -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-lg font-bold text-white">Daftar Seluruh Murid</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                        <th class="py-3 px-2">Nama Murid</th>
                        <th class="py-3 px-2">Kelas & Jurusan</th>
                        <th class="py-3 px-2">Email</th>
                        <th class="py-3 px-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                            <td class="py-3.5 px-2 font-bold text-white">{{ $st->name }}</td>
                            <td class="py-3.5 px-2">
                                @if($st->schoolClass)
                                    {{ $st->schoolClass->name }} - <span class="text-slate-400">{{ $st->schoolClass->major->name }}</span>
                                @else
                                    <span class="text-rose-400 italic">Belum disetting</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-2 text-slate-400 font-mono">{{ $st->email }}</td>
                            <td class="py-3.5 px-2 text-right">
                                <a href="{{ route('bk.student.detail', $st->id) }}" 
                                class="border border-slate-700 bg-slate-900 hover:bg-slate-800 text-white font-bold px-3 py-2 rounded-lg text-xs transition inline-block min-h-[36px] leading-normal">
                                Lihat File BK
                            </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 italic">Tidak ada murid terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
