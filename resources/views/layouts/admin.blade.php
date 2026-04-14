<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Command Center | Office System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- ⭐ Real-time Identity Injector --}}
    <script>
        window.UserId = {{ auth()->id() }};
        window.UserDeptId = {{ auth()->user()->department_id ?? 'null' }};
        window.currentChatId = null; 
        window.currentDeptId = null;
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
        }
        
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.3), rgba(79, 70, 229, 0.2));
            color: white !important;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.25);
            border-left: 3px solid #3b82f6;
        }
        
        .sidebar-link.active i { 
            color: #93c5fd !important;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, rgba(30, 27, 75, 0.9), rgba(15, 12, 41, 0.9));
            backdrop-filter: blur(15px);
        }
        
        .custom-sidebar::-webkit-scrollbar { width: 4px; }
        .custom-sidebar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        .custom-sidebar::-webkit-scrollbar-thumb { background: rgba(37, 99, 235, 0.3); border-radius: 2px; }
        
        .bg-animation { position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; overflow: hidden; }
        .bg-animation span { position: absolute; display: block; width: 20px; height: 20px; background: rgba(37, 99, 235, 0.2); animation: move 25s linear infinite; bottom: -150px; }
        
        @keyframes move {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }
        
        .notification-slide { animation: slideDown 0.5s ease-out; }
        @keyframes slideDown { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <div class="bg-animation">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>

    <div class="flex min-h-screen relative z-10">
        <aside id="sidebar" class="w-72 sidebar-gradient border-r border-white/10 hidden lg:flex flex-col fixed h-full z-50 transition-all duration-300 custom-sidebar">
            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-tight uppercase">Admin <span class="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Pro</span></h1>
                        <p class="text-xs text-white/60 uppercase tracking-widest mt-1">Fleet Management</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-6 space-y-2 overflow-y-auto custom-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-th-large w-5 text-blue-400"></i> Command Center
                </a>

                <a href="{{ route('admin.departments.index') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.departments.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-sitemap w-5 text-indigo-400"></i> Nodes (Depts)
                </a>

                <a href="{{ route('admin.employees.index') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.employees.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-users w-5 text-emerald-400"></i> Member Fleet
                </a>

                <a href="{{ route('admin.projects.index') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.projects.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-rocket w-5 text-amber-400"></i> Project Fleet
                </a>

                <a href="{{ route('admin.tasks.index') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.tasks.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-check-double w-5 text-rose-400"></i> Task Board
                </a>

                {{-- ⭐ Updated Comms Hub --}}
                <a href="{{ route('chat.index') }}" class="sidebar-link group flex items-center justify-between p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('chat.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-comment-dots w-5 text-blue-500 group-hover:animate-bounce"></i> 
                        Comms Hub
                    </div>
                    <span id="admin-chat-badge" class="hidden min-w-[20px] h-5 bg-rose-600 text-white text-[10px] font-black rounded-full flex items-center justify-center px-1 shadow-lg shadow-rose-500/40">0</span>
                </a>

                <a href="{{ route('admin.attendance.index') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.attendance.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-fingerprint w-5 text-indigo-400"></i> Fleet Attendance
                </a>
{{-- ⭐ New: Shift Intelligence (Reports) --}}
<a href="{{ route('admin.reports.shifts') }}" class="sidebar-link group flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.reports.shifts') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
    <div class="relative">
        <i class="fas fa-file-invoice w-5 text-cyan-400 group-hover:rotate-12 transition-transform"></i>
        {{-- Chota sa dot indicator taake futuristic lage --}}
        <span class="absolute -top-1 -right-1 w-2 h-2 bg-cyan-500 rounded-full animate-ping opacity-75"></span>
    </div>
    Shift Intelligence
</a>
                <div class="pt-4 mt-4 border-t border-white/10">
                    <a href="#" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm text-white/70 hover:bg-white/5 transition-all">
                        <i class="fas fa-chart-bar text-cyan-400 w-5"></i> Analytics
                    </a>
                </div>
            </nav>

            <div class="p-6 border-t border-white/10">
                <div class="flex items-center gap-3 mb-6 p-3 glass-effect rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'A' }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-xs text-white/60 uppercase tracking-wider">Administrator</p>
                    </div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 glass-effect text-rose-400 rounded-xl font-medium text-sm uppercase tracking-wider hover:bg-rose-500/10 transition-all group">
                        <i class="fas fa-power-off group-hover:animate-pulse"></i> Terminate Session
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 lg:ml-72 transition-all">
            <header class="lg:hidden glass-effect p-4 flex justify-between items-center sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-shield-alt text-white"></i></div>
                    <h1 class="font-bold text-white uppercase tracking-tight">Admin <span class="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Pro</span></h1>
                </div>
                <button onclick="toggleSidebar()" class="text-white/80 text-xl p-2 rounded-xl glass-effect"><i class="fas fa-bars"></i></button>
            </header>

            @if(session('success'))
            <div class="p-6 pb-0">
                <div class="glass-effect bg-emerald-500/20 border border-emerald-500/30 text-white p-4 rounded-2xl font-medium uppercase text-xs tracking-wider flex items-center gap-3 notification-slide">
                    <i class="fas fa-check-circle text-emerald-400 text-lg"></i> <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <div class="p-4 lg:p-8">@yield('content')</div>
        </main>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('hidden');
            overlay.classList.toggle('hidden');
        }

        // ⭐ Real-time Message Listener for Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Echo !== 'undefined' && window.UserId) {
                Echo.private(`chat.user.${window.UserId}`)
                    .listen('MessageSent', (data) => {
                        const badge = document.getElementById('admin-chat-badge');
                        // Update badge only if not currently chatting with sender
                        if (badge && window.currentChatId !== data.message.sender_id) {
                            badge.classList.remove('hidden');
                            let currentCount = parseInt(badge.innerText) || 0;
                            badge.innerText = currentCount + 1;
                        }
                    });
            }
        });
    </script>
</body>
</html>