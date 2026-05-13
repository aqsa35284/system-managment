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
        window.UserId = {{ auth()->id() ?? 'null' }};
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

        /* ⭐ Toast Animation */
        @keyframes toast-slide-in {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes toast-slide-out {
            0% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(100%); opacity: 0; }
        }
        .toast-enter { animation: toast-slide-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .toast-leave { animation: toast-slide-out 0.4s ease-in forwards; }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <div class="bg-animation">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>

    {{-- ⭐ Global Toast Container (Messages & Tasks Alerts) --}}
    <div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <div class="flex min-h-screen relative z-10">
        <aside id="sidebar" class="w-72 sidebar-gradient border-r border-white/10 hidden lg:flex flex-col fixed h-full z-50 transition-all duration-300 custom-sidebar">
            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-rocket text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-tight uppercase">Fleet <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">Member</span></h1>
                        <p class="text-[10px] text-cyan-400 uppercase tracking-widest mt-0.5 animate-pulse">Operational Unit</p>
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

                {{-- ⭐ Updated Secure Comms with Ping Badge --}}
                <a href="{{ route('chat.index') }}" class="sidebar-link group flex items-center justify-between p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('chat.*') ? 'active' : 'text-white/70 hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <i class="fas fa-comment-dots w-5 text-cyan-400 group-hover:scale-110 transition-transform"></i> 
                            <span id="employee-chat-ping" class="hidden absolute -top-1 -right-1 w-2 h-2 bg-rose-500 rounded-full animate-ping"></span>
                        </div>
                        Secure Comms
                    </div>
                    <span id="employee-chat-badge" class="hidden min-w-[22px] h-[22px] bg-gradient-to-r from-rose-500 to-rose-600 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(225,29,72,0.6)]">0</span>
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
                <div class="flex items-center gap-3 mb-6 p-3 glass-effect rounded-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-purple-500/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg z-10">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                    </div>
                    <div class="flex-1 overflow-hidden z-10">
                        <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name ?? 'Employee' }}</p>
                        <p class="text-[10px] text-emerald-400 uppercase tracking-widest flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Active
                        </p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 glass-effect text-rose-400 rounded-xl font-bold text-[11px] uppercase tracking-[0.2em] hover:bg-rose-500 hover:text-white border border-transparent hover:border-rose-400 transition-all group">
                        <i class="fas fa-power-off group-hover:animate-pulse"></i> Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 lg:ml-72 transition-all">
            <header class="lg:hidden glass-effect p-4 flex justify-between items-center sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-rocket text-white"></i></div>
                    <h1 class="font-bold text-white uppercase tracking-tight">Fleet <span class="text-purple-400">Member</span></h1>
                </div>
                <div class="flex items-center gap-4">
                    {{-- ⭐ Mobile Notification Bell --}}
                    <button id="mobile-alert-btn" class="relative text-slate-300">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="mobile-chat-badge" class="hidden absolute -top-1 -right-1 w-3 h-3 bg-rose-500 border-2 border-[#0F172A] rounded-full"></span>
                    </button>
                    <button onclick="toggleSidebar()" class="text-white/80 text-xl p-2 rounded-xl glass-effect"><i class="fas fa-bars"></i></button>
                </div>
            </header>

            <div class="p-4 lg:p-8">@yield('content')</div>
        </main>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- ⭐ Universal Notification Engine (Messages & Tasks) --}}
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        // ⭐ Toast Notification Generator
        function spawnToast(title, message, icon = 'fa-comment-dots', color = 'cyan') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            // Notification Beep Sound
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA'); 
                audio.volume = 0.5; audio.play().catch(()=>{});
            } catch(e){}

            // Dynamic Color Theming for different events
            const colors = {
                cyan: 'bg-cyan-600/20 border-cyan-500/50 text-cyan-400',
                purple: 'bg-purple-600/20 border-purple-500/50 text-purple-400',
                rose: 'bg-rose-600/20 border-rose-500/50 text-rose-400',
                emerald: 'bg-emerald-600/20 border-emerald-500/50 text-emerald-400'
            };

            const toast = document.createElement('div');
            toast.className = `glass-effect border-l-4 rounded-xl p-4 w-80 shadow-[0_10px_30px_rgba(0,0,0,0.5)] pointer-events-auto flex gap-4 items-start toast-enter ${colors[color]}`;
            
            toast.innerHTML = `
                <div class="mt-0.5 rounded-full p-2 bg-black/30 shadow-inner">
                    <i class="fas ${icon} text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">${title}</h4>
                    <p class="text-[11px] text-slate-300 font-medium leading-relaxed">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-500 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            // Auto-remove after 6 seconds
            setTimeout(() => {
                toast.classList.replace('toast-enter', 'toast-leave');
                setTimeout(() => toast.remove(), 400); 
            }, 6000);
        }

        // ⭐ Global Unread Message Poller (Checks every 5 sec)
        let lastMessageCount = 0;

        function checkSystemAlerts() {
            if (!window.UserId) return;

            fetch("{{ url('/chat/unread-count') }}")
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('employee-chat-badge');
                const ping = document.getElementById('employee-chat-ping');
                const mobileBadge = document.getElementById('mobile-chat-badge');
                
                if (data.count > 0) {
                    // Update Sidebar & Mobile Badges
                    if(badge) { badge.innerText = data.count; badge.classList.remove('hidden'); }
                    if(ping) ping.classList.remove('hidden');
                    if(mobileBadge) mobileBadge.classList.remove('hidden');

                    // If New Message Arrived -> Show Toast Alert
                    if (data.count > lastMessageCount) {
                        spawnToast(
                            'Admin Transmission', 
                            `Secure message received from <b class="text-white">${data.sender_name}</b>.`, 
                            'fa-envelope-open-text', 
                            'cyan'
                        );
                    }
                    lastMessageCount = data.count;
                } else {
                    // Hide Badges if 0
                    if(badge) badge.classList.add('hidden');
                    if(ping) ping.classList.add('hidden');
                    if(mobileBadge) mobileBadge.classList.add('hidden');
                    lastMessageCount = 0;
                }
            }).catch(e => {});
        }

        // Run checker immediately, then every 5 seconds
        checkSystemAlerts();
        setInterval(checkSystemAlerts, 5000);

        // ⭐ WebSockets Listeners (For Instant Updates & Tasks)
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Echo !== 'undefined' && window.UserId) {
                
                // 1. Instant Chat Message Listener
                Echo.private(`chat.user.${window.UserId}`)
                    .listen('MessageSent', (data) => {
                        if (window.currentChatId !== data.message.sender_id) {
                            checkSystemAlerts(); 
                        }
                    });

                // 2. Instant Task Assignment Listener (Agar Admin Task assign kare)
                Echo.private(`user.alerts.${window.UserId}`)
                    .listen('TaskAssigned', (data) => {
                        // Jab koi naya task milega, Purple color ka notification aayega
                        spawnToast(
                            'New Mission Assigned', 
                            `Task: ${data.task_title}`, 
                            'fa-tasks', 
                            'purple'
                        );
                    })
                    // System Updates / Admin Announcements
                    .listen('SystemAnnouncement', (data) => {
                        spawnToast(
                            'System Update', 
                            data.message, 
                            'fa-bullhorn', 
                            'emerald'
                        );
                    });
            }
        });
        
        // Example: How to trigger a task Toast manually (For Testing)
        // setTimeout(() => spawnToast('New Mission Assigned', 'Build the auth middleware.', 'fa-tasks', 'purple'), 3000);
    </script>
</body>
</html>