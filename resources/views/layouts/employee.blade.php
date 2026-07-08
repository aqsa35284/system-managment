<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal | Enterprise System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

    {{-- ⭐ Real-time Identity Injector for Reverb --}}
    <script>
        window.UserId = {{ auth()->id() ?? 'null' }};
        window.UserDeptId = {{ auth()->user()->department_id ?? 'null' }};
        window.currentChatId = null; 
        window.currentDeptId = null;
    </script>

    <link rel="stylesheet" href="{{ asset('build/assets/app-DoURTLbS.css') }}">
    <script src="{{ asset('build/assets/app-BTIP2hA6.js') }}" defer></script>

    {{-- ⭐ SWEETALERT FOR UPGRADE PROMPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #010717;
            min-height: 100vh;
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(210, 166, 61, 0.2), rgba(210, 166, 61, 0.08));
            color: #eaeef2 !important;
            box-shadow: 0 4px 20px rgba(210, 166, 61, 0.15);
            border-left: 3px solid #d2a63d;
        }

        .sidebar-link.active i { color: #d2a63d !important; }

        .glass-effect {
            background: rgba(210, 166, 61, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(210, 166, 61, 0.12);
        }

        .sidebar-gradient {
            background: linear-gradient(180deg, rgba(3, 10, 28, 0.97), rgba(1, 7, 23, 0.97));
            backdrop-filter: blur(15px);
        }

        .custom-sidebar::-webkit-scrollbar { width: 4px; }
        .custom-sidebar::-webkit-scrollbar-thumb { background: rgba(210, 166, 61, 0.3); border-radius: 2px; }

        .bg-animation { position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; overflow: hidden; }
        .bg-animation span {
            position: absolute; display: block;
            width: 20px; height: 20px;
            background: rgba(210, 166, 61, 0.08);
            animation: move 25s linear infinite;
            bottom: -150px;
        }

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
<body class="overflow-x-hidden" style="color: #eaeef2;">
    <div class="bg-animation">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    {{-- ⭐ Global Toast Container (Messages & Tasks Alerts) --}}
    <div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <div class="flex min-h-screen relative z-10">

        {{-- SIDEBAR --}}
        <aside id="sidebar" class="w-72 sidebar-gradient hidden lg:flex flex-col fixed h-full z-50 transition-all duration-300 custom-sidebar" style="border-right: 1px solid rgba(210, 166, 61, 0.12);">

            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #d2a63d, #a07828);">
                         <img src="{{ asset('storage/logo/hori.svg') }}" alt="Logo" class="w-full h-full object-contain p-1" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold uppercase tracking-tight" style="color: #eaeef2;">Employee <span style="color: #d2a63d;">Member</span></h1>
                        <p class="text-[10px] uppercase tracking-widest mt-0.5 animate-pulse" style="color: #d2a63d;">Operational Unit</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-6 space-y-2 overflow-y-auto custom-sidebar">
                
                <p class="px-4 pt-2 pb-2 text-[8px] font-black uppercase tracking-[0.2em] text-[#eaeef2]/30">Core Operations</p>

                <a href="{{ route('employee.dashboard') }}"
                   class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}"
                   style="{{ !request()->routeIs('employee.dashboard') ? 'color: rgba(234,238,242,0.6);' : '' }}"
                   onmouseover="{{ !request()->routeIs('employee.dashboard') ? 'this.style.background=\"rgba(210,166,61,0.06)\"' : '' }}"
                   onmouseout="{{ !request()->routeIs('employee.dashboard') ? 'this.style.background=\"transparent\"' : '' }}">
                    <i class="fas fa-terminal w-5" style="color: #d2a63d;"></i> Employee Dashboard
                </a>

                <a href="{{ route('employee.projects.index') }}"
                   class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employee.projects.*') ? 'active' : '' }}"
                   style="{{ !request()->routeIs('employee.projects.*') ? 'color: rgba(234,238,242,0.6);' : '' }}"
                   onmouseover="{{ !request()->routeIs('employee.projects.*') ? 'this.style.background=\"rgba(210,166,61,0.06)\"' : '' }}"
                   onmouseout="{{ !request()->routeIs('employee.projects.*') ? 'this.style.background=\"transparent\"' : '' }}">
                    <i class="fas fa-rocket w-5" style="color: #d2a63d;"></i> Project
                </a>

                <a href="{{ route('employee.tasks.index') }}"
                   class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employee.tasks.*') ? 'active' : '' }}"
                   style="{{ !request()->routeIs('employee.tasks.*') ? 'color: rgba(234,238,242,0.6);' : '' }}"
                   onmouseover="{{ !request()->routeIs('employee.tasks.*') ? 'this.style.background=\"rgba(210,166,61,0.06)\"' : '' }}"
                   onmouseout="{{ !request()->routeIs('employee.tasks.*') ? 'this.style.background=\"transparent\"' : '' }}">
                    <i class="fas fa-chart-pie w-5" style="color: #d2a63d;"></i> MyTask
                </a>

                <a href="{{ route('employee.attendance.index') }}"
                   class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}"
                   style="{{ !request()->routeIs('employee.attendance.*') ? 'color: rgba(234,238,242,0.6);' : '' }}"
                   onmouseover="{{ !request()->routeIs('employee.attendance.*') ? 'this.style.background=\"rgba(210,166,61,0.06)\"' : '' }}"
                   onmouseout="{{ !request()->routeIs('employee.attendance.*') ? 'this.style.background=\"transparent\"' : '' }}">
                    <i class="fas fa-clipboard-list w-5" style="color: #d2a63d;"></i> Attendances
                </a>

                <p class="px-4 pt-6 pb-2 text-[8px] font-black uppercase tracking-[0.2em] text-[#d2a63d]/50">Team & Comms</p>

                {{-- ⭐ 🔒 PRO FEATURE: Secure Comms (Whatsapp) --}}
                @if(auth()->user()->company->hasFeature('team_chat'))
                    <a href="{{ route('chat.index') }}"
                       class="sidebar-link group flex items-center justify-between p-4 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                       style="{{ !request()->routeIs('chat.*') ? 'color: rgba(234,238,242,0.6);' : '' }}"
                       onmouseover="{{ !request()->routeIs('chat.*') ? 'this.style.background=\"rgba(210,166,61,0.06)\"' : '' }}"
                       onmouseout="{{ !request()->routeIs('chat.*') ? 'this.style.background=\"transparent\"' : '' }}">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <i class="fas fa-comment-dots w-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"></i>
                                <span id="employee-chat-ping" class="hidden absolute -top-1 -right-1 w-2 h-2 rounded-full animate-ping" style="background-color: #ef4444;"></span>
                            </div>
                            Whatsapp
                        </div>
                        <span id="employee-chat-badge" class="hidden min-w-[22px] h-[22px] text-white text-[10px] font-black rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 0 15px rgba(239,68,68,0.5);">0</span>
                    </a>
                @else
                    <button onclick="upgradePrompt('Pro', 'Team Communication')" 
                            class="w-full sidebar-link flex items-center justify-between p-4 rounded-xl font-medium text-sm transition-all"
                            style="color: rgba(234,238,242,0.3); cursor: not-allowed;"
                            onmouseover="this.style.background='rgba(234,238,242,0.05)'"
                            onmouseout="this.style.background='transparent'">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-comment-dots w-5" style="color: rgba(234,238,242,0.3);"></i> Whatsapp
                        </div>
                        <span class="text-[8px] px-1.5 py-0.5 rounded uppercase font-black tracking-widest flex items-center gap-1" style="background: rgba(168,85,247,0.1); color: #a855f7; border: 1px solid rgba(168,85,247,0.3);">Pro <i class="fas fa-lock"></i></span>
                    </button>
                @endif

                <div class="pt-4 mt-4" style="border-top: 1px solid rgba(210, 166, 61, 0.12);">
                    <a href="#"
                       class="sidebar-link flex items-center gap-4 p-4 rounded-xl font-medium text-sm transition-all"
                       style="color: rgba(234,238,242,0.5);"
                       onmouseover="this.style.background='rgba(210,166,61,0.06)'"
                       onmouseout="this.style.background='transparent'">
                        <i class="fas fa-cog w-5" style="color: rgba(210,166,61,0.5);"></i> Settings
                    </a>
                </div>
            </nav>

            <div class="p-6" style="border-top: 1px solid rgba(210, 166, 61, 0.12);">
                <div class="flex items-center gap-3 mb-6 p-3 glass-effect rounded-xl relative overflow-hidden group">
                    <div class="absolute inset-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300" style="background: rgba(210,166,61,0.06);"></div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-sm font-bold shadow-lg z-10" style="background: linear-gradient(135deg, #d2a63d, #a07828); color: #010717;">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                    </div>
                    <div class="flex-1 overflow-hidden z-10">
                        <p class="text-sm font-bold truncate" style="color: #eaeef2;">{{ auth()->user()->name ?? 'Employee' }}</p>
                        <p class="text-[10px] uppercase tracking-widest flex items-center gap-1" style="color: #1dd182;">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color: #1dd182;"></span> Active
                        </p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 p-3 glass-effect rounded-xl font-bold text-[11px] uppercase tracking-[0.2em] transition-all group"
                            style="color: #ef4444; border: 1px solid rgba(239,68,68,0.2);"
                            onmouseover="this.style.backgroundColor='#ef4444'; this.style.color='#fff'; this.style.borderColor='#ef4444';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ef4444'; this.style.borderColor='rgba(239,68,68,0.2)';">
                        <i class="fas fa-power-off group-hover:animate-pulse"></i> Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 lg:ml-72 transition-all">
            {{-- MOBILE HEADER --}}
            <header class="lg:hidden p-4 flex justify-between items-center sticky top-0 z-40 glass-effect">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #d2a63d, #a07828);">
                        <i class="fas fa-rocket" style="color: #010717;"></i>
                    </div>
                    <h1 class="font-bold uppercase tracking-tight" style="color: #eaeef2;">Employee <span style="color: #d2a63d;">Member</span></h1>
                </div>
                <div class="flex items-center gap-4">
                    {{-- ⭐ Mobile Notification Bell --}}
                    <button id="mobile-alert-btn" class="relative" style="color: #8a9bb0;">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="mobile-chat-badge" class="hidden absolute -top-1 -right-1 w-3 h-3 rounded-full border-2" style="background-color: #ef4444; border-color: #010717;"></span>
                    </button>
                    <button onclick="toggleSidebar()" class="text-xl p-2 rounded-xl glass-effect" style="color: #eaeef2;">
                        <i class="fas fa-bars"></i>
                    </button>
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

        // ⭐ Feature Upgrade Prompt for Employees
        function upgradePrompt(planName, featureName) {
            Swal.fire({
                icon: 'lock',
                title: 'Access Restricted',
                html: `<p style="color:#eaeef2; margin-bottom:10px;">The <b>${featureName}</b> module is currently locked.</p><p style="color:#eaeef2; opacity:0.7; font-size:12px;">This feature requires the <span style="color:#d2a63d; font-weight:bold;">${planName} Plan</span>. Please contact your company's Administrator to request access.</p>`,
                background: '#0a1225',
                color: '#eaeef2',
                confirmButtonColor: '#d2a63d',
                confirmButtonText: '<i class="fas fa-user-shield mr-2"></i> Understood',
                customClass: {
                    popup: 'border border-[#d2a63d]/30 rounded-2xl shadow-[0_0_30px_rgba(210,166,61,0.2)]',
                    title: 'text-[#d2a63d]'
                }
            });
        }

        // ⭐ Toast Notification Generator
        function spawnToast(title, message, icon = 'fa-comment-dots', color = 'gold') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            // Notification Beep Sound
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA');
                audio.volume = 0.5; audio.play().catch(()=>{});
            } catch(e){}

            // Dynamic Color Theming
            const colors = {
                gold:    'border-[rgba(210,166,61,0.5)] text-[#d2a63d]',
                rose:    'border-[rgba(239,68,68,0.5)]  text-[#ef4444]',
                emerald: 'border-[rgba(29,209,130,0.5)] text-[#1dd182]',
                purple:  'border-[rgba(210,166,61,0.5)] text-[#d2a63d]',
                cyan:    'border-[rgba(210,166,61,0.5)] text-[#d2a63d]',
            };

            const toast = document.createElement('div');
            toast.className = `border-l-4 rounded-xl p-4 w-80 shadow-[0_10px_30px_rgba(0,0,0,0.5)] pointer-events-auto flex gap-4 items-start toast-enter ${colors[color] ?? colors.gold}`;
            toast.style.cssText = 'background: rgba(5,16,31,0.96); backdrop-filter: blur(10px); border-top: 1px solid rgba(210,166,61,0.1); border-right: 1px solid rgba(210,166,61,0.1); border-bottom: 1px solid rgba(210,166,61,0.1);';

            toast.innerHTML = `
                <div class="mt-0.5 rounded-full p-2" style="background: rgba(210,166,61,0.1);">
                    <i class="fas ${icon} text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-xs font-black uppercase tracking-widest mb-1" style="color: #eaeef2;">${title}</h4>
                    <p class="text-[11px] font-medium leading-relaxed" style="color: #8a9bb0;">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" style="color: #4a5568;" onmouseover="this.style.color='#eaeef2'" onmouseout="this.style.color='#4a5568'">
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

        // ⭐ WebSockets Listeners (For Instant Updates & Tasks)
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Echo !== 'undefined' && window.UserId) {

                @if(auth()->user()->company->hasFeature('team_chat'))
                    // 1. Instant Chat Message Listener (ONLY IF FEATURE UNLOCKED)
                    Echo.private(`chat.user.${window.UserId}`)
                        .listen('MessageSent', (data) => {
                            if (window.currentChatId !== data.message.sender_id) {
                                checkSystemAlerts();
                            }
                        });
                @endif

                // 2. Instant Task Assignment Listener
                Echo.private(`user.alerts.${window.UserId}`)
                    .listen('TaskAssigned', (data) => {
                        spawnToast(
                            'New Mission Assigned',
                            `Task: ${data.task_title}`,
                            'fa-tasks',
                            'gold'
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

        // ⭐ Global Unread Message Poller (Checks every 5 sec - ONLY IF UNLOCKED)
        @if(auth()->user()->company->hasFeature('team_chat'))
            let lastMessageCount = 0;

            function checkSystemAlerts() {
                if (!window.UserId) return;

                fetch("{{ url('/chat/unread-count') }}")
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('employee-chat-badge');
                    const ping  = document.getElementById('employee-chat-ping');
                    const mobileBadge = document.getElementById('mobile-chat-badge');

                    if (data.count > 0) {
                        if(badge) { badge.innerText = data.count; badge.classList.remove('hidden'); }
                        if(ping)  ping.classList.remove('hidden');
                        if(mobileBadge) mobileBadge.classList.remove('hidden');

                        if (data.count > lastMessageCount) {
                            spawnToast(
                                'Admin Transmission',
                                `Secure message received from <b style="color:#eaeef2">${data.sender_name}</b>.`,
                                'fa-envelope-open-text',
                                'gold'
                            );
                        }
                        lastMessageCount = data.count;
                    } else {
                        if(badge) badge.classList.add('hidden');
                        if(ping)  ping.classList.add('hidden');
                        if(mobileBadge) mobileBadge.classList.add('hidden');
                        lastMessageCount = 0;
                    }
                }).catch(e => {});
            }

            checkSystemAlerts();
            setInterval(checkSystemAlerts, 5000);
        @endif
    </script>
</body>
</html>