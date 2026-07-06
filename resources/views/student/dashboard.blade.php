@extends('layouts.app')

@section('title', 'AI Home')

@section('content')
<div class="space-y-8" x-data="{ 
    selectedMood: localStorage.getItem('user_mood') || '',
    moodResponse: localStorage.getItem('mood_response') || '',
    setMood(mood, response) {
        this.selectedMood = mood;
        this.moodResponse = response;
        localStorage.setItem('user_mood', mood);
        localStorage.setItem('mood_response', response);
    }
}">

    <!-- Welcome Header & Mood Check -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div>
            <div class="flex items-center gap-2 text-slate-400 text-sm font-semibold">
                <span>✨</span>
                <span>Selamat datang kembali,</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mt-1 tracking-tight">
                Halo, <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">{{ $nickname }}</span>! 👋
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Ayo mengobrol dengan asisten AI-mu untuk lebih mengenali potensimu.
            </p>
        </div>

        <!-- Mood Check Card -->
        <div class="glass-card p-4 rounded-2xl w-full md:w-auto min-w-[280px]">
            <p class="text-xs font-bold text-slate-400 mb-2.5 text-center md:text-left">Bagaimana perasaanmu hari ini?</p>
            <div class="flex justify-between md:justify-start gap-3">
                <button @click="setMood('😊', 'Senang mendengarnya! Semangat jalani harimu ya! 🌟')" 
                    :class="selectedMood === '😊' ? 'scale-125 bg-emerald-500/20' : 'opacity-70 hover:opacity-100'"
                    class="p-2 rounded-xl text-2xl transition-all duration-200">😊</button>
                <button @click="setMood('🥱', 'Kurang istirahat? Sempatkan regangkan tubuh & minum air ya! 💧')" 
                    :class="selectedMood === '🥱' ? 'scale-125 bg-amber-500/20' : 'opacity-70 hover:opacity-100'"
                    class="p-2 rounded-xl text-2xl transition-all duration-200">🥱</button>
                <button @click="setMood('😐', 'Hari yang netral ya? Semoga ada kejutan menyenangkan hari ini! 🍀')" 
                    :class="selectedMood === '😐' ? 'scale-125 bg-slate-500/20' : 'opacity-70 hover:opacity-100'"
                    class="p-2 rounded-xl text-2xl transition-all duration-200">😐</button>
                <button @click="setMood('😔', 'Ada sesuatu yang mengganjal? Kamu bisa ceritakan santai ke AI-mu. Hugs! 🫂')" 
                    :class="selectedMood === '😔' ? 'scale-125 bg-indigo-500/20' : 'opacity-70 hover:opacity-100'"
                    class="p-2 rounded-xl text-2xl transition-all duration-200">😔</button>
                <button @click="setMood('😡', 'Sedang kesal? Ambil nafas dalam-dalam... Kita di sini untuk mendukungmu. ❤️')" 
                    :class="selectedMood === '😡' ? 'scale-125 bg-rose-500/20' : 'opacity-70 hover:opacity-100'"
                    class="p-2 rounded-xl text-2xl transition-all duration-200">😡</button>
            </div>
            
            <!-- Mood Response Toast -->
            <div x-show="moodResponse" x-transition class="mt-2.5 text-xs font-semibold text-emerald-400 bg-emerald-500/10 p-2 rounded-lg border border-emerald-500/10 text-center">
                <span x-text="moodResponse"></span>
            </div>
        </div>
    </div>

    <!-- BENTO GRID SYSTEM -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- 1. MAIN CARD: Continue Conversation (Span 2 Cols) -->
        <div class="md:col-span-2 glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-lg relative overflow-hidden flex flex-col justify-between group">
            <!-- Glow background -->
            <div class="absolute -right-32 -bottom-32 w-80 h-80 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none transition-all duration-500 group-hover:scale-110"></div>
            
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400">Diskusi Aktif</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                    Lanjutkan Diskusi Potensi Diri
                </h2>
                <p class="text-slate-400 text-sm max-w-lg leading-relaxed">
                    Tahapan obrolan saat ini: <strong class="text-white">{{ $currentStage }}/12 (Tahap: {{ \App\Services\ReflectionEngine::STAGES[$currentStage] ?? 'Profiling' }})</strong>.
                    Sedikit lagi kamu bisa membuka seluruh rekomendasi karir masa depanmu dari sekolah!
                </p>
                <div class="inline-flex items-center gap-3 bg-slate-900/80 border border-slate-800 px-4 py-2.5 rounded-xl text-xs text-slate-300">
                    <span class="text-emerald-400 font-bold">🎯 Misi Saat Ini:</span>
                    <span>{{ $todayMission }}</span>
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('student.chat') }}" 
                    class="bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-500 hover:to-teal-500 text-slate-950 font-extrabold px-6 py-3.5 rounded-xl text-sm transition text-center shadow-lg shadow-emerald-500/10 flex items-center justify-center gap-2">
                    <span>Lanjutkan Percakapan</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                
                @if($latestReport)
                    <a href="{{ route('student.chat') }}#report" 
                        class="border border-slate-700 bg-slate-900/60 hover:bg-slate-900 text-white font-bold px-6 py-3.5 rounded-xl text-sm transition text-center flex items-center justify-center gap-2">
                        <span>Lihat Laporan Profiling</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- 2. CARD: Growth Journey (Span 1 Col) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between">
            <div class="space-y-4">
                <span class="text-xs uppercase font-extrabold tracking-widest text-indigo-400">Weekly Progress</span>
                <h3 class="text-lg font-bold text-white">Growth Journey</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Setiap percakapan mendekatkanmu pada peta jalan karir SMK yang dipersonalisasi.
                </p>
                
                <!-- Circular/Linear Progress Bar -->
                <div class="pt-2">
                    <div class="flex justify-between text-xs font-semibold text-slate-400 mb-1.5">
                        <span>Sesi Selesai</span>
                        <span class="text-indigo-400 font-bold">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-900 rounded-full h-3 border border-slate-800">
                        <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-900 pt-4 flex justify-between items-center text-xs text-slate-500">
                <span>Tahap ke: {{ $currentStage }} / 12</span>
                <span>{{ 12 - $currentStage }} Tahap Tersisa</span>
            </div>
        </div>

        <!-- 3. CARD: Personality Snapshot (Span 1 Col) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-cyan-400">Personality Snapshot</span>
                <h3 class="text-lg font-bold text-white mt-1.5">Karakter & Potensi</h3>
                
                <div class="mt-4 space-y-3.5">
                    @forelse($scores as $sc)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-slate-300">{{ $sc->domain->name }}</span>
                                <span class="font-bold text-slate-400">{{ $sc->score }}%</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2">
                                <div class="bg-emerald-500/80 h-2 rounded-full transition-all duration-300" style="width: {{ $sc->score }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic py-4">Belum ada data keyakinan. Mulai obrolan dengan AI untuk menganalisis.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 4. CARD: AI Insight of the Day (Span 1 Col) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-amber-400">Insight Hari Ini</span>
                <h3 class="text-lg font-bold text-white mt-1.5">Quote Hari Ini</h3>
                <blockquote class="text-xs text-slate-300 italic leading-relaxed mt-3 border-l-2 border-amber-500/60 pl-3">
                    "{{ $quoteOfTheDay }}"
                </blockquote>
            </div>
            
            <div class="text-xs text-slate-500 border-t border-slate-900 pt-3">
                Direfresh otomatis setiap kali halaman dimuat.
            </div>
        </div>

        <!-- 5. CARD: Career Goal & Hobbies (Span 1 Col) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-400">Tujuan Karir & Hobi</span>
                <h3 class="text-lg font-bold text-white mt-1.5">Fokus Masa Depan</h3>
                
                <div class="mt-4 space-y-4">
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800/80">
                        <span class="text-xs uppercase font-bold text-slate-500">Cita-Cita Karir</span>
                        <p class="text-xs font-semibold text-emerald-400 mt-0.5">{{ $careerGoal }}</p>
                    </div>
                    
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800/80">
                        <span class="text-xs uppercase font-bold text-slate-500">Hobi Utama</span>
                        <p class="text-xs font-semibold text-teal-400 mt-0.5">{{ $mainHobby }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. CARD: Recommendations (Span 1 Col) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-rose-400">Rekomendasi Pintar</span>
                <h3 class="text-lg font-bold text-white mt-1.5">Rekomendasi Murid</h3>
                
                @foreach($recommendations as $rec)
                    <div class="mt-3">
                        <h4 class="text-xs font-bold text-slate-300">{{ $rec['title'] }}</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            {{ $rec['text'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 7. CARD: AI Memory (Span 1 Col) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-purple-400">Ingatan AI</span>
                <h3 class="text-lg font-bold text-white mt-1.5">Memori Pendamping</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-3">
                    Hal-hal penting tentangmu yang diingat oleh asisten AI:
                </p>
                
                <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                    @forelse($rawMemories as $mem)
                        <div class="flex justify-between items-center text-xs bg-slate-900/50 p-2 rounded-lg border border-slate-800/60">
                            <span class="font-semibold text-slate-400 text-xs uppercase">{{ str_replace('_', ' ', $mem->key) }}</span>
                            <span class="text-slate-200 text-right text-xs truncate max-w-[150px]">{{ $mem->value }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic py-2 text-center">AI belum memiliki memori tentangmu. Mulailah mengobrol!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 8. CARD: Achievements (Span 2 Cols) -->
        <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between">
            <div>
                <span class="text-xs uppercase font-extrabold tracking-widest text-violet-400">Recent Achievements</span>
                <h3 class="text-lg font-bold text-white mt-1.5">Lencana Pencapaian</h3>
                
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @foreach($achievements as $ach)
                        <div class="flex flex-col items-center justify-center p-3 rounded-2xl border transition-all duration-300 {{ $ach['unlocked'] ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-slate-900/30 border-slate-800 opacity-40' }}">
                            <span class="text-3xl mb-2">{{ $ach['icon'] }}</span>
                            <span class="text-xs font-bold text-white text-center leading-tight">{{ $ach['title'] }}</span>
                            <span class="text-[11px] text-slate-400 text-center mt-1 leading-tight">{{ $ach['desc'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
