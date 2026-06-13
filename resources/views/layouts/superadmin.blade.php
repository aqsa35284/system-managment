<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Command | Super Admin')</title>
    
    <link rel="icon" href="{{ asset('favicon.jpg') }}" type="image/jpeg">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #010717; }
        ::-webkit-scrollbar-thumb { background: #d2a63d; border-radius: 10px; opacity: 0.8; }
        ::-webkit-scrollbar-thumb:hover { background: #b88e2c; }
    </style>
</head>
<body class="bg-[#010717] text-[#eaeef2] h-screen flex overflow-hidden antialiased">

    <aside class="w-72 bg-white/[0.01] backdrop-blur-2xl border-r border-[#d2a63d]/20 flex flex-col justify-between h-full relative z-20">
        <div>
            <div class="h-24 flex items-center px-6 border-b border-[#d2a63d]/10">
                <div class="w-10 h-10 rounded-full border-2 border-[#d2a63d]/50 bg-black flex items-center justify-center mr-3 shadow-[0_0_10px_rgba(210,166,61,0.3)]">
                    <i class="fa-solid fa-terminal text-[#d2a63d]"></i>
                </div>
                <h1 class="text-xl font-bold tracking-wider text-[#eaeef2]">
                    Master<span class="text-[#d2a63d]">Panel</span>
                </h1>
            </div>

            <nav class="p-4 space-y-2 mt-4">
                <p class="px-4 text-[10px] font-bold text-[#d2a63d]/60 uppercase tracking-widest mb-3">Main Menu</p>
                
                {{-- Dashboard --}}
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30' : 'text-[#eaeef2]/60 hover:text-[#d2a63d] hover:bg-[#d2a63d]/5' }} transition-all duration-300">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span class="font-medium">Command Center</span>
                </a>

                {{-- Tenant Management --}}
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('company.create', 'company.show', 'company.edit') ? 'bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30' : 'text-[#eaeef2]/60 hover:text-[#d2a63d] hover:bg-[#d2a63d]/5' }} transition-all duration-300">
                    <i class="fa-solid fa-building w-5"></i>
                    <span class="font-medium">Manage Tenants</span>
                </a>

                {{-- Shadow Audits --}}
                <a href="{{ route('superadmin.audits') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('superadmin.audits') ? 'bg-red-500/10 text-red-500 border border-red-500/30' : 'text-[#eaeef2]/60 hover:text-red-500 hover:bg-red-500/5' }} transition-all duration-300">
                    <i class="fa-solid fa-shield-halved w-5"></i>
                    <span class="font-medium">Shadow Audits</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-[#d2a63d]/10">
            <div class="flex items-center gap-3 px-4 py-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-[#010717] border border-[#d2a63d]/40 flex items-center justify-center">
                    <i class="fa-solid fa-crown text-[#d2a63d]"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-[#eaeef2]">System Owner</p>
                    <p class="text-xs text-[#d2a63d]/70">Super Admin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/30 transition-all duration-300">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- UI FIX: Removed the pt-20 padding that was leaving empty space at the top --}}
    <main class="flex-1 h-full overflow-y-auto relative">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-[#d2a63d]/5 blur-[120px] rounded-full pointer-events-none z-0"></div>
        <div class="relative z-10 p-10">
            @yield('content')
        </div>
    </main>

</body>
</html>