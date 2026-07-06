@extends('layouts.app')

@section('title', 'Laporan Murid')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div>
            <a href="{{ route('wali.dashboard') }}" class="text-sm text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Roster Kelas</a>
            <h1 class="text-2xl font-extrabold text-white mt-2 tracking-tight">Laporan Profiling: {{ $student->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Kelas: {{ $student->schoolClass->name }} | Jurusan: {{ $student->schoolClass->major->name }}</p>
        </div>

        <button onclick="window.print()" class="border border-slate-700 bg-slate-900 hover:bg-slate-850 text-white font-bold px-4 py-2 rounded-xl text-sm transition">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <!-- Report Body -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6 text-slate-300 text-sm leading-relaxed" id="printable-report-area">
        
        <div class="border-b border-slate-800 pb-4">
            <h3 class="text-base font-bold text-white uppercase tracking-wider">Laporan Bimbingan & Konseling AI</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-3 bg-slate-900/60 p-5 rounded-2xl border border-slate-800/80">
                <h4 class="font-bold text-white text-sm border-b border-slate-800 pb-2 mb-2">Executive Summary</h4>
                <p>{{ $latestReport->executive_summary }}</p>
            </div>
            
            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/50">
                <h4 class="font-bold text-white border-b border-slate-800 pb-1.5 mb-2">Analisis Kepribadian</h4>
                <p>{{ $latestReport->personality_analysis }}</p>
            </div>
            
            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/50">
                <h4 class="font-bold text-white border-b border-slate-800 pb-1.5 mb-2">Kekuatan Potensi</h4>
                <p>{{ $latestReport->strengths }}</p>
            </div>
            
            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/50">
                <h4 class="font-bold text-white border-b border-slate-800 pb-1.5 mb-2">Area Pengembangan</h4>
                <p>{{ $latestReport->development_areas }}</p>
            </div>

            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/50">
                <h4 class="font-bold text-white border-b border-slate-800 pb-1.5 mb-2">Minat & Bakat</h4>
                <p>{{ $latestReport->interests }}</p>
            </div>

            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/50">
                <h4 class="font-bold text-white border-b border-slate-800 pb-1.5 mb-2">Masalah / Kendala</h4>
                <p>{{ $latestReport->problems }}</p>
            </div>

            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/50">
                <h4 class="font-bold text-white border-b border-slate-800 pb-1.5 mb-2">Cita-Cita Karir</h4>
                <p>{{ $latestReport->career_goals }}</p>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-6">
            <h4 class="font-bold text-white text-sm mb-4">Rekomendasi Tindak Lanjut Kolaboratif</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <span class="text-xs uppercase font-extrabold text-emerald-400">Untuk Murid</span>
                    <p class="mt-1.5">{{ $latestReport->student_recommendations }}</p>
                </div>
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <span class="text-xs uppercase font-extrabold text-indigo-400">Khusus Untuk Anda (Wali Kelas)</span>
                    <p class="mt-1.5 text-emerald-300 font-medium">{{ $latestReport->wali_recommendations }}</p>
                </div>
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <span class="text-xs uppercase font-extrabold text-teal-400">Untuk Guru BK</span>
                    <p class="mt-1.5">{{ $latestReport->bk_recommendations }}</p>
                </div>
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <span class="text-xs uppercase font-extrabold text-pink-400">Untuk Orang Tua</span>
                    <p class="mt-1.5">{{ $latestReport->parent_recommendations }}</p>
                </div>
            </div>
        </div>

        <div class="bg-emerald-500/5 border border-emerald-500/10 p-5 rounded-xl mt-6">
            <h4 class="font-bold text-emerald-400 text-sm border-b border-emerald-500/10 pb-2 mb-2">Rencana Follow-Up</h4>
            <p>{{ $latestReport->follow_up_plan }}</p>
        </div>

    </div>

</div>

<!-- CSS Print helper specifically for printing reports -->
<style>
    @media print {
        body * {
            visibility: hidden;
            background: transparent !important;
            color: #000 !important;
        }
        #printable-report-area, #printable-report-area * {
            visibility: visible;
        }
        #printable-report-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            font-size: 14px;
            color: #000 !important;
        }
        #printable-report-area h4 {
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .grid {
            display: block !important;
        }
        .bg-slate-900\/60, .bg-slate-900\/40, .bg-slate-900\/50, .bg-emerald-500\/5 {
            border: 1px solid #ccc !important;
            margin-bottom: 15px !important;
            padding: 15px !important;
            border-radius: 5px !important;
            background: #fff !important;
        }
    }
</style>
@endsection
