@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-8" x-data="{ 
    editUserModal: false, 
    editUser: {}, 
    resetPasswordModal: false,
    resetUser: {},
    openEdit(user) {
        this.editUser = {...user};
        this.editUserModal = true;
    },
    openReset(user) {
        this.resetUser = {...user};
        this.resetPasswordModal = true;
    }
}">

    <!-- Header -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-400 hover:text-emerald-400 transition">&larr; Kembali ke Home AI</a>
            <h1 class="text-3xl font-extrabold text-white mt-1 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-slate-400 text-sm mt-1">Mengelola akun guru BK, wali kelas, murid, dan administrator sekolah.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left Column: Form & Import (1 Col) -->
        <div class="space-y-6">
            
            <!-- Left: Form Tambah Pengguna -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 h-fit space-y-4" x-data="{ userRole: 'student' }">
                <h3 class="text-lg font-bold text-white">Tambah Pengguna</h3>
                
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" required 
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                            placeholder="Nama Guru atau Murid">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" required 
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                            placeholder="email@sekolah.sch.id">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Peran Sistem (Role)</label>
                        <select name="role" required x-model="userRole"
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            <option value="student">Murid (Student)</option>
                            <option value="wali_kelas">Wali Kelas (Homeroom)</option>
                            <option value="guru_bk">Guru BK (Counselor)</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <!-- Dropdown Kelas (Hanya untuk Murid) -->
                    <div x-show="userRole === 'student'" x-transition>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Pilih Kelas</label>
                        <select name="class_id" 
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                            <option value="">-- Tanpa Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->major->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-1.5">Kata Sandi</label>
                        <input type="password" name="password" required 
                            class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm" 
                            placeholder="Minimal 6 karakter">
                    </div>

                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-2 rounded-xl text-sm transition">
                        Simpan Pengguna
                    </button>
                </form>
            </div>

            <!-- Left: Import Pengguna via Excel -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 h-fit space-y-4">
                <h3 class="text-lg font-bold text-white">Impor Pengguna</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Tambahkan banyak akun pengguna sekaligus menggunakan berkas Excel (.xlsx). Silakan unduh template di bawah ini terlebih dahulu.</p>
                
                @if(session('import_errors'))
                    <div class="bg-rose-500/10 border border-rose-500/20 p-3 rounded-xl text-xs text-rose-455 space-y-1 max-h-48 overflow-y-auto">
                        <span class="font-bold text-rose-350">Beberapa baris gagal diimpor:</span>
                        <ul class="list-disc pl-3">
                            @foreach(session('import_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-3">
                    <!-- Download Template Button -->
                    <a href="{{ route('admin.users.import-template') }}" 
                        class="w-full flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-sm font-semibold bg-slate-900 hover:bg-slate-805 text-slate-300 border border-slate-800 hover:border-emerald-500/30 transition">
                        📥 Unduh Template Excel (.xlsx)
                    </a>

                    <!-- Import Form -->
                    <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3 pt-2 border-t border-slate-800">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-1.5 font-sans">Pilih Berkas Excel (.xlsx)</label>
                            <input type="file" name="import_file" required accept=".xlsx,.xls"
                                class="appearance-none block w-full px-2 py-1.5 rounded-xl border border-slate-700 bg-slate-900 text-slate-400 focus:outline-none text-sm focus:ring-1 focus:ring-emerald-500">
                        </div>
                        
                        <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2.5 rounded-xl text-sm transition shadow-md shadow-indigo-500/10">
                            Unggah & Impor Pengguna
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right: Daftar User (2 Cols) -->
        <div class="md:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Daftar Pengguna Sistem</h3>
                <div class="flex items-center gap-1 bg-slate-900 border border-slate-800 rounded-lg p-0.5 text-xs">
                    <span class="text-slate-500 px-2">Urutkan:</span>
                    <a href="{{ route('admin.users', ['sort' => 'name']) }}" 
                        class="px-2.5 py-1 rounded {{ $sort === 'name' ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white' }}">
                        Nama
                    </a>
                    <a href="{{ route('admin.users', ['sort' => 'role']) }}" 
                        class="px-2.5 py-1 rounded {{ $sort === 'role' ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white' }}">
                        Peran
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold">
                            <th class="py-3 px-2">Nama Pengguna</th>
                            <th class="py-3 px-2">Peran</th>
                            <th class="py-3 px-2">Kelas/Afiliasi</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr class="border-b border-slate-900/60 hover:bg-slate-900/40 transition">
                                <td class="py-3 px-2">
                                    <div class="font-bold text-white">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $u->email }}</div>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold 
                                        {{ $u->isAdmin() ? 'bg-indigo-500/10 text-indigo-400' : ($u->isGuruBk() ? 'bg-teal-500/10 text-teal-400' : ($u->isWaliKelas() ? 'bg-amber-500/10 text-amber-400' : 'bg-emerald-500/10 text-emerald-400')) }}">
                                        {{ str_replace('_', ' ', $u->role) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-slate-300">
                                    @if($u->role === 'student' && $u->schoolClass)
                                        {{ $u->schoolClass->name }} ({{ $u->schoolClass->major->code }})
                                    @elseif($u->role === 'wali_kelas')
                                        @php
                                            $managedClass = \App\Models\SchoolClass::where('homeroom_teacher_id', $u->id)->first();
                                        @endphp
                                        <span class="text-slate-500">Mengampu:</span> {{ $managedClass ? $managedClass->name : 'Belum Ditautkan' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($u->id !== Auth::id())
                                            <!-- Edit Button -->
                                            <button @click="openEdit({{ json_encode(['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role, 'class_id' => $u->class_id]) }})" 
                                                class="p-1 text-emerald-400 hover:text-emerald-300 hover:bg-slate-900 rounded transition" title="Edit Pengguna">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            
                                            <!-- Reset Sandi Button -->
                                            <button @click="openReset({{ json_encode(['id' => $u->id, 'name' => $u->name]) }})" 
                                                class="p-1 text-indigo-400 hover:text-indigo-300 hover:bg-slate-900 rounded transition" title="Reset Sandi">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m-2 4a5 5 0 110-10 5 5 0 010 10zM19 19a2 2 0 01-2 2h-1.5a1 1 0 01-.707-.293l-3.5-3.5A1 1 0 0111 16.5V15" />
                                                </svg>
                                            </button>
                                            
                                            <!-- Hapus Button -->
                                            <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" 
                                                    class="p-1 text-rose-500 hover:text-rose-455 hover:bg-slate-900 rounded transition" title="Hapus Pengguna">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Reset Sesi Button (Hanya Murid) -->
                                            @if($u->role === 'student')
                                            <form action="{{ route('admin.users.reset-session', $u->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mereset seluruh sesi bimbingan murid ini? Obrolan, memori, skor, dan laporan akan terhapus secara permanen.')" 
                                                    class="p-1 text-amber-500 hover:text-amber-400 hover:bg-slate-900 rounded transition" title="Reset Sesi Bimbingan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 9H18.2" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        @else
                                            <span class="text-[10px] text-slate-500 italic pr-2">Akun Anda</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-500 italic">Belum ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Edit User -->
    <div x-show="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" style="display: none;">
        <div class="glass-panel max-w-sm w-full p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl relative" @click.away="editUserModal = false">
            <h3 class="text-lg font-bold text-white">Edit Pengguna</h3>
            <p class="text-sm text-slate-400">Edit data untuk <strong class="text-slate-200" x-text="editUser.name"></strong></p>
            
            <form :action="'{{ url('/admin/users') }}/' + editUser.id + '/update'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required x-model="editUser.name"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required x-model="editUser.email"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Peran Sistem (Role)</label>
                    <select name="role" required x-model="editUser.role"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        <option value="student">Murid (Student)</option>
                        <option value="wali_kelas">Wali Kelas (Homeroom)</option>
                        <option value="guru_bk">Guru BK (Counselor)</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <!-- Dropdown Kelas (Hanya untuk Murid) -->
                <div x-show="editUser.role === 'student'">
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5">Pilih Kelas</label>
                    <select name="class_id" x-model="editUser.class_id"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm">
                        <option value="">-- Tanpa Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->major->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editUserModal = false"
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

    <!-- Modal Reset Password -->
    <div x-show="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" style="display: none;">
        <div class="glass-panel max-w-sm w-full p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl relative" @click.away="resetPasswordModal = false">
            <h3 class="text-lg font-bold text-white">Reset Password</h3>
            <p class="text-sm text-slate-400">Reset password untuk <strong class="text-slate-200" x-text="resetUser.name"></strong></p>
            
            <form :action="'{{ url('/admin/users') }}/' + resetUser.id + '/reset-password'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1.5 font-sans">Password Baru</label>
                    <input type="password" name="password" required minlength="6"
                        class="appearance-none block w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 text-sm"
                        placeholder="Minimal 6 karakter">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="resetPasswordModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-slate-900 border border-slate-805 text-slate-300 hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-indigo-500 hover:bg-indigo-600 text-white transition">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
