<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Master Control Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/Favicon.png') }}">
    
    {{-- Tailwind CSS Script Configuration --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: 'rgb(var(--theme-color) / <alpha-value>)',
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        /* CSS Variables - Now strictly GOLD for everyone */
        :root {
            --theme-color: 210 166 61; /* #d2a63d */
        }

        body { font-family: 'Inter', sans-serif; background-color: #010717; color: #eaeef2; }
        
        /* Dynamic Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgb(var(--theme-color) / 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgb(var(--theme-color) / 0.5); }
    </style>
</head>

<body class="flex h-screen overflow-hidden antialiased selection:bg-theme selection:text-[#010717]">

    {{-- 💡 SMART ROUTING PREFIX --}}
    @php
        $routePrefix = auth()->check() && auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';
    @endphp

    {{-- ==============================
         LEFT SIDEBAR (NAVIGATION)
    =============================== --}}
    <aside class="w-64 bg-[#0a1225] border-r border-theme/10 flex flex-col h-full shadow-[5px_0_30px_rgba(0,0,0,0.5)] z-20">
        
        {{-- Branding Area --}}
        <div class="h-20 flex items-center px-6 border-b border-theme/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full border-2 border-theme flex items-center justify-center text-theme font-black">
                    <i class="fa-solid fa-angle-right text-xs"></i>
                </div>
                <span class="text-xl font-black tracking-widest text-[#eaeef2]">Master<span class="text-theme">Panel</span></span>
            </div>
        </div>

        {{-- Scrollable Menu Area --}}
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1 custom-scrollbar">
            
            {{-- 1. MAIN MENU (Shared for both) --}}
            <div class="text-[9px] font-black text-[#eaeef2]/40 uppercase tracking-widest mb-2 mt-2 ml-2">Main Menu</div>
            
            <a href="{{ route($routePrefix . 'dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs($routePrefix . 'dashboard') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> 
                <span class="text-sm font-bold">Command Center</span>
            </a>

            <a href="{{ route($routePrefix . 'tenants.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs($routePrefix . 'tenants.*') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                <i class="fa-solid fa-building w-5 text-center"></i> 
                <span class="text-sm font-bold">Manage Tenants</span>
            </a>

            {{-- 2. RESTRICTED ZONE (Only for Super Admin) --}}
            @if(auth()->check() && auth()->user()->role === 'super_admin')
                
                {{-- Administration --}}
                <div class="text-[9px] font-black text-[#eaeef2]/40 uppercase tracking-widest mb-2 mt-6 ml-2">Administration</div>
                <a href="{{ route('superadmin.partners.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs('superadmin.partners.*') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center"></i> 
                    <span class="text-sm font-bold">Partner Management</span>
                </a>

            @endif

            {{-- 3. FINANCIAL ENGINE (Shared for Super Admin & Partner) --}}
            <div class="text-[9px] font-black text-[#eaeef2]/40 uppercase tracking-widest mb-2 mt-6 ml-2">Financial Engine</div>
            
            <a href="{{ route($routePrefix . 'plans.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs($routePrefix . 'plans.*') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                <i class="fa-solid fa-tags w-5 text-center"></i> 
                <span class="text-sm font-bold">SaaS Plans Matrix</span>
            </a>
            
            <a href="{{ route($routePrefix . 'invoices.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs($routePrefix . 'invoices.*') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> 
                <span class="text-sm font-bold">Billing & Invoices</span>
            </a>

           {{-- 4. SECURITY ZONE (Master Control Only - HIDDEN FROM PARTNERS) --}}
            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'superadmin')
                <div class="text-[9px] font-black text-[#eaeef2]/40 uppercase tracking-widest mb-2 mt-6 ml-2">Security</div>
                
                <a href="{{ route('superadmin.audits.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs('superadmin.audits.*') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                    <i class="fa-solid fa-user-secret w-5 text-center"></i> 
                    <span class="text-sm font-bold">Shadow Audits</span>
                </a>
            @endif
            
            {{-- Partner Activity Monitor (Hidden from Super Admin) --}}
            @if(auth()->user()->role === 'partner')
                <div class="text-[9px] font-black text-[#eaeef2]/40 uppercase tracking-widest mb-2 mt-6 ml-2">Workspace Logs</div>
                
                <a href="{{ route('partner.audits.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-theme/10 text-[#eaeef2] hover:text-theme transition-colors {{ request()->routeIs('partner.audits.*') ? 'bg-theme/10 text-theme border border-theme/20' : '' }}">
                    <i class="fa-solid fa-desktop w-5 text-center"></i> 
                    <span class="text-sm font-bold">Activity Monitor</span>
                </a>
            @endif

        </nav>

        {{-- Bottom User Profile Card --}}
        <div class="p-4 border-t border-theme/10 bg-[#060b17]">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-theme/10 border border-theme/30 flex items-center justify-center text-theme font-black uppercase shadow-inner">
                    {{ substr(auth()->user()->name ?? 'S', 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-[#eaeef2] leading-tight">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-[9px] text-theme uppercase tracking-widest font-black mt-0.5">
                        {{ auth()->user()->role === 'partner' ? 'Authorized Partner' : 'System Owner' }}
                    </p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all text-[10px] font-black uppercase tracking-widest border border-red-500/20">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ==============================
         RIGHT MAIN CONTENT AREA
    =============================== --}}
    <main class="flex-1 flex flex-col h-full bg-[#010717] overflow-y-auto relative z-10">
        
        {{-- Top Subtle Glow Effect --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-3xl h-32 bg-theme/5 blur-[100px] pointer-events-none transition-colors"></div>

        <div class="p-8 lg:p-10 relative z-10">
            @yield('content')
        </div>
    </main>

    {{-- ==============================
         FLOATING ACTION BUTTONS (GOD MODE EXITS)
    =============================== --}}

    {{-- 1. Partner God Mode Exit --}}
    @if(session()->has('partner_id'))
        <div class="fixed bottom-6 right-6 z-[9999]">
            <a href="{{ route('partner.exit') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-full shadow-[0_10px_30px_rgba(220,38,38,0.5)] font-black text-xs uppercase tracking-widest flex items-center gap-2 border-2 border-red-400 transition-all hover:scale-105">
                <i class="fa-solid fa-bolt"></i> Exit God Mode
            </a>
        </div>
    @endif

    {{-- 2. Super Admin God Mode Exit --}}
    @if(session()->has('superadmin_id'))
        <div class="fixed bottom-6 right-6 z-[9999]">
            <a href="{{ route('superadmin.exit_partner') }}" class="bg-[#d2a63d] hover:bg-[#b58c2e] text-[#010717] px-6 py-3 rounded-full shadow-[0_10px_30px_rgba(210,166,61,0.5)] font-black text-xs uppercase tracking-widest flex items-center gap-2 border-2 border-[#d2a63d]/50 transition-all hover:scale-105">
                <i class="fa-solid fa-crown"></i> Return to Master
            </a>
        </div>
    @endif

</body>
</html>