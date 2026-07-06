@extends('layouts.app')

@section('title', 'Detail Profil Murid')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'analysis' }">
    
    <!-- Student Header Card -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bk.dashboard') }}" class="text-sm text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Dashboard</a>
            </div>
            <h1 class="text-2xl font-extrabold text-white mt-2 tracking-tight">{{ $student->name }}</h1>
            <p class="text-sm text-slate-400 mt-1">
                Kelas: <span class="text-slate-200 font-semibold">{{ $student->schoolClass->name }}</span> | 
                Jurusan: <span class="text-slate-200 font-semibold">{{ $student->schoolClass->major->name }}</span> | 
                Status Sesi: 
                <span class="px-2.5 py-0.5 rounded text-xs font-bold {{ $conversation && $conversation->status === 'completed' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">
                    {{ $conversation ? strtoupper($conversation->status) : 'INACTIVE' }}
                </span>
            </p>
        </div>

        <div class="flex flex-wrap gap-2.5">
            <form action="{{ route('bk.student.report.trigger', $student->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-600 hover:to-teal-500 text-slate-950 font-extrabold px-4 py-2.5 rounded-xl text-sm transition shadow-md shadow-emerald-500/10">
                    💡 Susun Ulang Laporan AI
                </button>
            </form>
            
            <form action="{{ route('bk.student.reset-session', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset sesi bimbingan murid ini? Seluruh riwayat obrolan, skor keyakinan, bukti profiling, dan memori AI murid ini akan dihapus secara permanen.')">
                @csrf
                <button type="submit" class="bg-slate-900 border border-slate-805 hover:bg-slate-800 text-rose-500 font-extrabold px-4 py-2.5 rounded-xl text-sm transition">
                    🔄 Reset Sesi
                </button>
            </form>
        </div>
    </div>

    <!-- TAB HEADERS -->
    <div class="flex border-b border-slate-800 gap-2 overflow-x-auto pb-px scrollbar-none whitespace-nowrap">
        <button @click="activeTab = 'analysis'" 
            :class="activeTab === 'analysis' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-white'"
            class="px-4 py-3 border-b-2 text-sm font-semibold transition shrink-0">
            📊 Explainable AI & Analisis
        </button>
        <button @click="activeTab = 'report'" 
            :class="activeTab === 'report' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-white'"
            class="px-4 py-3 border-b-2 text-sm font-semibold transition shrink-0">
            📝 Laporan Profiling Akhir
        </button>
        <button @click="activeTab = 'chat'" 
            :class="activeTab === 'chat' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-white'"
            class="px-4 py-3 border-b-2 text-sm font-semibold transition shrink-0">
            💬 Riwayat Chat Transcript
        </button>
        <button @click="activeTab = 'memories'" 
            :class="activeTab === 'memories' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-white'"
            class="px-4 py-3 border-b-2 text-sm font-semibold transition shrink-0">
            🧠 Memori Ingatan AI
        </button>
        <button @click="activeTab = 'notes'" 
            :class="activeTab === 'notes' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-white'"
            class="px-4 py-3 border-b-2 text-sm font-semibold transition shrink-0">
            📓 Catatan Bimbingan BK ({{ $notes->count() }})
        </button>
    </div>

    <!-- TAB CONTENTS -->
    
    <!-- 1. TAB: Explainable AI -->
    <div x-show="activeTab === 'analysis'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Confidence Scores -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4 h-fit">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Tingkat Keyakinan Domain</h3>
                <p class="text-sm text-slate-400 leading-relaxed">Persentase keyakinan asisten AI mengenai pemahaman domain kompetensi/masalah murid:</p>
                
                <div class="space-y-4">
                    @forelse($confidenceScores as $score)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-slate-300">{{ $score->domain->name }}</span>
                                <span class="font-bold text-slate-400">{{ $score->score }}%</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2">
                                <div class="bg-emerald-500/85 h-2 rounded-full transition-all duration-300" style="width: {{ $score->score }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic py-4 text-center">Belum ada domain bimbingan yang dianalisis oleh AI.</p>
                    @endforelse
                </div>
            </div>

            <!-- Right: Evidence logs (Explainable AI Detail) -->
            <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Bukti Kesimpulan (Explainable AI Trace)</h3>
                <p class="text-sm text-slate-400 leading-relaxed">Tracing mengapa AI mengambil kesimpulan di atas berdasarkan kutipan percakapan asli:</p>
                
                <div class="space-y-4">
                    @forelse($evidence as $ev)
                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 space-y-2.5">
                            <div class="flex justify-between items-center gap-2">
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded text-xs font-bold">
                                    Domain: {{ $ev->domain->name }}
                                </span>
                                <div class="flex items-center gap-1.5 text-sm">
                                    <span class="text-slate-400">Bobot Bukti:</span>
                                    <span class="font-bold text-slate-300">{{ $ev->weight }}</span>
                                </div>
                            </div>
                            
                            <div class="text-sm text-slate-300 border-l-2 border-slate-700 pl-3 italic leading-relaxed">
                                "{{ $ev->excerpt }}"
                            </div>
                            
                            <div class="text-sm text-slate-400 leading-relaxed">
                                <strong class="text-slate-300">Rasional Analisis AI:</strong> {{ $ev->reasoning }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic py-8 text-center">Belum ada bukti kutipan percakapan yang dicatat oleh Evidence Engine.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TAB: Report View -->
    <div x-show="activeTab === 'report'" class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6">
        @if($latestReport)
            <div class="flex justify-between items-center border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white">Laporan Profiling AI SMK</h3>
                    <p class="text-sm text-slate-400">Disusun otomatis pada: {{ $latestReport->created_at->format('d M Y H:i') }}</p>
                </div>
                
                <!-- Print Report button -->
                <button onclick="window.print()" class="border border-slate-700 bg-slate-900 hover:bg-slate-850 text-white font-bold px-4 py-2 rounded-xl text-sm transition">
                    🖨️ Cetak / Simpan PDF
                </button>
            </div>

            <!-- Printable content area -->
            <div class="space-y-6 text-slate-300 text-sm leading-relaxed" id="printable-report-area">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
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
                            <p class="mt-1.5 text-sm">{{ $latestReport->student_recommendations }}</p>
                        </div>
                        <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                            <span class="text-xs uppercase font-extrabold text-teal-400">Untuk Guru BK</span>
                            <p class="mt-1.5 text-sm">{{ $latestReport->bk_recommendations }}</p>
                        </div>
                        <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                            <span class="text-xs uppercase font-extrabold text-indigo-400">Untuk Wali Kelas</span>
                            <p class="mt-1.5 text-sm">{{ $latestReport->wali_recommendations }}</p>
                        </div>
                        <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                            <span class="text-xs uppercase font-extrabold text-pink-400">Untuk Orang Tua</span>
                            <p class="mt-1.5 text-sm">{{ $latestReport->parent_recommendations }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-500/5 border border-emerald-500/10 p-5 rounded-xl mt-6">
                    <h4 class="font-bold text-emerald-400 text-sm border-b border-emerald-500/10 pb-2 mb-2">Rencana Follow-Up</h4>
                    <p class="text-sm">{{ $latestReport->follow_up_plan }}</p>
                </div>

            </div>
        @else
            <div class="text-center py-12 space-y-4">
                <span class="text-4xl">🗒️</span>
                <h3 class="text-base font-bold text-slate-300">Laporan belum disusun</h3>
                <p class="text-sm text-slate-400 max-w-md mx-auto leading-relaxed">Murid ini belum menyelesaikan seluruh sesi percakapan dengan AI atau laporan belum digenerate secara otomatis.</p>
                <form action="{{ route('bk.student.report.trigger', $student->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-4 py-2 rounded-xl text-sm transition">
                        Susun Laporan dengan AI Sekarang
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- 3. TAB: Chat History -->
    <div x-show="activeTab === 'chat'" class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Transkrip Percakapan</h3>
        
        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
            @forelse($messages as $msg)
                <div class="flex {{ $msg->sender === 'student' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] p-3.5 rounded-2xl text-sm leading-relaxed {{ $msg->sender === 'student' ? 'bg-slate-800 text-white rounded-tr-none' : 'bg-slate-900 border border-slate-800 text-slate-300 rounded-tl-none' }}">
                        <span class="block text-xs uppercase font-bold text-slate-400 mb-1">{{ $msg->sender === 'student' ? 'Murid' : 'AI Mentor' }}</span>
                        {!! nl2br(e($msg->message_text)) !!}
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 italic text-center py-8">Belum ada riwayat pesan percakapan.</p>
            @endforelse
        </div>
    </div>

    <!-- 4. TAB: Memories -->
    <div x-show="activeTab === 'memories'" class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Memori Faktual Murid</h3>
        <p class="text-sm text-slate-400 leading-relaxed">Kumpulan fakta/entitas yang secara konsisten diingat oleh asisten AI dari respons murid:</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @forelse($memories as $mem)
                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80">
                    <span class="text-xs uppercase font-extrabold text-slate-400">{{ str_replace('_', ' ', $mem->key) }}</span>
                    <p class="text-sm font-semibold text-slate-200 mt-1 leading-relaxed">{{ $mem->value }}</p>
                    <span class="text-xs text-slate-400 block mt-2">Tingkat Akurasi: {{ $mem->confidence * 100 }}%</span>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 text-slate-400 italic">Belum ada memori murid yang berhasil diekstraksi.</div>
            @endforelse
        </div>
    </div>

    <!-- 5. TAB: Counselor Notes -->
    <div x-show="activeTab === 'notes'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left: Write Note Form -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 h-fit space-y-4"
            x-data="{ 
                noteText: '', 
                isLoading: false,
                generateAI() {
                    this.isLoading = true;
                    fetch('{{ route('bk.student.recommendation.generate', $student->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isLoading = false;
                        if (data.recommendation) {
                            this.noteText = data.recommendation;
                        } else {
                            alert('Gagal menyusun rekomendasi. Periksa API Key AI di panel admin.');
                        }
                    })
                    .catch(err => {
                        this.isLoading = false;
                        alert('Terjadi kesalahan sistem saat menghubungi AI.');
                    });
                }
            }">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Tulis Catatan Bimbingan BK</h3>
            
            <form action="{{ route('bk.student.note', $student->id) }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Tombol Rekomendasi AI -->
                <button type="button" @click="generateAI()" :disabled="isLoading"
                    class="w-full flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-sm font-bold bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/25 transition disabled:opacity-50 shadow-sm shadow-indigo-500/5">
                    <span x-show="!isLoading">✨ Tulis Draf Rekomendasi BK via AI</span>
                    <span x-show="isLoading">⏳ Menyusun Draf Rekomendasi...</span>
                </button>

                <div>
                    <textarea name="note_text" x-model="noteText" required rows="8" 
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 placeholder-slate-500 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm leading-relaxed" 
                        placeholder="Tuliskan catatan hasil bimbingan tatap muka, rekomendasi sanksi, arahan khusus, dll..."></textarea>
                </div>
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition shadow-md shadow-emerald-500/10">
                    Simpan Catatan BK
                </button>
            </form>
        </div>

        <!-- Right: Notes List -->
        <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Catatan Historis</h3>
            
            <div class="space-y-4">
                @forelse($notes as $note)
                    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                        <div class="flex justify-between items-center text-sm text-slate-400">
                            <span>Ditulis oleh: <strong class="text-slate-300">{{ $note->teacher->name }}</strong></span>
                            <span>{{ $note->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed font-sans">{!! nl2br(e($note->note_text)) !!}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 italic text-center py-8">Belum ada catatan konseling manual yang disimpan.</p>
                @endforelse
            </div>
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
            color: #000 !important;
        }
    }
</style>
@endsection
