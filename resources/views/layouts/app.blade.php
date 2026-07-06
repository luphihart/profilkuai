<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Profilku AI') - Kenali Diri, Temukan Potensi</title>
    
    <!-- Google Fonts: Inter (Unified Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite Assets (Tailwind & Alpine) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body, h1, h2, h3, h4, h5, h6, input, select, textarea, button, .font-display {
            font-family: 'Inter', sans-serif !important;
        }
        /* Custom Glassmorphism Styles */
        .glass-panel {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            background: rgba(30, 41, 59, 0.55);
            border-color: rgba(16, 185, 129, 0.2);
            transform: translateY(-2px);
        }
        .bento-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.4);
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.6);
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans antialiased flex flex-col selection:bg-emerald-500/30 selection:text-emerald-400">

    <!-- Header Navbar with Alpine for Mobile Responsiveness -->
    <header class="glass-panel border-b border-slate-800/80 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <span class="text-slate-950 font-black text-xl tracking-tighter">P</span>
                    </div>
                    <div>
                        <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">Profilku AI</span>
                        <span class="hidden md:inline-block text-xs text-slate-400 ml-2 border-l border-slate-700 pl-2">Sistem Profiling SMK</span>
                    </div>
                </div>

                <!-- Navigation Controls (Desktop) -->
                <nav class="hidden sm:flex items-center gap-4">
                    @auth
                        <!-- Dashboard Links based on Role -->
                        <div class="flex items-center gap-2">
                            @if(Auth::user()->isStudent())
                                <a href="{{ route('student.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('student.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Home</a>
                                <a href="{{ route('student.chat') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('student.chat') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Konseling AI</a>
                            @elseif(Auth::user()->isGuruBk())
                                <a href="{{ route('bk.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('bk.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Dashboard BK</a>
                            @elseif(Auth::user()->isWaliKelas())
                                <a href="{{ route('wali.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('wali.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Dashboard Wali Kelas</a>
                            @elseif(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Home AI</a>
                                <a href="{{ route('admin.users') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('admin.users') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Pengguna</a>
                                <a href="{{ route('admin.classes.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('admin.classes.index') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Kelas & Plotting</a>
                                <a href="{{ route('admin.kb') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('admin.kb') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Knowledge Base</a>
                                <a href="{{ route('admin.rules') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('admin.rules') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Aturan AI</a>
                                <a href="{{ route('admin.settings') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ Request::routeIs('admin.settings') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Pengaturan</a>
                            @endif
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center gap-3 ml-2 border-l border-slate-800 pl-4">
                            <div class="text-right hidden md:block">
                                <p class="text-xs font-semibold text-slate-200">{{ Auth::user()->name }}</p>
                                <span class="text-[11px] uppercase font-bold text-slate-400 bg-slate-900 px-2 py-0.5 rounded-full border border-slate-800">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-900 transition" title="Logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest Mode Links -->
                        @if(!Request::routeIs('login'))
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Masuk</a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-600 hover:to-teal-500 text-slate-950 font-bold px-4 py-2 rounded-xl text-sm transition shadow-md shadow-emerald-500/10">Registrasi</a>
                        @endif
                    @endauth
                </nav>

                <!-- Mobile Header Actions & Menu Toggle -->
                <div class="flex items-center sm:hidden gap-3">
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center text-slate-400 hover:text-rose-400 rounded-lg transition" title="Logout">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                             </button>
                        </form>
                    @endauth
                    
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center text-slate-400 hover:text-white rounded-lg hover:bg-slate-900 transition focus:outline-none">
                        <!-- Hamburger Icon -->
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Close Icon -->
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="mobileMenuOpen" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" x-transition class="sm:hidden border-t border-slate-800 bg-slate-950/95 py-3 px-4 space-y-1" style="display: none;">
            @auth
                @if(Auth::user()->isStudent())
                    <a href="{{ route('student.dashboard') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('student.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Home</a>
                    <a href="{{ route('student.chat') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('student.chat') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Konseling AI</a>
                @elseif(Auth::user()->isGuruBk())
                    <a href="{{ route('bk.dashboard') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('bk.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Dashboard BK</a>
                @elseif(Auth::user()->isWaliKelas())
                    <a href="{{ route('wali.dashboard') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('wali.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Dashboard Wali Kelas</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Home AI</a>
                    <a href="{{ route('admin.users') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('admin.users') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Pengguna</a>
                    <a href="{{ route('admin.classes.index') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('admin.classes.index') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Kelas & Plotting</a>
                    <a href="{{ route('admin.kb') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('admin.kb') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Knowledge Base</a>
                    <a href="{{ route('admin.rules') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('admin.rules') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Aturan AI</a>
                    <a href="{{ route('admin.settings') }}" class="block px-3 py-3 rounded-lg text-sm font-medium transition min-h-[44px] flex items-center {{ Request::routeIs('admin.settings') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-300 hover:text-white' }}">Pengaturan</a>
                @endif
                
                <div class="border-t border-slate-900 pt-3 mt-2 px-3">
                    <p class="text-sm font-semibold text-slate-200">{{ Auth::user()->name }}</p>
                    <span class="text-[11px] uppercase font-bold text-slate-400 bg-slate-900 px-2.5 py-0.5 rounded-full border border-slate-800 mt-1 inline-block">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                </div>
            @else
                @if(!Request::routeIs('login'))
                    <a href="{{ route('login') }}" class="block px-3 py-3 rounded-lg text-sm font-medium text-slate-300 hover:text-white transition min-h-[44px] flex items-center">Masuk</a>
                    <a href="{{ route('register') }}" class="block text-center bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 font-bold px-4 py-3 rounded-xl text-sm transition min-h-[44px] flex items-center justify-center">Registrasi</a>
                @endif
            @endauth
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-3">
                <span class="font-bold">✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl border border-rose-500/20 bg-rose-500/10 text-rose-400 text-sm flex items-center gap-3">
                <span class="font-bold">⚠️</span> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 py-6 text-center text-xs text-slate-500 mt-auto bg-slate-950/80">
        <p>&copy; {{ date('Y') }} Profilku AI. Kenali Diri, Temukan Potensi, Rencanakan Masa Depan.</p>
    </footer>

</body>
</html>
