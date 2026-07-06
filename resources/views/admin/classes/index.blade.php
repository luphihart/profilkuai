@extends('layouts.app')

@section('title', 'Manajemen Kelas & Jurusan')

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: 'classes',
    editClassModal: false,
    editClassData: {},
    editMajorModal: false,
    editMajorData: {},
    openEditClass(c) {
        this.editClassData = {...c};
        this.editClassModal = true;
    },
    openEditMajor(m) {
        this.editMajorData = {...m};
        this.editMajorModal = true;
    }
}">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Home AI</a>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Kelas & Jurusan</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola kelas bimbingan, plotting Wali Kelas, jurusan akademik, serta penempatan murid.</p>
        </div>
    </div>

    <!-- Navigation Tabs: Horizontal Scrollable on Mobile -->
    <div class="flex border-b border-slate-800 gap-4 sm:gap-6 overflow-x-auto pb-1 scrollbar-none whitespace-nowrap">
        <button @click="activeTab = 'classes'" 
            class="pb-3 text-sm uppercase font-extrabold tracking-wider transition-all duration-200 border-b-2"
            :class="activeTab === 'classes' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-200'">
            🏫 Kelas & Wali Kelas
        </button>
        <button @click="activeTab = 'students'" 
            class="pb-3 text-sm uppercase font-extrabold tracking-wider transition-all duration-200 border-b-2"
            :class="activeTab === 'students' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-200'">
            🧑‍🎓 Plotting Kelas Murid
        </button>
        <button @click="activeTab = 'majors'" 
            class="pb-3 text-sm uppercase font-extrabold tracking-wider transition-all duration-200 border-b-2"
            :class="activeTab === 'majors' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-200'">
            📚 Manajemen Jurusan
        </button>
    </div>

    <!-- TAB 1: MANAJEMEN KELAS & WALI KELAS -->
    <div x-show="activeTab === 'classes'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1: Tambah Kelas -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 h-fit space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Kelas</h3>
            
            <form action="{{ route('admin.classes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Kelas</label>
                    <input type="text" name="name" required 
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="Contoh: XII RPL 1">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Pilih Jurusan</label>
                    <select name="major_id" required 
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Plotting Wali Kelas (Opsional)</label>
                    <select name="homeroom_teacher_id" 
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        <option value="">-- Belum Diplot --</option>
                        @foreach($homeroomTeachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition">
                    Simpan Kelas
                </button>
            </form>
        </div>

        <!-- Column 2-3: Daftar Kelas -->
        <div class="lg:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-lg font-bold text-white">Daftar Kelas</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                            <th class="py-3 px-2">Nama Kelas</th>
                            <th class="py-3 px-2">Jurusan</th>
                            <th class="py-3 px-2">Wali Kelas</th>
                            <th class="py-3 px-2">Jumlah Murid</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $c)
                            <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                                <td class="py-3 px-2 font-bold text-white">{{ $c->name }}</td>
                                <td class="py-3 px-2 text-slate-300 font-medium">
                                    {{ $c->major->name }} ({{ $c->major->code }})
                                </td>
                                <td class="py-3 px-2">
                                    <form action="{{ route('admin.classes.plot-homeroom', $c->id) }}" method="POST" class="flex items-center gap-1.5">
                                        @csrf
                                        <select name="homeroom_teacher_id" onchange="this.form.submit()"
                                            class="bg-slate-900 border border-slate-800 text-xs text-slate-300 rounded px-2 py-1.5 focus:outline-none focus:border-emerald-500 max-w-[160px]">
                                            <option value="">-- Belum Diplot --</option>
                                            @foreach($homeroomTeachers as $t)
                                                <option value="{{ $t->id }}" {{ $c->homeroom_teacher_id == $t->id ? 'selected' : '' }}>
                                                    {{ $t->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="py-3 px-2 font-bold text-emerald-400">
                                    {{ $c->students->count() }} Murid
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Class Button (Ikon) -->
                                        <button @click="openEditClass({{ json_encode(['id' => $c->id, 'name' => $c->name, 'major_id' => $c->major_id, 'homeroom_teacher_id' => $c->homeroom_teacher_id]) }})" 
                                            class="p-1 text-emerald-400 hover:text-emerald-300 hover:bg-slate-900 rounded transition" title="Edit Kelas">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Delete Class Button (Ikon) -->
                                        <form action="{{ route('admin.classes.delete', $c->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Murid di kelas ini akan dialokasikan tanpa kelas.')"
                                                class="p-1 text-rose-500 hover:text-rose-455 hover:bg-slate-900 rounded transition" title="Hapus Kelas">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500 italic">Belum ada kelas terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: PLOTTING KELAS MURID -->
    <div x-show="activeTab === 'students'" class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-lg font-bold text-white">Plotting Murid ke Kelas</h3>
        <p class="text-sm text-slate-400">Posisikan atau pindahkan murid bimbingan ke dalam kelas akademik yang aktif.</p>
        
        <div class="overflow-x-auto pt-2">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                        <th class="py-3 px-2">Nama Murid</th>
                        <th class="py-3 px-2">Alamat Email</th>
                        <th class="py-3 px-2">Kelas Saat Ini</th>
                        <th class="py-3 px-2">Plotting Pilihan Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $s)
                        <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                            <td class="py-3 px-2 font-bold text-white">{{ $s->name }}</td>
                            <td class="py-3 px-2 text-slate-500 font-mono">{{ $s->email }}</td>
                            <td class="py-3 px-2">
                                @if($s->schoolClass)
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $s->schoolClass->name }} ({{ $s->schoolClass->major->code }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-rose-500/10 text-rose-455 border border-rose-500/20">
                                        Belum Ada Kelas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                <form action="{{ route('admin.classes.plot-student') }}" method="POST" class="flex items-center">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $s->id }}">
                                    <select name="class_id" onchange="this.form.submit()"
                                        class="bg-slate-900 border border-slate-800 text-xs text-slate-300 rounded px-2.5 py-1.5 focus:outline-none focus:border-emerald-500 max-w-[220px]">
                                        <option value="">-- Pindahkan/Keluarkan --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ $s->class_id == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} - {{ $c->major->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-500 italic">Belum ada pengguna murid terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: MANAJEMEN JURUSAN -->
    <div x-show="activeTab === 'majors'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1: Tambah Jurusan -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 h-fit space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Jurusan</h3>
            
            <form action="{{ route('admin.majors.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Jurusan</label>
                    <input type="text" name="name" required 
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="Contoh: Rekayasa Perangkat Lunak">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kode Singkatan</label>
                    <input type="text" name="code" required 
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                        placeholder="Contoh: RPL">
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition">
                    Simpan Jurusan
                </button>
            </form>
        </div>

        <!-- Column 2-3: Daftar Jurusan -->
        <div class="lg:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <h3 class="text-lg font-bold text-white">Daftar Jurusan</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                            <th class="py-3 px-2">Nama Jurusan</th>
                            <th class="py-3 px-2">Kode Singkatan</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($majors as $m)
                            <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                                <td class="py-3 px-2 font-bold text-white">{{ $m->name }}</td>
                                <td class="py-3 px-2 text-slate-300 font-mono">{{ $m->code }}</td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Major Button (Ikon) -->
                                        <button @click="openEditMajor({{ json_encode(['id' => $m->id, 'name' => $m->name, 'code' => $m->code]) }})" 
                                            class="p-1 text-emerald-400 hover:text-emerald-350 hover:bg-slate-900 rounded transition" title="Edit Jurusan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Delete Major Button (Ikon) -->
                                        <form action="{{ route('admin.majors.delete', $m->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus jurusan ini? Seluruh kelas dan penempatan murid di dalamnya juga akan terhapus.')"
                                                class="p-1 text-rose-500 hover:text-rose-450 hover:bg-slate-900 rounded transition" title="Hapus Jurusan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-500 italic">Belum ada jurusan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kelas -->
    <div x-show="editClassModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" style="display: none;">
        <div class="glass-panel max-w-sm w-full p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl relative" @click.away="editClassModal = false">
            <h3 class="text-lg font-bold text-white">Edit Kelas</h3>
            
            <form :action="'{{ url('/admin/classes') }}/' + editClassData.id + '/update'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Kelas</label>
                    <input type="text" name="name" required x-model="editClassData.name"
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Pilih Jurusan</label>
                    <select name="major_id" required x-model="editClassData.major_id"
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Plotting Wali Kelas</label>
                    <select name="homeroom_teacher_id" x-model="editClassData.homeroom_teacher_id"
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        <option value="">-- Belum Diplot --</option>
                        @foreach($homeroomTeachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editClassModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-slate-900 border border-slate-805 text-slate-300 hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-600 text-slate-950 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jurusan -->
    <div x-show="editMajorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" style="display: none;">
        <div class="glass-panel max-w-sm w-full p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl relative" @click.away="editMajorModal = false">
            <h3 class="text-lg font-bold text-white">Edit Jurusan</h3>
            
            <form :action="'{{ url('/admin/majors') }}/' + editMajorData.id + '/update'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Jurusan</label>
                    <input type="text" name="name" required x-model="editMajorData.name"
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kode Singkatan</label>
                    <input type="text" name="code" required x-model="editMajorData.code"
                        class="appearance-none block w-full px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editMajorModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-slate-900 border border-slate-805 text-slate-300 hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-600 text-slate-950 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
