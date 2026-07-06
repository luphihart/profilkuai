@extends('layouts.app')

@section('title', 'Manajemen Aturan Alur AI')

@section('content')
<div class="space-y-8" x-data="{
    editRuleModal: false,
    createRuleModal: false,
    editRuleData: {},
    openEditRule(rule) {
        this.editRuleData = {
            id: rule.id,
            name: rule.name,
            category: rule.category,
            priority: rule.priority,
            trigger_condition: rule.trigger_condition,
            action: rule.action,
            description: rule.description || '',
            parameters: rule.parameters || ''
        };
        this.editRuleModal = true;
    }
}">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Home AI</a>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Rule Engine</h1>
            <p class="text-slate-400 text-sm mt-1">Mengaktifkan/menonaktifkan aturan kendali percakapan dan alur validasi pakar AI.</p>
        </div>
        <div class="flex gap-2">
            <button @click="createRuleModal = true" class="bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-600 hover:to-teal-500 text-slate-950 font-extrabold px-4 py-2.5 rounded-xl text-sm transition shadow-md shadow-emerald-500/10">
                ✨ Tambah Aturan Baru
            </button>
        </div>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-6">
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Total Aturan</span>
            <span class="text-2xl font-extrabold text-white mt-1 block">{{ $stats['total'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Aktif</span>
            <span class="text-2xl font-extrabold text-emerald-400 mt-1 block">{{ $stats['active'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Non-Aktif</span>
            <span class="text-2xl font-extrabold text-slate-400 mt-1 block">{{ $stats['inactive'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Prioritas Kritis</span>
            <span class="text-2xl font-extrabold text-rose-500 mt-1 block">{{ $stats['critical'] }}</span>
        </div>
        <div class="glass-card p-4 rounded-xl border border-slate-800/80">
            <span class="text-xs uppercase font-bold text-slate-400 block">Prioritas Tinggi</span>
            <span class="text-2xl font-extrabold text-amber-500 mt-1 block">{{ $stats['high'] }}</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 shadow-xl">
        <form action="{{ route('admin.rules') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Cari Nama Aturan</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari berdasarkan nama..."
                    class="appearance-none block w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
            </div>
            
            <!-- Filter Category -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Kategori</label>
                <select name="category" class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Priority -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Prioritas</label>
                <select name="priority" class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                    <option value="">Semua Prioritas</option>
                    <option value="Critical" {{ $priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                    <option value="High" {{ $priority == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Medium" {{ $priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="Low" {{ $priority == 'Low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Status</label>
                <select name="status" class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-5 flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.rules') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-slate-900 border border-slate-805 text-slate-300 hover:bg-slate-800 transition">
                    Reset Filter
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-md shadow-emerald-500/10">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Rules Grid -->
    <div class="space-y-4">
        <h3 class="text-lg font-bold text-white">Aturan Alur Terdaftar</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($rules as $rule)
                <div class="bg-slate-900/60 p-5 rounded-2xl border border-slate-805 space-y-4 flex flex-col justify-between relative group hover:border-emerald-500/20 transition-all duration-300">
                    <div class="space-y-3">
                        <!-- Title & Category Badges -->
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h4 class="font-extrabold text-base text-white">{{ $rule->name }}</h4>
                                <div class="flex flex-wrap gap-2 mt-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-950 border border-slate-850 text-slate-400">
                                        {{ $rule->category }}
                                    </span>
                                    
                                    <!-- Priority Badge -->
                                    @if($rule->priority === 'Critical')
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-rose-500/10 text-rose-455 border border-rose-500/20">
                                            CRITICAL
                                        </span>
                                    @elseif($rule->priority === 'High')
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-amber-500/10 text-amber-455 border border-amber-500/20">
                                            HIGH
                                        </span>
                                    @elseif($rule->priority === 'Medium')
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                            MEDIUM
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-950 text-slate-500 border border-slate-850">
                                            LOW
                                        </span>
                                    @endif

                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $rule->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-950 text-slate-500 border border-slate-850' }}">
                                        {{ $rule->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-slate-300 leading-relaxed">{{ $rule->description }}</p>

                        <!-- Trigger & Action Block -->
                        <div class="text-sm space-y-1.5 border-t border-slate-850/80 pt-3 font-mono">
                            <div>
                                <span class="text-slate-400 font-sans font-semibold text-xs">Kondisi Pemicu:</span>
                                <span class="text-teal-450 bg-slate-950 px-2 py-0.5 rounded text-xs inline-block mt-0.5 font-mono">{{ $rule->trigger_condition }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-slate-400 font-sans font-semibold text-xs">Tindakan Sistem:</span>
                                <span class="text-indigo-400 bg-slate-950 px-2 py-0.5 rounded text-xs inline-block mt-0.5 font-mono">{{ $rule->action }}</span>
                            </div>
                            @if($rule->parameters)
                                <div class="mt-1">
                                    <span class="text-slate-400 font-sans font-semibold text-xs">Parameter:</span>
                                    <span class="text-amber-400 bg-slate-950 px-2 py-0.5 rounded text-xs inline-block mt-0.5 font-mono">{{ $rule->parameters }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Footer of Card -->
                    <div class="flex justify-between items-center border-t border-slate-850 pt-3 mt-2 text-xs text-slate-500">
                        <span>Update: {{ $rule->updated_at->format('d M Y H:i') }}</span>
                        <div class="flex gap-2">
                            <!-- Edit Button -->
                            <button @click="openEditRule({{ json_encode($rule) }})" 
                                class="px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-850 text-emerald-400 hover:text-emerald-355 transition font-bold" title="Edit Aturan">
                                ✏️ Edit
                            </button>

                            <!-- Toggle Button -->
                            <form action="{{ route('admin.rules.toggle', $rule->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                    class="px-2.5 py-1.5 rounded-lg border text-xs font-bold transition
                                    {{ $rule->is_active 
                                        ? 'bg-rose-500/10 hover:bg-rose-500/20 text-rose-455 border-rose-500/25' 
                                        : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border-emerald-500/25' }}">
                                    {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.rules.delete', $rule->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan ini?')">
                                @csrf
                                <button type="submit" 
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-850 text-rose-500 hover:text-rose-455 transition font-bold" title="Hapus Aturan">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-12 text-center text-slate-500 italic">
                    Tidak ada aturan alur yang sesuai dengan filter pencarian Anda.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Create Rule -->
    <div x-show="createRuleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" style="display: none;" x-cloak>
        <div class="glass-panel max-w-lg w-full p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl relative" @click.away="createRuleModal = false">
            <h3 class="text-lg font-bold text-white">Tambah Aturan Baru</h3>
            <p class="text-sm text-slate-400">Tentukan nama, pemicu, tindakan, dan prioritas untuk aturan baru</p>
            
            <form action="{{ route('admin.rules.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Aturan</label>
                        <input type="text" name="name" required placeholder="e.g. Response Too Short"
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kategori</label>
                        <select name="category" required
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Prioritas</label>
                        <select name="priority" required
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-350 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            <option value="Critical">Critical</option>
                            <option value="High">High</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Tindakan Sistem (Action)</label>
                        <input type="text" name="action" required
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. elaboration_prompt">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kondisi Pemicu (Trigger Condition)</label>
                    <input type="text" name="trigger_condition" required
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="e.g. length(student_response) < 15">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Parameter (Opsional)</label>
                    <input type="text" name="parameters"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="e.g. {'limit': 5}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Deskripsi Aturan</label>
                    <textarea name="description" rows="3"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="Jelaskan tujuan dan fungsi aturan ini..."></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="createRuleModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-slate-900 border border-slate-805 text-slate-300 hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-600 text-slate-950 transition">
                        Tambah Aturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Rule -->
    <div x-show="editRuleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" style="display: none;" x-cloak>
        <div class="glass-panel max-w-lg w-full p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl relative" @click.away="editRuleModal = false">
            <h3 class="text-lg font-bold text-white">Edit Aturan Pakar</h3>
            <p class="text-sm text-slate-400">Sesuaikan pemicu, prioritas, dan tindakan untuk aturan <strong class="text-slate-200" x-text="editRuleData.name"></strong></p>
            
            <form :action="'{{ url('/admin/rules') }}/' + editRuleData.id + '/update'" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Aturan</label>
                        <input type="text" name="name" required x-model="editRuleData.name"
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kategori</label>
                        <select name="category" required x-model="editRuleData.category"
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-350 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Prioritas</label>
                        <select name="priority" required x-model="editRuleData.priority"
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-350 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            <option value="Critical">Critical</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Tindakan Sistem (Action)</label>
                        <input type="text" name="action" required x-model="editRuleData.action"
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                            placeholder="e.g. deepen_exploration">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kondisi Pemicu (Trigger Condition)</label>
                    <input type="text" name="trigger_condition" required x-model="editRuleData.trigger_condition"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="e.g. confidence_score < 70">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Parameter (Opsional)</label>
                    <input type="text" name="parameters" x-model="editRuleData.parameters"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="e.g. e.g. {'limit': 5}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Deskripsi Aturan</label>
                    <textarea name="description" x-model="editRuleData.description" rows="3"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="Jelaskan tujuan dan fungsi aturan ini..."></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editRuleModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-slate-900 border border-slate-850 text-slate-300 hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-600 text-slate-950 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
