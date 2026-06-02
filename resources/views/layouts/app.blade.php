<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Blog Dashboard') - PolinesBlog</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f5ff',
                            100: '#e0ebff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            900: '#1e3a8a',
                        },
                        darkBg: '#090d16',
                        darkCard: '#131b2e',
                        darkBorder: '#1e294b'
                    }
                }
            }
        }
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #090d16;
            color: #f8fafc;
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #090d16;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e294b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
        }
        .glass-panel {
            background: rgba(19, 27, 46, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(30, 41, 75, 0.5);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col font-sans overflow-x-hidden">

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-md w-full"></div>

    <!-- Header Navigation -->
    <header class="glass-panel sticky top-0 z-40 w-full border-b border-darkBorder/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/posts" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-indigo-400">PolinesBlog</span>
                    <span class="text-[10px] block text-cyan-400/80 -mt-1 font-semibold tracking-widest uppercase">Integrative API</span>
                </div>
            </a>

            <!-- Header Menu -->
            <nav class="flex items-center gap-4">
                <a href="/posts" class="px-4 py-2 rounded-lg text-sm font-medium hover:text-cyan-400 transition-colors duration-200" id="nav-all-posts">Semua Artikel</a>
                
                <!-- Guest Menu -->
                <div id="guest-menu" class="flex items-center gap-3">
                    <a href="/login" class="px-4 py-2 rounded-lg text-sm font-medium hover:text-white text-slate-400 transition-colors duration-200">Masuk</a>
                    <a href="/register" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold tracking-wide shadow-md shadow-indigo-600/20 transition-all duration-200">Daftar</a>
                </div>

                <!-- User Menu -->
                <div id="user-menu" class="hidden flex items-center gap-4">
                    <a href="/posts?filter=mine" class="px-4 py-2 rounded-lg text-sm font-medium hover:text-cyan-400 transition-colors duration-200" id="nav-my-posts">Artikel Saya</a>
                    <a href="/posts/create" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white text-sm font-semibold tracking-wide shadow-lg shadow-indigo-600/20 flex items-center gap-2 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tulis Artikel
                    </a>
                    
                    <div class="h-8 w-px bg-darkBorder"></div>
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col text-right">
                            <span id="user-display-name" class="text-sm font-semibold text-slate-200">User</span>
                            <span class="text-[10px] text-cyan-400/80 uppercase tracking-wider font-medium">Penulis</span>
                        </div>
                        <button onclick="handleGlobalLogout()" class="p-2 rounded-lg hover:bg-red-500/10 text-slate-400 hover:text-red-400 transition-all duration-200" title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-panel border-t border-darkBorder/40 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-slate-500">
            &copy; 2026 PolinesBlog. Seluruh hak cipta dilindungi undang-undang.
        </div>
    </footer>

    <!-- Global Application Script -->
    <script>
        // Global Auth State
        const auth = {
            token: localStorage.getItem('jwt_token') || null,
            id: parseInt(localStorage.getItem('user_id')) || null,
            name: localStorage.getItem('user_name') || null
        };

        // Initialize Global layout UI
        document.addEventListener('DOMContentLoaded', () => {
            updateGlobalNavUI();
            highlightActiveNav();
        });

        // Highlight Active Tab based on path
        function highlightActiveNav() {
            const path = window.location.pathname;
            const search = window.location.search;
            const navAll = document.getElementById('nav-all-posts');
            const navMy = document.getElementById('nav-my-posts');

            if (navAll) {
                if (path === '/posts' && !search.includes('mine')) {
                    navAll.className = 'px-4 py-2 rounded-lg text-sm font-medium text-cyan-400 bg-cyan-500/10';
                } else {
                    navAll.className = 'px-4 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white';
                }
            }

            if (navMy) {
                if (path === '/posts' && search.includes('mine')) {
                    navMy.className = 'px-4 py-2 rounded-lg text-sm font-medium text-cyan-400 bg-cyan-500/10';
                } else {
                    navMy.className = 'px-4 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white';
                }
            }
        }

        // Update Nav Menu depending on token existence
        function updateGlobalNavUI() {
            const guestMenu = document.getElementById('guest-menu');
            const userMenu = document.getElementById('user-menu');
            const displayName = document.getElementById('user-display-name');

            if (auth.token) {
                if (guestMenu) guestMenu.classList.add('hidden');
                if (userMenu) userMenu.classList.remove('hidden');
                if (displayName) displayName.textContent = auth.name || 'Penulis';
            } else {
                if (guestMenu) guestMenu.classList.remove('hidden');
                if (userMenu) userMenu.classList.add('hidden');
            }
        }

        // Show toast alert
        function showGlobalToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let typeClasses = 'border-l-4 shadow-lg ';
            let iconSvg = '';
            
            if (type === 'success') {
                typeClasses += 'bg-emerald-950/85 border-emerald-500 text-emerald-200';
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else if (type === 'error') {
                typeClasses += 'bg-red-950/85 border-red-500 text-red-200';
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else {
                typeClasses += 'bg-cyan-950/85 border-cyan-500 text-cyan-200';
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            }
            
            toast.className = `glass-panel rounded-2xl p-4 flex items-center gap-3 transition-all duration-300 ease-out transform translate-x-12 opacity-0 ${typeClasses}`;
            toast.innerHTML = `
                <div class="shrink-0">${iconSvg}</div>
                <div class="flex-grow text-sm font-semibold">${message}</div>
                <button class="text-slate-400 hover:text-white" onclick="this.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-x-12', 'opacity-0');
            }, 10);
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Unified API Request helper
        async function runApiRequest(endpoint, options = {}) {
            const url = `/api/v1${endpoint}`;
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            };

            if (auth.token) {
                headers['Authorization'] = `Bearer ${auth.token}`;
            }

            try {
                const response = await fetch(url, { ...options, headers });
                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 401 && auth.token) {
                        handleTokenExpiration();
                        throw new Error('Sesi Anda berakhir. Silakan masuk kembali.');
                    }
                    throw new Error(data.message || data.error || 'Terjadi kesalahan sistem.');
                }
                return data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }

        // Log out when token dies
        function handleTokenExpiration() {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user_id');
            localStorage.removeItem('user_name');
            auth.token = null;
            auth.id = null;
            auth.name = null;
            window.location.href = '/login';
        }

        // Global logout action
        async function handleGlobalLogout() {
            try {
                await runApiRequest('/logout', { method: 'POST' });
            } catch (err) {
                console.warn('Logout error:', err);
            } finally {
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('user_id');
                localStorage.removeItem('user_name');
                auth.token = null;
                auth.id = null;
                auth.name = null;
                window.location.href = '/posts';
            }
        }

        // Check if page requires authentication
        function requireAuth() {
            if (!auth.token) {
                window.location.href = '/login';
            }
        }

        // Escape helper for templates
        function escapeText(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
    @yield('scripts')
</body>
</html>
