@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="space-y-8">
    
    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400">Workspace Wali Kelas</span>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">
                Kelas: <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">{{ $class ? $class->name : 'Belum Ditautkan' }}</span>
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                @if($class)
                    Jurusan: {{ $class->major->name }}
                @else
                    Hubungi Administrator untuk menautkan akun Anda ke kelas yang diampu.
                @endif
            </p>
        </div>
    </div>

    @if($class)
        <!-- Class Stats Bento -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div class="glass-card p-4 rounded-xl border border-slate-800">
                <span class="text-xs uppercase font-bold text-slate-500 block">Total Murid</span>
                <span class="text-2xl font-extrabold text-white mt-1 block">{{ $classStats['total'] }}</span>
            </div>
            <div class="glass-card p-4 rounded-xl border border-slate-800">
                <span class="text-xs uppercase font-bold text-slate-500 block">Sesi Aktif</span>
                <span class="text-2xl font-extrabold text-amber-400 mt-1 block">{{ $classStats['active'] }}</span>
            </div>
            <div class="glass-card p-4 rounded-xl border border-slate-800">
                <span class="text-xs uppercase font-bold text-slate-500 block">Sesi Selesai</span>
                <span class="text-2xl font-extrabold text-emerald-400 mt-1 block">{{ $classStats['completed'] }}</span>
            </div>
            <div class="glass-card p-4 rounded-xl border border-slate-800">
                <span class="text-xs uppercase font-bold text-slate-500 block">Belum Memulai</span>
                <span class="text-2xl font-extrabold text-slate-500 mt-1 block">{{ $classStats['not_started'] }}</span>
            </div>
        </div>

        <!-- Student Roster & Profiling Status Table -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-lg font-bold text-white">Roster Kelas & Progress Profiling</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                            <th class="py-3 px-2">Nama Murid</th>
                            <th class="py-3 px-2">Hobi</th>
                            <th class="py-3 px-2">Cita-Cita Karir</th>
                            <th class="py-3 px-2">Progress AI</th>
                            <th class="py-3 px-2 text-right">Laporan Profiling</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentData as $data)
                            <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                                <td class="py-4 px-2">
                                    <div class="font-bold text-white">{{ $data['student']->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $data['student']->email }}</div>
                                </td>
                                <td class="py-4 px-2 text-slate-300 italic">{{ $data['hobby'] }}</td>
                                <td class="py-4 px-2 font-semibold text-emerald-400">{{ $data['career_goal'] }}</td>
                                <td class="py-4 px-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold 
                                            {{ $data['status'] === 'completed' ? 'bg-emerald-500/10 text-emerald-400' : ($data['status'] === 'active' ? 'bg-amber-500/10 text-amber-400' : 'bg-slate-900 text-slate-500') }}">
                                            {{ $data['status'] === 'completed' ? 'SELESAI' : ($data['status'] === 'active' ? 'AKTIF' : 'BELUM MULAI') }}
                                        </span>
                                        @if($data['status'] !== 'inactive')
                                            <span class="text-xs text-slate-400">(Tahap {{ $data['stage'] }}/12)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-2 text-right">
                                    @if($data['report'])
                                        <a href="{{ route('wali.student.report', $data['student']->id) }}" 
                                            class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-3 py-1.5 rounded-lg text-xs transition inline-block">
                                            Buka Laporan
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-500 italic">Menunggu sesi obrolan selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 italic">Belum ada murid yang bergabung di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="glass-panel p-12 rounded-3xl border border-slate-800 text-center space-y-4">
            <span class="text-4xl">🏫</span>
            <h3 class="text-lg font-bold text-white">Akun Belum Terhubung dengan Kelas</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto">Untuk dapat memantau murid, hubungi Administrator untuk mengatur tautan 'Wali Kelas' pada menu Kelas di Panel Admin.</p>
        </div>
    @endif

</div>
@endsection
