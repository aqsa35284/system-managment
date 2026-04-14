<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal | Enterprise System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- ⭐ Real-time Identity Injector for Reverb --}}
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
            background: linear-gradient(90deg, rgba(124, 58, 237, 0.3), rgba(79, 70, 229, 0.2));
            color: white !important;
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.25);
            border-left: 3px solid #7c3aed;
        }
        
        .sidebar-link.active i { color: #a78bfa !important; }
        
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
        .custom-sidebar::-webkit-scrollbar-thumb { background: rgba(124, 58, 237, 0.3); border-radius: 2px; }
        
        .bg-animation { position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; overflow: hidden; }
        .bg-animation span { position: absolute; display: block; width: 20px; height: 20px; background: rgba(124, 58, 237, 0.2); animation: move 25s linear infinite; bottom: -150px; }
        
        @keyframes move {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }
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
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-rocket text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-tight uppercase">Fleet <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">Member</span></h1>
                        <p class="text-xs text-white/60 uppercase tracking-widest mt-1">Operational Unit</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-6 space-y-2 overflow-y-auto custom-sidebar">
                <a href="{{ route('employee.dashboard') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employee.dashboard') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-terminal w-5 text-purple-400"></i> My Terminal
                </a>

                <a href="#" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm text-white/70 hover:bg-white/5 transition-all">
                    <i class="fas fa-rocket text-amber-400 w-5"></i> Project Fleet
                </a>

                <a href="#" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm text-white/70 hover:bg-white/5 transition-all">
                    <i class="fas fa-chart-pie text-emerald-400 w-5"></i> My Efficiency
                </a>

                {{-- ⭐ Updated Comms Hub for Employee --}}
                <a href="{{ route('chat.index') }}" class="sidebar-link group flex items-center justify-between p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('chat.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-comment-dots w-5 text-cyan-400 group-hover:animate-bounce"></i> 
                        Secure Comms
                    </div>
                    <span id="employee-chat-badge" class="hidden min-w-[20px] h-5 bg-purple-600 text-white text-[10px] font-black rounded-full flex items-center justify-center px-1 shadow-lg shadow-purple-500/40">0</span>
                </a>

                <a href="{{ route('employee.attendance.index') }}" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employee.attendance.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <i class="fas fa-clipboard-list w-5 text-indigo-400"></i> My Fleet Logs
                </a>
                
                <div class="pt-4 mt-4 border-t border-white/10">
                    <a href="#" class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm text-white/70 hover:bg-white/5 transition-all">
                        <i class="fas fa-cog text-gray-400 w-5"></i> Settings
                    </a>
                </div>
            </nav>

            <div class="p-6 border-t border-white/10">
                <div class="flex items-center gap-3 mb-6 p-3 glass-effect rounded-xl">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-white/60 uppercase tracking-wider">Member Fleet</p>
                    </div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 glass-effect text-rose-400 rounded-xl font-medium text-sm uppercase transition-all group">
                        <i class="fas fa-power-off group-hover:animate-pulse"></i> Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 lg:ml-72 transition-all">
            <header class="lg:hidden glass-effect p-4 flex justify-between items-center sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-rocket text-white"></i></div>
                    <h1 class="font-bold text-white uppercase tracking-tight">Fleet <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">Member</span></h1>
                </div>
                <button onclick="toggleSidebar()" class="text-white/80 text-xl p-2 rounded-xl glass-effect"><i class="fas fa-bars"></i></button>
            </header>

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

        // ⭐ Real-time Message Listener for Employee Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Echo !== 'undefined' && window.UserId) {
                Echo.private(`chat.user.${window.UserId}`)
                    .listen('MessageSent', (data) => {
                        const badge = document.getElementById('employee-chat-badge');
                        // Sirf tab badge dikhao jab woh us sender se chat na kar raha ho
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