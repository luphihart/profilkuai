@extends('layouts.app')

@section('title', 'Home AI - Statistik & Pemantauan')

@section('content')
<div class="space-y-8">
    
    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400">Dasbor Utama</span>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Home AI</h1>
            <p class="text-slate-400 text-sm mt-1">Pemantauan data bimbingan, aktivitas obrolan AI, dan distribusi murid secara real-time.</p>
        </div>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6">
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Total Pengguna</span>
            <span class="text-2xl font-extrabold text-white mt-1 block">{{ $stats['users'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Murid</span>
            <span class="text-2xl font-extrabold text-emerald-400 mt-1 block">{{ $stats['students'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Guru BK & Wali</span>
            <span class="text-2xl font-extrabold text-indigo-400 mt-1 block">{{ $stats['teachers'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Kelas</span>
            <span class="text-2xl font-extrabold text-white mt-1 block">{{ $stats['classes'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">KB Domains</span>
            <span class="text-2xl font-extrabold text-teal-400 mt-1 block">{{ $stats['domains'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Aturan Alur AI</span>
            <span class="text-2xl font-extrabold text-amber-400 mt-1 block">{{ $stats['rules'] }}</span>
        </div>
    </div>

    <!-- Data & Grafik Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Line Chart: Tren Obrolan Konseling (Spans 2 cols) -->
        <div class="lg:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-white">Tren Aktivitas Konseling</h3>
                    <p class="text-xs text-slate-400">Jumlah pesan konseling AI yang dikirim dalam 7 hari terakhir.</p>
                </div>
                <span class="text-xs font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">Live Sync</span>
            </div>

            <!-- SVG Line Chart: Fully Scalable and Responsive -->
            <div class="pt-4 overflow-hidden">
                @php
                    $maxChat = max(array_column($chatActivity, 'count')) ?: 100;
                    $maxChat = ceil($maxChat / 10) * 10;
                    
                    $points = [];
                    $fillPoints = ["40,160"];
                    foreach ($chatActivity as $index => $item) {
                        $x = 40 + ($index * 90);
                        $y = 160 - ($item['count'] / $maxChat * 110);
                        $points[] = "{$x},{$y}";
                        $fillPoints[] = "{$x},{$y}";
                    }
                    $fillPoints[] = (40 + (6 * 90)) . ",160";
                    $pointsStr = implode(' ', $points);
                    $fillPointsStr = implode(' ', $fillPoints);
                @endphp
                <svg viewBox="0 0 600 180" class="w-full h-48 text-emerald-500 font-sans">
                    <defs>
                        <linearGradient id="chart-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <!-- Y Axis Grid Lines -->
                    <line x1="40" y1="160" x2="580" y2="160" stroke="#1e293b" stroke-width="1"/>
                    <line x1="40" y1="105" x2="580" y2="105" stroke="#1e293b" stroke-dasharray="3"/>
                    <line x1="40" y1="50" x2="580" y2="50" stroke="#1e293b" stroke-dasharray="3"/>
                    
                    <!-- Fill Area -->
                    <polygon points="{{ $fillPointsStr }}" fill="url(#chart-grad)" />
                    
                    <!-- Polyline path -->
                    <polyline points="{{ $pointsStr }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    
                    <!-- Circles and Labels on Data Points -->
                    @foreach($chatActivity as $index => $item)
                        @php
                            $x = 40 + ($index * 90);
                            $y = 160 - ($item['count'] / $maxChat * 110);
                        @endphp
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#020617" stroke="#10b981" stroke-width="2"/>
                        <text x="{{ $x }}" y="{{ $y - 10 }}" fill="#34d399" font-size="9.5" font-weight="bold" text-anchor="middle">{{ $item['count'] }}</text>
                        <text x="{{ $x }}" y="175" fill="#64748b" font-size="9.5" text-anchor="middle">{{ $item['label'] }}</text>
                    @endforeach
                </svg>
            </div>
        </div>

        <!-- Bar Chart: Distribusi Murid per Kelas -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div>
                <h3 class="text-base font-bold text-white">Distribusi Murid per Kelas</h3>
                <p class="text-xs text-slate-400">Persentase alokasi murid bimbingan dalam kelas aktif.</p>
            </div>

            <!-- Horizontal Bar Charts -->
            <div class="space-y-4 pt-2">
                @php
                    $maxClassStudents = count($classDistribution) > 0 ? max(array_column($classDistribution, 'count')) : 10;
                @endphp
                @forelse($classDistribution as $item)
                    @php
                        $percentage = $maxClassStudents > 0 ? ($item['count'] / $maxClassStudents * 100) : 0;
                    @endphp
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-300 font-semibold">{{ $item['name'] }}</span>
                            <span class="text-emerald-400 font-bold">{{ $item['count'] }} Murid</span>
                        </div>
                        <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-850">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-400 italic text-center py-6">Belum ada data distribusi kelas.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
