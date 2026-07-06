@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="space-y-8" x-data="{ activeProviderTab: '{{ $activeProvider ? $activeProvider->name : 'gemini' }}' }">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Beranda</a>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Pengaturan Sistem</h1>
            <p class="text-slate-400 text-sm mt-1">Konfigurasi API AI, integrasi model, pencadangan database, dan pemantauan tindakan pengguna.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="glass-panel p-5 rounded-2xl border border-rose-500/20 bg-rose-500/10 text-rose-455 text-sm space-y-2 shadow-lg">
            <h4 class="font-bold text-white flex items-center gap-1.5">
                <span>⚠️ Gagal Menyimpan:</span>
            </h4>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- AI Configuration Panel -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-bold text-white">Integrasi Provider AI</h3>
                <p class="text-xs text-slate-400">Konfigurasi API Key, Model LLM, dan Prompt Sistem bawaan untuk pendamping virtual BK.</p>
            </div>
            
            <!-- Quick Active Switch -->
            <form action="{{ route('admin.ai-provider.switch') }}" method="POST" class="flex gap-2 items-center">
                @csrf
                <span class="text-sm text-slate-400 font-semibold">Provider Aktif:</span>
                <select name="provider_id" onchange="this.form.submit()" 
                    class="appearance-none block px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-900 text-sm text-emerald-400 font-bold focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @foreach($providers as $prov)
                        <option value="{{ $prov->id }}" {{ $prov->is_active ? 'selected' : '' }}>
                            {{ strtoupper($prov->name) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Provider Tab Headers -->
        <div class="flex border-b border-slate-850 gap-2 overflow-x-auto">
            @foreach($providers as $prov)
                <button @click="activeProviderTab = '{{ $prov->name }}'"
                    :class="activeProviderTab === '{{ $prov->name }}' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-slate-300'"
                    class="px-4 py-2 border-b-2 text-sm font-semibold transition whitespace-nowrap">
                    Integrasi {{ strtoupper($prov->name) }}
                </button>
            @endforeach
        </div>

        <!-- Provider Tab Contents -->
        @foreach($providers as $prov)
            <div x-show="activeProviderTab === '{{ $prov->name }}'" class="space-y-4 pt-2">
                <form action="{{ route('admin.ai-provider.update', $prov->id) }}" method="POST" class="space-y-4" novalidate>
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-1.5">API Key (Rahasia)</label>
                            <input type="password" name="api_key" value="{{ $prov->api_key }}" 
                                class="appearance-none block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                                placeholder="Masukkan API Key {{ $prov->name }}">
                        </div>
                        
                        <!-- Dropdown Model Pilihan & Kustom -->
                        <div x-data="{ 
                            isCustom: false, 
                            selectedModel: '{{ $prov->model }}',
                            customModel: ''
                        }"
                        x-init="
                            const standardModels = [
                                'gemini-2.5-flash', 'gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash',
                                'google/gemini-2.5-flash', 'meta-llama/llama-3-8b-instruct:free',
                                'mistralai/mistral-7b-instruct:free', 'qwen/qwen-2-7b-instruct:free',
                                'microsoft/phi-3-medium-128k-instruct:free', 'openchat/openchat-7b:free',
                                'gryphe/mythomax-l2-13b:free', 'llama-3.3-70b-versatile',
                                'llama-3.1-8b-instant', 'mixtral-8x7b-32768', 'gemma2-9b-it',
                                'meta-llama/Llama-3.2-3B-Instruct', 'mistralai/Mistral-7B-Instruct-v0.3',
                                'Qwen/Qwen2.5-7B-Instruct', 'microsoft/Phi-3-mini-4k-instruct'
                            ];
                            if (!standardModels.includes(selectedModel)) {
                                customModel = selectedModel;
                                selectedModel = 'custom';
                                isCustom = true;
                            }
                        }" class="space-y-1">
                            <label class="block text-sm font-semibold text-slate-400 mb-1.5">Model LLM yang Digunakan</label>
                            
                            <select :name="!isCustom ? 'model' : ''" x-model="selectedModel" @change="isCustom = (selectedModel === 'custom')"
                                class="appearance-none block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                                
                                @if($prov->name === 'gemini')
                                    <option value="gemini-2.5-flash">gemini-2.5-flash (Direkomendasikan - Free Tier)</option>
                                    <option value="gemini-1.5-flash">gemini-1.5-flash (Free Tier)</option>
                                    <option value="gemini-1.5-pro">gemini-1.5-pro (Free Tier)</option>
                                    <option value="gemini-2.0-flash">gemini-2.0-flash (Free Tier)</option>
                                @elseif($prov->name === 'openrouter')
                                    <option value="google/gemini-2.5-flash">google/gemini-2.5-flash (Free)</option>
                                    <option value="meta-llama/llama-3-8b-instruct:free">meta-llama/llama-3-8b-instruct:free (Free)</option>
                                    <option value="mistralai/mistral-7b-instruct:free">mistralai/mistral-7b-instruct:free (Free)</option>
                                    <option value="qwen/qwen-2-7b-instruct:free">qwen/qwen-2-7b-instruct:free (Free)</option>
                                    <option value="microsoft/phi-3-medium-128k-instruct:free">microsoft/phi-3-medium-128k-instruct:free (Free)</option>
                                    <option value="openchat/openchat-7b:free">openchat/openchat-7b:free (Free)</option>
                                    <option value="gryphe/mythomax-l2-13b:free">gryphe/mythomax-l2-13b:free (Free)</option>
                                @elseif($prov->name === 'groq')
                                    <option value="llama-3.3-70b-versatile">llama-3.3-70b-versatile (Free Tier)</option>
                                    <option value="llama-3.1-8b-instant">llama-3.1-8b-instant (Free Tier)</option>
                                    <option value="mixtral-8x7b-32768">mixtral-8x7b-32768 (Free Tier)</option>
                                    <option value="gemma2-9b-it">gemma2-9b-it (Free Tier)</option>
                                @elseif($prov->name === 'huggingface')
                                    <option value="meta-llama/Llama-3.2-3B-Instruct">meta-llama/Llama-3.2-3B-Instruct (Free API)</option>
                                    <option value="mistralai/Mistral-7B-Instruct-v0.3">mistralai/Mistral-7B-Instruct-v0.3 (Free API)</option>
                                    <option value="Qwen/Qwen2.5-7B-Instruct">Qwen/Qwen2.5-7B-Instruct (Free API)</option>
                                    <option value="microsoft/Phi-3-mini-4k-instruct">microsoft/Phi-3-mini-4k-instruct (Free API)</option>
                                @endif
                                <option value="custom">Kustom (Masukkan Manual...)</option>
                            </select>

                            <div x-show="isCustom" x-transition class="mt-2">
                                <input type="text" :name="isCustom ? 'model' : ''" x-model="customModel" required placeholder="Masukkan nama model kustom..."
                                    class="appearance-none block w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-1.5">Temperature (0.0 - 2.0)</label>
                            <input type="text" name="temperature" inputmode="decimal" value="{{ $prov->temperature }}" required
                                pattern="[0-9]*[.,]?[0-9]+"
                                class="appearance-none block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                                placeholder="Contoh: 0.7">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-1.5">Top P (0.0 - 1.0)</label>
                            <input type="text" name="top_p" inputmode="decimal" value="{{ $prov->top_p }}" required
                                pattern="[0-9]*[.,]?[0-9]+"
                                class="appearance-none block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                                placeholder="Contoh: 0.9">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-1.5">Maksimal Output Token</label>
                            <input type="number" name="max_tokens" value="{{ $prov->max_tokens }}" required
                                class="appearance-none block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Prompt Sistem (Fokus Bimbingan Konseling)</label>
                        <textarea name="system_prompt" required rows="6" 
                            class="appearance-none block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm leading-relaxed font-mono">{{ $prov->system_prompt }}</textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <label class="flex items-center text-sm text-slate-300 font-semibold cursor-pointer">
                            <input type="checkbox" name="make_active" value="1" {{ $prov->is_active ? 'checked' : '' }}
                                class="h-4 w-4 text-emerald-500 focus:ring-emerald-500/50 border-slate-700 bg-slate-900 rounded mr-2">
                            <span>Jadikan Provider Aktif</span>
                        </label>
                    </div>

                    <div class="pt-2 flex flex-wrap gap-3 items-center" x-data="{ 
                        testStatus: '', 
                        testSuccess: null, 
                        isTesting: false,
                        testConn() {
                            this.isTesting = true;
                            this.testStatus = 'Menghubungi server AI...';
                            this.testSuccess = null;
                            
                            const form = $el.closest('form');
                            const apiKey = form.querySelector('[name=api_key]').value;
                            const model = form.querySelector('[name=model]').value;
                            const csrf = form.querySelector('[name=_token]').value;
                            
                            fetch('{{ url('/admin/ai-provider') }}/{{ $prov->id }}/test-connection', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify({ api_key: apiKey, model: model })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.isTesting = false;
                                this.testSuccess = data.success;
                                this.testStatus = data.message;
                            })
                            .catch(err => {
                                this.isTesting = false;
                                this.testSuccess = false;
                                this.testStatus = 'Koneksi gagal: ' + err;
                            });
                        }
                    }">
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-md shadow-emerald-500/10">
                            Simpan Konfigurasi {{ strtoupper($prov->name) }}
                        </button>
                        
                        <button type="button" @click="testConn()" :disabled="isTesting"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold bg-slate-900 border border-slate-800 hover:bg-slate-850 text-slate-300 transition">
                            <span x-text="isTesting ? '⌛ Menguji...' : '🔌 Tes Koneksi'"></span>
                        </button>

                        <!-- Test result message -->
                        <template x-if="testStatus">
                            <div class="text-xs px-3 py-2 rounded-xl border font-semibold leading-relaxed"
                                :class="testSuccess ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-455 border-rose-500/20'">
                                <span x-text="testStatus"></span>
                            </div>
                        </template>
                    </div>

                </form>
            </div>
        @endforeach
    </div>

    <!-- DB Backups & Audit Logs Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left: System Utilities (Backup/Restore) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider text-slate-400">Pencadangan Sistem</h3>
            <p class="text-xs text-slate-400 leading-relaxed">Ekspor database lengkap atau pulihkan sistem ke cadangan terdekat:</p>
            
            <div class="space-y-3 pt-2">
                <form action="{{ route('admin.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center py-3 rounded-xl text-sm font-semibold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 transition">
                        🗄️ Ekspor Database (.SQL)
                    </button>
                </form>
                <form action="{{ route('admin.restore') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center py-3 rounded-xl text-sm font-semibold bg-slate-900 hover:bg-slate-850 text-slate-300 border border-slate-850 transition">
                        🔄 Restore Point Terakhir
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Audit Logs (Span 2 Cols) -->
        <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider text-slate-400">Riwayat Tindakan Pengguna (Audit Logs)</h3>
            <p class="text-xs text-slate-400 leading-relaxed">Log aktivitas keamanan dan konfigurasi sistem oleh administrator atau guru:</p>
            
            <div class="space-y-3 max-h-[160px] overflow-y-auto pr-1">
                @foreach($auditLogs as $log)
                    <div class="flex justify-between items-start text-xs bg-slate-900/50 p-3 rounded-xl border border-slate-850">
                        <div class="space-y-1">
                            <p class="text-slate-300 font-medium">{{ $log['action'] }}</p>
                            <div class="flex gap-2 text-xs text-slate-400">
                                <span>Oleh Pengguna: <strong class="text-slate-400">{{ $log['user'] }}</strong></span>
                                <span>•</span>
                                <span>IP: {{ $log['ip'] }}</span>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400 font-mono shrink-0 pl-2">{{ $log['timestamp'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
