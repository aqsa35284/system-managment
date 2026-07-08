<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{-- ⭐ VIEWPORT META TAG FIXED --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Command Center | SaaS Tenant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
    
    {{-- ⭐ Real-time Identity Injector --}}
    <script>
        window.UserId = {{ auth()->id() ?? 'null' }};
        window.UserDeptId = {{ auth()->user()->department_id ?? 'null' }};
        window.currentChatId = null; 
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ⭐ PUSHER SDK CHANNELS INTEGRATION --}}
    <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = false; 
        var pusherInstance = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });
    </script>

    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: #010717; 
            min-height: 100vh;
            overflow-x: hidden;
            color: #eaeef2; 
        }
        
        .sidebar-link.active {
            background: rgba(210, 166, 61, 0.15); 
            color: #d2a63d !important;
            box-shadow: 0 4px 20px rgba(210, 166, 61, 0.15);
            border-left: 3px solid #d2a63d;
        }
        
        .sidebar-link.active i { color: #d2a63d !important; }
        
        .glass-effect {
            background: rgba(234, 238, 242, 0.05); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(234, 238, 242, 0.1);
        }
        
        .sidebar-gradient {
            background: rgba(1, 7, 23, 0.95); 
            backdrop-filter: blur(15px);
        }
        
        .custom-sidebar::-webkit-scrollbar { width: 4px; }
        .custom-sidebar::-webkit-scrollbar-track { background: rgba(234, 238, 242, 0.05); }
        .custom-sidebar::-webkit-scrollbar-thumb { background: rgba(210, 166, 61, 0.5); border-radius: 2px; } 
        
        .bg-animation { position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; overflow: hidden; }
        .bg-animation span { position: absolute; display: block; width: 20px; height: 20px; background: rgba(210, 166, 61, 0.15); animation: move 25s linear infinite; bottom: -150px; }
        
        @keyframes move {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }
        
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
<body class="text-[#eaeef2]">
    <div class="bg-animation">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>

    {{-- ⭐ Toast Container --}}
    <div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <div class="flex min-h-screen relative z-10 w-full overflow-hidden">
        
        {{-- ⭐ SIDEBAR --}}
        <aside id="sidebar" class="w-72 sidebar-gradient border-r border-[#eaeef2]/10 fixed flex flex-col h-full z-50 transition-transform duration-300 custom-sidebar -translate-x-full lg:translate-x-0">
            <div class="p-8 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-[#eaeef2]/5 border border-[#eaeef2]/10 rounded-xl flex items-center justify-center shadow-lg overflow-hidden shrink-0">
                        <img src="{{ asset('storage/logo/Favicon.png') }}" alt="Logo" class="w-full h-full object-contain p-1" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-[#eaeef2] tracking-tight uppercase">Admin <span class="text-[#d2a63d]">Pro</span></h1>
                        
                        @php 
                            // 🟢 FIX: Array protection for plan_type
                            $plan = auth()->user()->company->plan_type ?? 'Starter'; 
                            $planName = is_array($plan) ? ($plan[0] ?? 'Starter') : $plan;
                        @endphp
                        
                        <p class="text-[10px] text-[#d2a63d] opacity-80 uppercase tracking-widest mt-0.5 animate-pulse">
                            {{ $planName }} Plan
                        </p>
                        
                        {{-- 🟢 FIX: Company Name Added --}}
                        <h2 class="text-xs font-bold text-[#eaeef2]/70 truncate mt-1">
                            <i class="fas fa-building text-[10px] mr-1"></i> {{ auth()->user()->company->name ?? 'My Company' }}
                        </h2>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden text-[#eaeef2]/50 hover:text-[#d2a63d]">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto custom-sidebar pb-6">
                
                <p class="px-4 pt-4 pb-2 text-[9px] font-black uppercase tracking-[0.2em] text-[#eaeef2]/30">Core Features</p>

                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-th-large w-5 text-[#d2a63d]"></i> Admin dashboard
                </a>

                <a href="{{ route('admin.departments.index') }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.departments.*') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-sitemap w-5 text-[#d2a63d]"></i> Departments
                </a>

                <a href="{{ route('admin.employees.index') }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.employees.*') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-users w-5 text-[#d2a63d]"></i> Manage Employees
                </a>

                <a href="{{ route('admin.projects.index') }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.projects.*') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-rocket w-5 text-[#d2a63d]"></i> Projects
                </a>

                <a href="{{ route('admin.tasks.index') }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.tasks.*') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-check-double w-5 text-[#d2a63d]"></i> Task Board
                </a>

                <a href="{{ route('admin.attendance.index') }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.attendance.*') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-fingerprint w-5 text-[#d2a63d]"></i> Attendance Log
                </a>

                <a href="{{ route('admin.reports.shifts') }}" class="sidebar-link group flex items-center gap-4 p-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.reports.shifts') ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                    <i class="fas fa-file-invoice w-5 text-[#d2a63d] group-hover:rotate-12 transition-transform"></i> Shift Reports
                </a>

                <p class="px-4 pt-6 pb-2 text-[9px] font-black uppercase tracking-[0.2em] text-[#d2a63d]/50">Premium Add-ons</p>

                {{-- 🚀 SAFE DYNAMIC FEATURE LOCKING (Fix for the TypeError) --}}
                @php
                    $features = config('features.all');
                    if (!is_array($features) || empty($features)) {
                        // Safe Fallback
                        $features = [
                            'screenshot_monitoring' => 'Live Screenshots',
                            'ai_tool_tracking'      => 'AI Intelligence Radar',
                            'team_chat'             => 'Encrypted Team Chat'
                        ];
                    }
                @endphp

                @foreach($features as $slug => $label)
                    @php
                        // 🟢 FIX: Array protection for slugs and labels to prevent crash!
                        $safeSlug = is_string($slug) ? $slug : (is_array($label) ? ($label['slug'] ?? 'feature') : 'feature');
                        $safeLabel = is_array($label) ? ($label['name'] ?? $label['label'] ?? 'Premium Feature') : (is_string($label) ? $label : 'Premium Feature');
                        
                        $hasFeature = false;
                        if (auth()->user() && auth()->user()->company) {
                            $hasFeature = auth()->user()->company->hasFeature($safeSlug);
                        }
                    @endphp

                    @if($hasFeature)
                        <a href="{{ Route::has('admin.' . $safeSlug) ? route('admin.' . $safeSlug) : '#' }}" class="sidebar-link flex items-center gap-4 p-3 rounded-xl font-medium text-sm {{ request()->routeIs('admin.' . $safeSlug) ? 'active' : 'text-[#eaeef2]/70 hover:bg-[#eaeef2]/5 hover:text-[#d2a63d]' }}">
                            <i class="fas fa-microchip w-5 text-[#d2a63d]"></i> {{ $safeLabel }}
                        </a>
                    @else
                        <button onclick="upgradePrompt('Premium Plan', '{{ addslashes($safeLabel) }}')" class="w-full sidebar-link flex items-center justify-between p-3 rounded-xl font-medium text-sm text-[#eaeef2]/30 hover:bg-[#eaeef2]/5 cursor-pointer">
                            <div class="flex items-center gap-4"><i class="fas fa-lock w-5 text-slate-600"></i> {{ $safeLabel }}</div>
                            <span class="text-[8px] bg-slate-800/50 px-2 py-0.5 rounded uppercase font-black tracking-widest">Locked</span>
                        </button>
                    @endif
                @endforeach

            </nav>

            <div class="p-6 border-t border-[#eaeef2]/10">
                <div class="flex items-center gap-3 mb-6 p-3 glass-effect rounded-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[#d2a63d]/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <div class="w-12 h-12 rounded-xl bg-[#d2a63d] flex items-center justify-center text-[#010717] font-bold shadow-lg z-10">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'A' }}
                    </div>
                    <div class="flex-1 overflow-hidden z-10">
                        <p class="text-sm font-bold text-[#eaeef2] truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-[10px] text-emerald-400 uppercase tracking-widest flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> {{ ucfirst($planName) }}
                        </p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 glass-effect text-[#eaeef2] opacity-80 rounded-xl font-bold text-[11px] uppercase tracking-[0.2em] hover:bg-rose-600 hover:text-white border border-transparent hover:border-rose-500 transition-all group">
                        <i class="fas fa-power-off group-hover:animate-pulse text-rose-500 group-hover:text-white"></i> Terminate Session
                    </button>
                </form>
            </div>
        </aside>

        {{-- ⭐ MAIN CONTENT AREA --}}
        <main class="flex-1 lg:ml-72 transition-all min-w-0 w-full flex flex-col overflow-x-hidden">
            
            <header class="lg:hidden glass-effect p-4 flex justify-between items-center sticky top-0 z-40 border-b border-[#eaeef2]/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#d2a63d] rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-shield-alt text-[#010717]"></i></div>
                    <div>
                        <h1 class="font-bold text-[#eaeef2] uppercase tracking-tight leading-tight">Admin <span class="text-[#d2a63d]">Pro</span></h1>
                        {{-- 🟢 FIX: Mobile Header Company Name Added --}}
                        <p class="text-[9px] text-[#d2a63d] uppercase font-bold truncate max-w-[120px]">{{ auth()->user()->company->name ?? 'My Company' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button id="mobile-chat-btn" class="relative text-[#eaeef2]/70 hover:text-[#d2a63d]">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="mobile-chat-badge" class="hidden absolute -top-1 -right-1 w-3 h-3 bg-[#d2a63d] border-2 border-[#010717] rounded-full"></span>
                    </button>
                    <button onclick="toggleSidebar()" class="text-[#eaeef2]/80 text-xl p-2 rounded-xl glass-effect hover:text-[#d2a63d]"><i class="fas fa-bars"></i></button>
                </div>
            </header>

            <div class="w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-[#010717]/80 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- ⭐ SWEETALERT FOR UPGRADE PROMPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ⭐ Notification, Sidebar & WebSockets Engine --}}
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        // Feature Upgrade Prompt
        function upgradePrompt(planName, featureName) {
            Swal.fire({
                icon: 'lock',
                title: 'Access Restricted',
                html: `<p style="color:#eaeef2; margin-bottom:10px;">The <b>${featureName}</b> feature requires the <span style="color:#d2a63d; font-weight:bold;">${planName} Plan</span>.</p><p style="color:#eaeef2; opacity:0.7; font-size:12px;">Please contact your Super Admin or Sales representative to upgrade your tenant account.</p>`,
                background: '#0a1225',
                color: '#eaeef2',
                confirmButtonColor: '#d2a63d',
                confirmButtonText: '<i class="fas fa-rocket mr-2"></i> Understood',
                customClass: {
                    popup: 'border border-[#d2a63d]/30 rounded-2xl shadow-[0_0_30px_rgba(210,166,61,0.2)]',
                    title: 'text-[#d2a63d]'
                }
            });
        }

        function spawnToast(title, message, icon = 'fa-comment-dots', color = 'gold') {
            const container = document.getElementById('toast-container');
            try {
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3'); 
                audio.volume = 0.4; 
                audio.play().catch(()=>{});
            } catch(e){}

            const colors = {
                gold: 'bg-[#010717]/90 border-[#d2a63d]/50 text-[#d2a63d]',
                rose: 'bg-[#010717]/90 border-rose-500/50 text-rose-400',
            };

            const selectedColor = colors[color] || colors.gold;
            const toast = document.createElement('div');
            toast.className = `glass-effect border-l-4 rounded-xl p-4 w-72 md:w-80 shadow-[0_10px_30px_rgba(0,0,0,0.5)] pointer-events-auto flex gap-4 items-start toast-enter ${selectedColor}`;
            
            toast.innerHTML = `
                <div class="mt-0.5 rounded-full p-2 bg-[#010717] shadow-inner">
                    <i class="fas ${icon} text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-[#eaeef2] uppercase tracking-widest mb-1">${title}</h4>
                    <p class="text-[11px] text-[#eaeef2]/70 font-medium leading-relaxed">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-[#eaeef2]/50 hover:text-[#d2a63d] transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.replace('toast-enter', 'toast-leave');
                setTimeout(() => toast.remove(), 400);
            }, 6000);
        }

        let lastMessageCount = 0;
        function checkSystemAlerts() {
            if (!window.UserId) return;
            fetch("{{ url('/chat/unread-count') }}")
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('admin-chat-badge');
                const ping = document.getElementById('admin-chat-ping');
                const mobileBadge = document.getElementById('mobile-chat-badge');
                
                if (data.count > 0) {
                    if(badge) { badge.innerText = data.count; badge.classList.remove('hidden'); }
                    if(ping) ping.classList.remove('hidden');
                    if(mobileBadge) mobileBadge.classList.remove('hidden');

                    if (data.count > lastMessageCount) {
                        spawnToast('Secure Link', `You have ${data.count} pending encrypted messages.`, 'fa-satellite-dish', 'gold');
                    }
                    lastMessageCount = data.count;
                } else {
                    if(badge) badge.classList.add('hidden');
                    if(ping) ping.classList.add('hidden');
                    if(mobileBadge) mobileBadge.classList.add('hidden');
                    lastMessageCount = 0;
                }
            }).catch(e => {});
        }

        checkSystemAlerts();
        setInterval(checkSystemAlerts, 5000);

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Echo !== 'undefined' && window.UserId) {
                Echo.private(`chat.user.${window.UserId}`)
                    .listen('MessageSent', (data) => {
                        if (window.currentChatId !== data.message.sender_id) {
                            spawnToast('Incoming Transmission', `New message from ${data.message.sender.name}`, 'fa-envelope-open-text', 'gold');
                            checkSystemAlerts();
                        }
                    });

                Echo.private(`system.alerts.${window.UserDeptId}`)
                    .listen('ShiftEnded', (data) => {
                        spawnToast('Shift Report', `${data.employee_name} has terminated their shift.`, 'fa-power-off', 'rose');
                    });
            }
        });
    </script>
</body> 
</html>