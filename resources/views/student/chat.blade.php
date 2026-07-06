@extends('layouts.app')

@section('title', 'Konseling AI')

@section('content')
<div class="flex-grow max-w-4xl mx-auto w-full flex flex-col h-[calc(100vh-120px)]" x-data="chatEngine()">

    <!-- Main Chat Window (Full Width) -->
    <div class="flex-grow flex flex-col glass-panel rounded-3xl border border-slate-800 overflow-hidden relative shadow-2xl">
        
        <!-- Header Info Bar -->
        <div class="border-b border-slate-800/80 px-6 py-4 bg-slate-900/40 flex justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-xl shadow shadow-emerald-500/15">
                    🤖
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">Profilku AI</h4>
                    <p class="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-emerald-400 font-semibold">Online</span>
                        <span class="text-slate-500">•</span>
                        <span>Mentor & Teman Diskusi Virtual Anda</span>
                    </p>
                </div>
            </div>

            <!-- Subtle Reset Button -->
            <div>
                <form action="{{ route('student.chat.reset') }}" method="POST" @submit="confirmReset($event)">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-rose-400 text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-slate-900 transition-colors">
                        Mulai Ulang
                    </button>
                </form>
            </div>
        </div>

        <!-- Chat Viewport -->
        <div class="flex-grow overflow-y-auto p-6 space-y-4" id="chat-viewport">
            <template x-for="(msg, index) in messageHistory" :key="index">
                <div class="flex" :class="msg.sender === 'student' ? 'justify-end' : 'justify-start'">
                    
                    <!-- Avatar left for AI -->
                    <template x-if="msg.sender === 'ai'">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-sm mr-2.5 mt-1 shrink-0">
                            🤖
                        </div>
                    </template>
                    
                    <!-- Message bubble -->
                    <div class="max-w-[75%] p-4 rounded-2xl text-sm leading-relaxed transition-all shadow"
                         :class="msg.sender === 'student' 
                            ? 'bg-emerald-500 text-slate-950 font-medium rounded-tr-none' 
                            : 'bg-slate-900/90 border border-slate-800 text-slate-200 rounded-tl-none'">
                        
                        <!-- Format Text with simplified markdown renderer -->
                        <div class="space-y-1.5" x-html="renderMarkdown(msg.message_text)"></div>
                    </div>
                </div>
            </template>

            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-sm mr-2.5 shrink-0">
                    🤖
                </div>
                <div class="bg-slate-900/60 border border-slate-800/80 px-4 py-3 rounded-2xl rounded-tl-none flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-bounce"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-bounce [animation-delay:0.4s]"></span>
                </div>
            </div>
        </div>

        <!-- Message Input Form -->
        <div class="border-t border-slate-800/80 p-4 bg-slate-900/20 space-y-3">
            
            <!-- Suggestion Chips -->
            <div x-show="suggestionChips && suggestionChips.length > 0" class="flex gap-2 overflow-x-auto pb-1 scrollbar-none whitespace-nowrap">
                <template x-for="(chip, i) in suggestionChips" :key="i">
                    <button type="button" @click="useSuggestion(chip)"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-900/60 border border-slate-800 text-slate-300 hover:text-emerald-400 hover:border-emerald-500/30 transition shrink-0">
                        <span x-text="chip"></span>
                    </button>
                </template>
            </div>

            <!-- Input area -->
            <form @submit.prevent="sendMessage()" class="flex gap-3 items-center">
                <input type="text" x-model="inputMessage" :disabled="isTyping"
                    class="flex-grow appearance-none block px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/80 placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition text-sm"
                    placeholder="Ketik balasan Anda di sini... (AI akan menanggapi secara otomatis)">
                
                <button type="submit" :disabled="isTyping || !inputMessage.trim()"
                    class="bg-emerald-500 hover:bg-emerald-600 disabled:opacity-40 text-slate-950 font-bold p-3 rounded-xl transition shrink-0 flex items-center justify-center shadow shadow-emerald-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    function chatEngine() {
        return {
            currentStage: @json($currentStage),
            stageName: @json($stageName),
            suggestionChips: @json($suggestionChips),
            evidenceCount: @json($evidenceCount),
            averageConfidence: @json($averageConfidence),
            isTyping: false,
            inputMessage: '',
            messageHistory: [
                @foreach($messages as $msg)
                    { sender: '{{ $msg->sender }}', message_text: {!! json_encode($msg->message_text) !!} },
                @endforeach
            ],
            
            init() {
                this.scrollToBottom();
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const viewport = document.getElementById('chat-viewport');
                    if (viewport) {
                        viewport.scrollTop = viewport.scrollHeight;
                    }
                });
            },

            useSuggestion(chip) {
                this.inputMessage = chip;
                this.sendMessage();
            },

            sendMessage() {
                const message = this.inputMessage.trim();
                if (!message || this.isTyping) return;

                // 1. Dorong pesan murid ke UI
                this.messageHistory.push({ sender: 'student', message_text: message });
                this.inputMessage = '';
                this.isTyping = true;
                this.scrollToBottom();

                // 2. Kirim POST request ke server
                fetch('{{ route("student.chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(res => res.json())
                .then(data => {
                    this.isTyping = false;
                    if (data.status === 'success') {
                        // 3. Tampilkan respons AI secara streaming tiruan (efek ketik cepat)
                        this.streamMessage(data.ai_message);
                        
                        // 4. Update data scoring
                        this.currentStage = data.current_stage;
                        this.stageName = data.stage_name;
                        this.suggestionChips = data.suggestion_chips;
                        this.evidenceCount = data.evidence_count;
                        this.averageConfidence = data.average_confidence;

                        // Jika percakapan selesai
                        if (data.is_completed) {
                            setTimeout(() => {
                                alert("Selamat! Sesi profiling Anda telah lengkap. Laporan Anda berhasil disusun.");
                                window.location.href = "{{ route('student.dashboard') }}";
                            }, 3000);
                        }
                    } else {
                        this.messageHistory.push({ sender: 'ai', message_text: 'Maaf, terjadi kesalahan pengiriman pesan.' });
                    }
                })
                .catch(err => {
                    this.isTyping = false;
                    this.messageHistory.push({ sender: 'ai', message_text: 'Koneksi bermasalah. Periksa jaringan Anda.' });
                    this.scrollToBottom();
                });
            },

            streamMessage(fullText) {
                // Masukkan objek pesan kosong ke dalam array reaktif
                this.messageHistory.push({ sender: 'ai', message_text: '' });
                const index = this.messageHistory.length - 1;
                
                const words = fullText.split(' ');
                let i = 0;
                
                const timer = setInterval(() => {
                    if (i < words.length) {
                        // Perbarui teks secara reaktif menggunakan indeks array proxy
                        this.messageHistory[index].message_text += (i === 0 ? '' : ' ') + words[i];
                        i++;
                        this.scrollToBottom();
                    } else {
                        clearInterval(timer);
                    }
                }, 35); // 35ms per kata
            },

            confirmReset(e) {
                if (!confirm("Apakah Anda yakin ingin mengulangi percakapan dari awal? Semua memori, bukti, dan skor profiling saat ini akan diarsipkan.")) {
                    e.preventDefault();
                }
            },

            renderMarkdown(text) {
                if (!text) return '';
                
                // Escape HTML
                let formatted = text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;");

                // Replace newlines
                formatted = formatted.replace(/\n/g, '<br>');

                // Bold markers: **text**
                formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

                // Bullets: - item
                formatted = formatted.replace(/^- (.*?)(?:<br>|$)/gm, '<li class="ml-4 list-disc">$1</li>');

                return formatted;
            }
        };
    }
</script>
@endsection
