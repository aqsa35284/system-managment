<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Content-Security-Policy" content="frame-ancestors 'self'">
    <title>EAMS | Enterprise Workforce Analytics</title>
    
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/Favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;700;800;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Space Mono"', 'monospace'],
                    },
                    colors: {
                        navy: '#010717',
                        'navy-light': '#05101f',
                        gold: '#d2a63d',
                        'gold-light': '#e6b84a',
                        card: '#0a1225',
                    }
                }
            }
        }
    </script>
    
    <style>
        body { background-color: #010717; color: #eaeef2; overflow-x: hidden; }
        
        /* Custom Inputs */
        input[type=range] { -webkit-appearance: none; width: 100%; background: transparent; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; height: 24px; width: 24px; border-radius: 50%; background: #d2a63d; cursor: pointer; margin-top: -10px; box-shadow: 0 0 15px rgba(210,166,61,0.6); transition: transform 0.2s; }
        input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.15); }
        input[type=range]::-webkit-slider-runnable-track { width: 100%; height: 4px; cursor: pointer; background: rgba(234,238,242,0.1); border-radius: 2px; }
        
        /* Glassmorphism */
        .glass-border { border: 1px solid rgba(210,166,61,0.2); background: rgba(5,16,31,0.8); backdrop-filter: blur(12px); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .glass-border:hover { border-color: rgba(210,166,61,0.5); box-shadow: 0 0 40px rgba(210,166,61,0.1); transform: translateY(-5px); }
        
        /* Scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(1,7,23,0.5); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(210, 166, 61, 0.3); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(210, 166, 61, 0.6); }
        
        /* Animations */
        .bg-animation { position: absolute; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; overflow: hidden; pointer-events: none;}
        .bg-animation span {
            position: absolute; display: block; width: 1.5px; height: 25vh;
            background: linear-gradient(to bottom, transparent, rgba(210,166,61,0.6), transparent);
            animation: rain 4s linear infinite;
        }
        @keyframes rain { 0% { transform: translateY(-100vh); opacity: 0; } 50% { opacity: 1; } 100% { transform: translateY(100vh); opacity: 0; } }

        /* ⭐ STRICT PORTRAIT OVERLAY FOR MOBILE */
        #rotate-device-overlay { display: none; }
        @media screen and (max-width: 1024px) and (orientation: portrait) {
            #rotate-device-overlay { display: flex !important; }
            #main-saas-ecosystem { display: none !important; }
            html, body { overflow: hidden !important; width: 100vw; height: 100vh; position: fixed; }
        }

        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .mockup-toast { animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }

        /* Brand logo sizing — square PNG canvas, horizontal logo content */
        .brand-logo-header-wrap {
            height: 2.5rem;
            width: 8.5rem;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 768px) {
            .brand-logo-header-wrap { height: 2.75rem; width: 10.5rem; }
        }
        .brand-logo-header-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .brand-logo-footer {
            width: 10rem;
            max-width: 75vw;
            height: auto;
            object-fit: contain;
        }
        @media (min-width: 768px) {
            .brand-logo-footer { width: 12rem; }
        }

        /* Matrix comparison table — fit without horizontal scroll */
        #matrix .matrix-table-wrap { overflow: hidden; }
        #matrix .matrix-comparison-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        #matrix .matrix-comparison-table th,
        #matrix .matrix-comparison-table td {
            white-space: normal;
            word-break: break-word;
            line-height: 1.35;
            vertical-align: middle;
            padding: 0.55rem 0.35rem;
            font-size: 0.62rem;
        }
        @media (min-width: 768px) {
            #matrix .matrix-comparison-table th,
            #matrix .matrix-comparison-table td {
                padding: 0.7rem 0.5rem;
                font-size: 0.68rem;
            }
        }
        @media (min-width: 1024px) {
            #matrix .matrix-comparison-table th,
            #matrix .matrix-comparison-table td {
                padding: 0.85rem 0.65rem;
                font-size: 0.72rem;
            }
        }
        @media (min-width: 1280px) {
            #matrix .matrix-comparison-table th,
            #matrix .matrix-comparison-table td {
                font-size: 0.75rem;
            }
        }
        #matrix .matrix-comparison-table th:first-child,
        #matrix .matrix-comparison-table td:first-child {
            width: 22%;
        }
        #matrix .matrix-tab-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.15rem;
            min-width: 7.5rem;
            line-height: 1.3;
        }
        #matrix .matrix-tab-btn span.matrix-tab-sub {
            font-size: 0.58rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: none;
            opacity: 0.85;
        }
        #matrix .matrix-tab-btn.active span.matrix-tab-sub { opacity: 0.75; }
        #matrix .matrix-tier-hint {
            min-height: 2.5rem;
        }
        #matrix .matrix-comparison-table .matrix-tier-row th {
            font-size: 0.58rem;
            padding-top: 0.65rem;
            padding-bottom: 0.35rem;
            letter-spacing: 0.12em;
        }
        #matrix .matrix-comparison-table .matrix-plan-row th {
            font-size: 0.65rem;
            padding-top: 0.35rem;
            text-transform: none;
            letter-spacing: 0.05em;
        }
        #matrix .matrix-comparison-table .feature-label {
            font-weight: 700;
        }
        #matrix .matrix-comparison-table .feature-hint {
            display: block;
            font-size: 0.55rem;
            font-weight: 500;
            color: #94a3b8;
            margin-top: 0.2rem;
            line-height: 1.3;
            text-transform: none;
            letter-spacing: 0;
        }
        @media (min-width: 768px) {
            #matrix .matrix-comparison-table .feature-hint { font-size: 0.58rem; }
        }
    </style>
</head>
<body class="antialiased selection:bg-gold selection:text-navy">

    {{-- ⭐ PORTRAIT ROTATE PROTECTION OVERLAY --}}
    <div id="rotate-device-overlay" class="fixed inset-0 z-[100000] flex-col items-center justify-center bg-[#090e17] px-6 text-center select-none w-full h-full">
        <div class="mb-10 relative">
            <div class="absolute inset-0 bg-gold blur-3xl opacity-20 rounded-full animate-pulse"></div>
            <div class="w-16 h-28 border-4 border-gold rounded-2xl mx-auto flex justify-center items-end pb-3 shadow-[0_0_40px_rgba(210,166,61,0.2)] bg-navy-light relative z-10">
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-4 h-1 bg-gold/50 rounded-full"></div>
                <div class="w-3 h-3 bg-gold rounded-full animate-ping"></div>
            </div>
        </div>
        <h2 class="text-3xl md:text-4xl font-black text-white mb-4 tracking-tighter uppercase drop-shadow-md">Rotate Device</h2>
        <p class="text-slate-400 text-sm md:text-base max-w-[320px] mx-auto leading-relaxed font-medium">
            For the optimal experience featuring our Live Dashboard Simulator, please rotate your device to landscape mode.
        </p>
        <div class="mt-12 flex items-center gap-3 text-[10px] font-mono uppercase tracking-[0.2em] text-gold border border-gold/20 bg-gold/10 px-5 py-3 rounded-full backdrop-blur-md shadow-lg">
            <i class="fas fa-sync spin animate-spin"></i> Awaiting Landscape Sync...
        </div>
    </div>

    {{-- MAIN ECOSYSTEM CONTAINER --}}
    <div id="main-saas-ecosystem">

        {{-- TOP ALERT BAR --}}
        <div class="bg-navy-light text-center py-2.5 border-b border-gold/20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-gold/10 to-transparent translate-x-[-100%] animate-[shimmer_3s_infinite]"></div>
            <span class="text-[10px] font-mono font-bold text-gold uppercase tracking-widest relative z-10">
                <i class="fa-solid fa-satellite-dish text-gold mr-2 animate-pulse"></i> Client-Ready SaaS Deployment Architecture
            </span>
        </div>

        {{-- NAVBAR --}}
        <nav class="sticky top-0 w-full z-50 bg-navy/90 backdrop-blur-xl border-b border-gold/10 transition-all duration-300" id="navbar">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="brand-logo-header-wrap hover:scale-105 transition-transform">
                        <img src="{{ asset('storage/logo/EAMS Logo.png') }}" alt="EAMS Logo" onerror="this.closest('.brand-logo-header-wrap').style.display='none';">
                    </div>
                    <div class="hidden md:block border-l border-gold/30 pl-4 shrink-0">
                        <h1 class="text-sm font-black text-white tracking-widest uppercase leading-none drop-shadow-lg">EAMS</h1>
                        <p class="text-[8px] text-gold uppercase tracking-[0.3em] mt-1 font-bold">By VWEB.DEV</p>
                    </div>
                </div>
                
                <div class="hidden lg:flex items-center gap-8 text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <a href="#simulator" class="hover:text-gold transition-colors">Live Simulator</a>
                    <a href="#matrix" class="hover:text-gold transition-colors">Capabilities</a>
                    <a href="#pricing" class="hover:text-gold transition-colors">Pricing Engine</a>
                    <a href="#architecture" class="hover:text-gold transition-colors">Logistics</a>
                </div>

                <a href="{{ route('login') }}" class="group bg-gold hover:bg-gold-light text-navy px-6 py-2.5 rounded-lg font-black text-[11px] uppercase tracking-widest transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(210,166,61,0.4)] hover:shadow-[0_0_25px_rgba(210,166,61,0.6)]">
                    Login <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </nav>

        {{-- ⭐ SPLIT HERO SECTION (Updated to Match Requirement) --}}
        <main class="relative pt-24 pb-20 px-6 max-w-7xl mx-auto overflow-hidden flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-8">
            <div class="bg-animation z-0">
                <span style="left: 10%; animation-delay: 0s;"></span><span style="left: 30%; animation-delay: 2s;"></span>
                <span style="left: 50%; animation-delay: 1s;"></span><span style="left: 70%; animation-delay: 3s;"></span>
                <span style="left: 90%; animation-delay: 0.5s;"></span>
            </div>
            
            {{-- Left Side: Text and Buttons (70% on lg screens) --}}
            <div class="flex-1 lg:w-[65%] text-center lg:text-left relative z-10">
                <div class="gsap-hero inline-block border border-gold/30 bg-gold/5 px-5 py-2 rounded-full mb-8 backdrop-blur-md shadow-lg">
                    <p class="text-[10px] font-black text-gold uppercase tracking-[0.3em]"><i class="fas fa-eye mr-2"></i> Absolute Oversight</p>
                </div>

                <h1 class="gsap-hero text-4xl md:text-5xl lg:text-[4rem] xl:text-[4.5rem] font-black tracking-tighter mb-8 leading-[1.1] text-white uppercase drop-shadow-xl">
                    Workforce Visibility <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold to-gold-light drop-shadow-[0_0_30px_rgba(210,166,61,0.3)]">Rendered in Pure Time</span>
                </h1>

                <p class="gsap-hero text-slate-400 text-sm md:text-base lg:text-lg max-w-2xl mx-auto lg:mx-0 mb-12 leading-relaxed font-medium">
                    EAMS is a fully managed enterprise SaaS designed to intercept distractions, track exact productive seconds, log screen activity, and manage tasks from a singular, highly encrypted dashboard.
                </p>

                <div class="gsap-hero flex flex-wrap gap-4 w-full justify-center lg:justify-start">
                    <a href="#simulator" class="bg-gold text-navy px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all shadow-[0_0_30px_rgba(210,166,61,0.4)] border border-gold-light flex items-center gap-2">
                        <i class="fas fa-play"></i> Live Simulator
                    </a>
                    <a href="#pricing" class="bg-navy-light border border-gold/30 hover:border-gold hover:bg-gold/10 text-white px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                        <i class="fas fa-calculator"></i> Build Pricing
                    </a>
                </div>
            </div>

            {{-- Right Side: Large Logo (30% on lg screens) --}}
            <div class="w-full lg:w-[35%] flex justify-center lg:justify-end relative z-10 gsap-hero">
                <div class="relative group">
                    {{-- Glow Effect Behind Logo --}}
                    <div class="absolute inset-0 bg-gold blur-3xl opacity-20 rounded-full group-hover:opacity-40 transition-opacity duration-700"></div>
                    {{-- The Logo --}}
                    <img src="{{ asset('storage/logo/hero.png') }}" alt="EAMS Primary Logo" class="w-[280px] sm:w-[350px] lg:w-[450px] object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-500 ease-out relative z-10" onerror="this.style.display='none';">
                </div>
            </div>
        </main>

        {{-- ══════════════════════════════════════════════════════════
             ⭐ LIVE PLATFORM SIMULATOR (MULTIPAGE INTERACTIVE)
        ══════════════════════════════════════════════════════════ --}}
        <section id="simulator" class="py-24 px-6 border-t border-gold/10 bg-navy relative">
            <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(#d2a63d 1px, transparent 1px); background-size: 40px 40px;"></div>
            
            <div class="text-center mb-16 relative z-10 gsap-fade-up">
                <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-2">Real-Time Interaction</p>
                <h2 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight">The Platform Demo</h2>
                <p class="text-xs text-slate-400 mt-4 max-w-2xl mx-auto">This is an exact interactive replica of the live software. Navigate through the sidebar menu to explore the modules.</p>
            </div>

            <div class="max-w-[1400px] mx-auto relative z-10 gsap-fade-up">
                
                {{-- TOGGLE ROLES --}}
                <div class="flex justify-center mb-10">
                    <div class="p-2 bg-[#05101f] rounded-full inline-flex mx-auto border border-gold/20 shadow-xl gap-2">
                        <button onclick="toggleSimulatorRole('admin')" id="btn-admin" class="flex items-center gap-2 bg-gold text-navy px-8 py-3.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(210,166,61,0.4)]">
                            <i class="fas fa-crown"></i> Admin View
                        </button>
                        <button onclick="toggleSimulatorRole('employee')" id="btn-employee" class="flex items-center gap-2 bg-transparent text-slate-400 hover:text-white hover:bg-gold/10 px-8 py-3.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-all">
                            <i class="fas fa-laptop-code"></i> Employee View
                        </button>
                    </div>
                </div>

                {{-- 👑 ADMIN SIMULATOR WRAPPER --}}
                <div id="sim-admin-panel" class="w-full bg-[#010717] border border-gold/30 rounded-[2rem] shadow-[0_0_50px_rgba(210,166,61,0.15)] overflow-hidden transition-all duration-500 flex flex-col md:flex-row relative h-[750px]">
                    <div id="mockup-toast-area" class="absolute top-6 right-6 z-50 flex flex-col gap-3"></div>

                    {{-- Admin Sidebar (Clickable Navigation) --}}
                    <div class="w-[240px] bg-navy border-r border-slate-800 p-6 flex flex-col hidden lg:flex relative z-20">
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-light rounded-xl flex items-center justify-center text-navy font-black shadow-lg">
                                <i class="fa-solid fa-shield-halved text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-white font-black text-xs tracking-widest uppercase">ADMIN PRO</h1>
                                <p class="text-[8px] text-gold/50 font-mono tracking-widest uppercase mt-0.5">Advanced Plan</p>
                            </div>
                        </div>
                        <div class="space-y-2 flex-1 select-none">
                            <p class="text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-2">Core Features</p>
                            <div onclick="switchAdminView(this, 'admin-view-dashboard', 'Admin Center', 'Global Employee Overview')" class="admin-nav-item border-l-2 border-gold bg-gold/10 text-gold rounded-r-lg p-3 text-[11px] font-bold tracking-wide flex items-center gap-3 cursor-pointer shadow-inner transition-colors"><i class="fa-solid fa-chart-line w-4 text-center"></i> Admin dashboard</div>
                            <div onclick="switchAdminView(this, 'admin-view-departments', 'Organization Nodes', 'Manage your company departments')" class="admin-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium flex items-center gap-3 cursor-pointer hover:text-white transition-colors"><i class="fa-solid fa-sitemap w-4 text-center"></i> Departments</div>
                            <div onclick="switchAdminView(this, 'admin-view-employees', 'Team Members', 'Manage organization personnel')" class="admin-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium flex items-center gap-3 cursor-pointer hover:text-white transition-colors"><i class="fa-solid fa-users w-4 text-center"></i> Manage Employees</div>
                            <div onclick="switchAdminView(this, 'admin-view-projects', 'Project Fleet', 'Active operational missions')" class="admin-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium flex items-center gap-3 cursor-pointer hover:text-white transition-colors"><i class="fa-solid fa-rocket w-4 text-center"></i> Projects</div>
                            <div onclick="switchAdminView(this, 'admin-view-tasks', 'Operational Tasks', 'Live objective matrix')" class="admin-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium flex items-center gap-3 cursor-pointer hover:text-white transition-colors"><i class="fa-solid fa-tasks w-4 text-center"></i> Task Board</div>
                            <div onclick="switchAdminView(this, 'admin-view-attendance', 'Shift Intelligence', 'Automated Performance Reports')" class="admin-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium flex items-center gap-3 cursor-pointer hover:text-white transition-colors"><i class="fa-solid fa-calendar-check w-4 text-center"></i> Attendance Log</div>
                            <div onclick="switchAdminView(this, 'admin-view-whatsapp', 'Communication Hub', 'Encrypted Work Chat')" class="admin-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium flex items-center gap-3 cursor-pointer hover:text-white transition-colors"><i class="fa-brands fa-whatsapp w-4 text-center"></i> Team Chat</div>
                        </div>
                    </div>

                    {{-- Admin Main Content Wrapper --}}
                    <div class="flex-1 bg-[#0a1225] flex flex-col relative z-10 overflow-hidden" id="admin-main-container">
                        
                        {{-- Sticky Top Header --}}
                        <div class="p-6 lg:p-8 pb-4 border-b border-slate-800 bg-[#0a1225] sticky top-0 z-20">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p id="admin-sim-subtitle" class="text-[9px] text-slate-500 font-black uppercase tracking-[0.2em] mb-1">Global Employee Overview</p>
                                    <h3 id="admin-sim-title" class="text-2xl font-black text-white uppercase tracking-wider">Admin Center</h3>
                                </div>
                                <button class="border border-gold text-[9px] font-black uppercase tracking-widest text-gold px-4 py-2 rounded hover:bg-gold/10 transition-colors shadow-[0_0_10px_rgba(210,166,61,0.2)]" onclick="showMockupToast('Action Simulated', 'Data fetched from backend.', 'text-emerald-400', 'border-emerald-500/50', 'fa-solid fa-check')">Refresh Data</button>
                            </div>
                        </div>

                        {{-- Scrollable View Area --}}
                        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8" id="admin-views-wrapper">
                            
                            {{-- VIEW 1: DASHBOARD (Default) --}}
                            <div id="admin-view-dashboard" class="admin-sim-view block space-y-6">
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-navy border border-slate-700/60 rounded-xl p-5 shadow-inner">
                                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest mb-2">Total Workforce</p>
                                        <p class="text-3xl font-black text-white">24</p>
                                    </div>
                                    <div class="bg-navy border border-slate-700/60 rounded-xl p-5 shadow-inner">
                                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest mb-2">Active Nodes</p>
                                        <p class="text-3xl font-black text-green-400">18</p>
                                    </div>
                                    <div class="bg-navy border border-slate-700/60 rounded-xl p-5 shadow-inner transition-colors duration-300" id="box-dist">
                                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest mb-2">Distraction Alerts</p>
                                        <p class="text-3xl font-black text-white" id="sim-dist-count">0</p>
                                    </div>
                                    <div class="bg-navy border border-slate-700/60 rounded-xl p-5 shadow-inner transition-colors duration-300" id="box-ai">
                                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest mb-2">AI Tool Utilization</p>
                                        <p class="text-3xl font-black text-white" id="sim-ai-count">0</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                                    {{-- Interactive Injector --}}
                                    <div class="bg-gradient-to-br from-[#05101f] to-navy border border-gold/20 rounded-xl p-6 shadow-lg flex flex-col justify-center relative overflow-hidden">
                                        <div class="absolute right-0 top-0 w-32 h-32 bg-gold/5 blur-3xl rounded-full pointer-events-none"></div>
                                        <div class="flex justify-between items-start mb-4 relative z-10">
                                            <div>
                                                <h4 class="text-[11px] text-gold uppercase font-black tracking-widest mb-1"><i class="fas fa-bolt mr-1"></i> Event Injector</h4>
                                                <p class="text-[9px] text-slate-400 leading-relaxed">Simulate background client window changes.</p>
                                            </div>
                                        </div>
                                        <div class="space-y-3 relative z-10">
                                            <button onclick="simulateAdminEvent('vscode')" class="w-full flex justify-between items-center bg-[#010717] border border-slate-700 hover:border-blue-500/50 px-4 py-3 rounded-xl transition-all hover:scale-[1.01] shadow-inner">
                                                <span class="text-xs text-white flex items-center gap-3 font-bold"><i class="fa-solid fa-code text-blue-400 text-lg"></i> App: VS Code</span>
                                                <span class="text-[8px] bg-green-500/10 text-green-400 px-2 py-1 rounded border border-green-500/30 uppercase font-black">Approved</span>
                                            </button>
                                            <button onclick="simulateAdminEvent('facebook')" class="w-full flex justify-between items-center bg-[#010717] border border-slate-700 hover:border-red-500/50 px-4 py-3 rounded-xl transition-all hover:scale-[1.01] shadow-inner">
                                                <span class="text-xs text-white flex items-center gap-3 font-bold"><i class="fa-brands fa-facebook text-blue-500 text-lg"></i> App: Facebook</span>
                                                <span class="text-[8px] bg-red-500/10 text-red-400 px-2 py-1 rounded border border-red-500/30 uppercase font-black">Distraction</span>
                                            </button>
                                            <button onclick="simulateAdminEvent('ai')" class="w-full flex justify-between items-center bg-[#010717] border border-slate-700 hover:border-purple-500/50 px-4 py-3 rounded-xl transition-all hover:scale-[1.01] shadow-inner">
                                                <span class="text-xs text-white flex items-center gap-3 font-bold"><i class="fa-solid fa-robot text-purple-400 text-lg"></i> App: ChatGPT</span>
                                                <span class="text-[8px] bg-purple-500/10 text-purple-400 px-2 py-1 rounded border border-purple-500/30 uppercase font-black">AI Tracked</span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    {{-- Target Matrix --}}
                                    <div class="flex flex-col">
                                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest mb-2 pl-1">Live Telemetry Matrix</p>
                                        <div class="bg-navy border border-slate-700/60 rounded-xl overflow-hidden shadow-inner flex-1 min-h-[220px]">
                                            <table class="w-full text-left text-xs whitespace-nowrap">
                                                <thead class="text-[8px] text-slate-500 uppercase tracking-[0.2em] font-black border-b border-slate-700/50 bg-[#05101f]">
                                                    <tr>
                                                        <th class="px-5 py-4">Operator Node</th>
                                                        <th class="px-4 py-4 text-center">Status</th>
                                                        <th class="px-4 py-4 text-center">Detected Window</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-white">
                                                    <tr class="transition-colors duration-500 border-b border-slate-800" id="sim-row">
                                                        <td class="px-5 py-4">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-black shadow-inner">JD</div>
                                                                <div>
                                                                    <p class="font-bold text-white text-[11px]">John Doe</p>
                                                                    <p class="text-[8px] text-slate-500 uppercase mt-0.5 tracking-widest">Development</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-center">
                                                            <span id="sim-status" class="bg-blue-500/10 text-blue-400 border border-blue-500/30 px-3 py-1 rounded text-[8px] font-black uppercase tracking-widest transition-all">Productive</span>
                                                        </td>
                                                        <td class="px-4 py-4 text-center font-mono text-slate-300 text-[10px]" id="sim-app">VS Code</td>
                                                    </tr>
                                                    <tr class="opacity-40">
                                                        <td class="px-5 py-4">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-black shadow-inner">SK</div>
                                                                <div><p class="font-bold text-white text-[11px]">Sara Khan</p><p class="text-[8px] text-slate-500 uppercase mt-0.5 tracking-widest">HR</p></div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-center"><span class="text-slate-500 font-black text-[8px] border border-slate-700 bg-slate-800/50 px-2 py-1 rounded">IDLE</span></td>
                                                        <td class="px-4 py-4 text-center font-mono text-slate-500 text-[10px]">—</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- VIEW 2: DEPARTMENTS --}}
                            <div id="admin-view-departments" class="admin-sim-view hidden">
                                <div class="bg-navy border border-slate-700/60 rounded-xl overflow-hidden shadow-inner">
                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                        <thead class="text-[8px] text-slate-500 uppercase tracking-[0.2em] font-black border-b border-slate-700/50 bg-[#05101f]">
                                            <tr>
                                                <th class="px-6 py-5">Node / Department Name</th>
                                                <th class="px-6 py-5 text-center">Code ID</th>
                                                <th class="px-6 py-5 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-white divide-y divide-slate-800/50">
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded bg-gold/10 text-gold flex items-center justify-center"><i class="fas fa-sitemap"></i></div>
                                                    <span class="font-bold text-[11px]">Accounts</span>
                                                </td>
                                                <td class="px-6 py-4 text-center"><span class="bg-slate-800 text-slate-400 px-2 py-1 rounded font-mono text-[9px]">it-10</span></td>
                                                <td class="px-6 py-4 text-right text-slate-500"><i class="fas fa-edit mx-2 cursor-pointer hover:text-white"></i><i class="fas fa-trash cursor-pointer hover:text-red-500"></i></td>
                                            </tr>
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded bg-gold/10 text-gold flex items-center justify-center"><i class="fas fa-sitemap"></i></div>
                                                    <span class="font-bold text-[11px]">Development</span>
                                                </td>
                                                <td class="px-6 py-4 text-center"><span class="bg-slate-800 text-slate-400 px-2 py-1 rounded font-mono text-[9px]">dev-01</span></td>
                                                <td class="px-6 py-4 text-right text-slate-500"><i class="fas fa-edit mx-2 cursor-pointer hover:text-white"></i><i class="fas fa-trash cursor-pointer hover:text-red-500"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- VIEW 3: EMPLOYEES --}}
                            <div id="admin-view-employees" class="admin-sim-view hidden">
                                <div class="mb-4 flex justify-between">
                                    <div class="relative w-64">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                                        <input type="text" placeholder="Search employees..." class="w-full bg-[#010717] border border-slate-700 rounded-lg pl-9 pr-3 py-2 text-[10px] text-white outline-none focus:border-gold">
                                    </div>
                                </div>
                                <div class="bg-navy border border-slate-700/60 rounded-xl overflow-hidden shadow-inner">
                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                        <thead class="text-[8px] text-gold uppercase tracking-[0.2em] font-black border-b border-slate-700/50 bg-[#05101f]">
                                            <tr>
                                                <th class="px-6 py-5">Employee</th>
                                                <th class="px-6 py-5">Designation</th>
                                                <th class="px-6 py-5">Department</th>
                                                <th class="px-6 py-5">Status</th>
                                                <th class="px-6 py-5 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-white divide-y divide-slate-800/50">
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center font-black text-blue-500 text-xs">NT</div>
                                                    <div>
                                                        <p class="font-bold text-[11px]">employee 5</p>
                                                        <p class="text-[9px] text-slate-500">employee5@gmail.com</p>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4"><p class="font-bold text-[11px]">api inspection</p><p class="text-[9px] text-gold">Rs. 45,000</p></td>
                                                <td class="px-6 py-4 text-slate-300">accounts</td>
                                                <td class="px-6 py-4"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded text-[8px] font-black">ACTIVE</span></td>
                                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                                    <button class="w-6 h-6 rounded bg-emerald-500/20 text-emerald-500"><i class="fas fa-star text-[10px]"></i></button>
                                                    <button class="w-6 h-6 rounded bg-blue-500/20 text-blue-500"><i class="fas fa-eye text-[10px]"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-black text-white text-xs"><i class="fas fa-mobile-alt"></i></div>
                                                    <div>
                                                        <p class="font-bold text-[11px]">employee2</p>
                                                        <p class="text-[9px] text-slate-500">employee2@gmail.com</p>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4"><p class="font-bold text-[11px]">psdo store</p><p class="text-[9px] text-gold">Rs. 34,000</p></td>
                                                <td class="px-6 py-4 text-slate-300">accounts</td>
                                                <td class="px-6 py-4"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded text-[8px] font-black">ACTIVE</span></td>
                                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                                    <button class="w-6 h-6 rounded bg-emerald-500/20 text-emerald-500"><i class="fas fa-star text-[10px]"></i></button>
                                                    <button class="w-6 h-6 rounded bg-blue-500/20 text-blue-500"><i class="fas fa-eye text-[10px]"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- VIEW 4: PROJECTS --}}
                            <div id="admin-view-projects" class="admin-sim-view hidden">
                                <div class="bg-navy border border-slate-700/60 rounded-2xl p-6 shadow-inner w-full max-w-sm">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="bg-gold/10 text-gold border border-gold/30 px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-widest">COMPLETED</span>
                                        <span class="text-slate-500 font-mono text-[9px]">#PROJ-5</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-gold uppercase tracking-widest">PAKISAN</p>
                                    <h3 class="text-xl font-black text-white uppercase tracking-wider mb-6">SHADI APP</h3>
                                    
                                    <div class="flex gap-4 border-b border-slate-700 pb-4 mb-4">
                                        <div>
                                            <p class="text-[7px] text-slate-500 uppercase font-black tracking-widest">Uptime Allocation</p>
                                            <p class="text-xs font-bold text-white"><i class="fas fa-clock text-blue-400 mr-1"></i> 0 Days</p>
                                        </div>
                                        <div>
                                            <p class="text-[7px] text-slate-500 uppercase font-black tracking-widest">Timeline Status</p>
                                            <p class="text-xs font-bold text-gold">NOMINAL</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 mb-6">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center font-black text-blue-500 text-[10px]">NT</div>
                                        <span class="text-[9px] font-black text-white">+1 UNITS</span>
                                    </div>

                                    <div class="flex justify-between items-end pt-2 border-t border-slate-700">
                                        <div>
                                            <p class="text-[7px] text-slate-500 uppercase font-black tracking-widest">Calendar Boundary</p>
                                            <p class="text-[10px] font-bold text-white">10 Jun, 2026</p>
                                        </div>
                                        <div class="flex gap-1 text-gold">
                                            <button class="w-6 h-6 rounded border border-gold/30 hover:bg-gold hover:text-navy"><i class="fas fa-eye text-[9px]"></i></button>
                                            <button class="w-6 h-6 rounded border border-gold/30 hover:bg-gold hover:text-navy"><i class="fas fa-cog text-[9px]"></i></button>
                                            <button class="w-6 h-6 rounded border border-rose-500/30 text-rose-500 hover:bg-rose-500 hover:text-white"><i class="fas fa-trash text-[9px]"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- VIEW 5: TASKS BOARD --}}
                            <div id="admin-view-tasks" class="admin-sim-view hidden">
                                <div class="bg-navy border border-slate-700/60 rounded-xl overflow-hidden shadow-inner">
                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                        <thead class="text-[8px] text-gold uppercase tracking-[0.2em] font-black border-b border-slate-700/50 bg-[#05101f]">
                                            <tr>
                                                <th class="px-6 py-5">Objective Details</th>
                                                <th class="px-6 py-5">Assigned Operator</th>
                                                <th class="px-6 py-5 text-center">Completion Stage</th>
                                                <th class="px-6 py-5 text-center">Telemetry Metrics</th>
                                                <th class="px-6 py-5 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-white">
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-5">
                                                    <p class="text-[7px] text-gold uppercase tracking-widest font-black">HABIBA APP</p>
                                                    <p class="font-black text-sm uppercase tracking-wider my-1">BACKEND</p>
                                                    <p class="text-[8px] text-slate-500 uppercase tracking-widest">DUE: 06 OCT 2026</p>
                                                </td>
                                                <td class="px-6 py-5 flex items-center gap-2 mt-2">
                                                    <div class="w-5 h-5 rounded bg-gold/20 text-gold flex items-center justify-center font-black text-[9px]">E</div>
                                                    <span class="font-bold text-[10px] uppercase">EMPLOYEE 5</span>
                                                </td>
                                                <td class="px-6 py-5 text-center">
                                                    <span class="bg-gold/10 border border-gold/30 text-gold px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-widest">HALFWAY</span>
                                                    <div class="w-16 h-1 bg-slate-700 rounded-full mx-auto mt-2 overflow-hidden"><div class="w-1/2 h-full bg-gold rounded-full"></div></div>
                                                    <p class="text-[7px] font-black mt-1">50%</p>
                                                </td>
                                                <td class="px-6 py-5 text-center text-[9px] font-bold text-slate-300">
                                                    Active: 2d
                                                </td>
                                                <td class="px-6 py-5 text-right text-slate-500">
                                                    <i class="fas fa-eye mx-1 cursor-pointer border border-slate-600 p-1.5 rounded hover:text-white"></i>
                                                    <i class="fas fa-edit mx-1 cursor-pointer border border-slate-600 p-1.5 rounded hover:text-white"></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- VIEW 6: ATTENDANCE (SHIFT INTELLIGENCE) --}}
                            <div id="admin-view-attendance" class="admin-sim-view hidden">
                                <div class="flex gap-2 mb-6">
                                    <div class="bg-gold text-navy px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest">ALL TIME</div>
                                    <div class="border border-slate-700 text-slate-400 px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest">DAILY</div>
                                    <div class="border border-slate-700 text-slate-400 px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest">WEEKLY</div>
                                </div>
                                <div class="bg-navy border border-slate-700/60 rounded-xl overflow-hidden shadow-inner">
                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                        <thead class="text-[8px] text-gold uppercase tracking-[0.2em] font-black border-b border-slate-700/50 bg-[#05101f]">
                                            <tr>
                                                <th class="px-6 py-4">Operator Node</th>
                                                <th class="px-4 py-4 text-center">Cycle</th>
                                                <th class="px-4 py-4 text-center">Work Duration</th>
                                                <th class="px-4 py-4 text-center text-gold/70">AI Load</th>
                                                <th class="px-4 py-4 text-center">Visual Pulses</th>
                                                <th class="px-4 py-4 text-center">Temporal Log</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-white divide-y divide-slate-800">
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center font-black text-blue-500 text-xs">NT</div>
                                                    <div><p class="font-bold text-[11px] uppercase">EMPLOYEE 5</p><p class="text-[7px] text-slate-500 uppercase tracking-widest">ID: #13</p></div>
                                                </td>
                                                <td class="px-4 py-4 text-center"><span class="border border-gold/30 text-gold px-2 py-0.5 rounded text-[7px] font-black uppercase">AFTERNOON</span></td>
                                                <td class="px-4 py-4 text-center font-black">1h 40m</td>
                                                <td class="px-4 py-4">
                                                    <div class="flex justify-between text-[7px] font-black text-gold mb-1"><span>AI.USE</span><span>21.90%</span></div>
                                                    <div class="w-16 h-1 bg-slate-800 rounded mx-auto"><div class="w-[21%] h-full bg-gold rounded"></div></div>
                                                </td>
                                                <td class="px-4 py-4 text-center text-[9px] font-black">57 <span class="text-[7px] text-slate-500">CAPS</span></td>
                                                <td class="px-4 py-4 text-center text-[9px] font-bold text-slate-400">20 JUN, 2026</td>
                                            </tr>
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center font-black text-blue-500 text-xs">NT</div>
                                                    <div><p class="font-bold text-[11px] uppercase">EMPLOYEE 5</p><p class="text-[7px] text-slate-500 uppercase tracking-widest">ID: #13</p></div>
                                                </td>
                                                <td class="px-4 py-4 text-center"><span class="border border-gold/30 text-gold px-2 py-0.5 rounded text-[7px] font-black uppercase">AFTERNOON</span></td>
                                                <td class="px-4 py-4 text-center font-black">1h 3m</td>
                                                <td class="px-4 py-4">
                                                    <div class="flex justify-between text-[7px] font-black text-gold mb-1"><span>AI.USE</span><span>62.10%</span></div>
                                                    <div class="w-16 h-1 bg-slate-800 rounded mx-auto"><div class="w-[62%] h-full bg-gold rounded"></div></div>
                                                </td>
                                                <td class="px-4 py-4 text-center text-[9px] font-black">92 <span class="text-[7px] text-slate-500">CAPS</span></td>
                                                <td class="px-4 py-4 text-center text-[9px] font-bold text-slate-400">19 JUN, 2026</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- VIEW 7: WHATSAPP CHAT --}}
                            <div id="admin-view-whatsapp" class="admin-sim-view hidden h-full flex gap-4">
                                <div class="w-1/3 bg-navy border border-slate-700/60 rounded-xl p-4 flex flex-col h-full shadow-inner">
                                    <p class="text-[8px] font-black text-gold uppercase tracking-[0.2em] mb-4"><i class="fas fa-bolt mr-1"></i> Communication Hub</p>
                                    <p class="text-[7px] font-bold text-slate-500 uppercase tracking-widest mb-2">Broadcast Nodes</p>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gold/20 mb-4 cursor-pointer hover:bg-gold/5">
                                        <div class="w-8 h-8 rounded bg-gold/10 text-gold flex items-center justify-center"><i class="fas fa-users"></i></div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-white">General Ops</span>
                                    </div>
                                    <p class="text-[7px] font-bold text-slate-500 uppercase tracking-widest mb-2">Direct Uplink</p>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-700 mb-2 cursor-pointer hover:bg-slate-800">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-black text-white">e</div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-white">EMPLOYEE 2</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-700 bg-slate-800/50 cursor-pointer">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-black text-white">e</div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-white">EMPLOYEE 5</span>
                                    </div>
                                </div>
                                
                                <div class="flex-1 bg-[#010717] border border-slate-700/60 rounded-xl p-4 flex flex-col h-full shadow-inner relative" style="background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-800 pb-3 mb-4">EMPLOYEE 5</p>
                                    
                                    <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar flex flex-col">
                                        {{-- Incoming Message --}}
                                        <div class="self-start bg-[#05101f] border border-slate-700 text-white p-3 rounded-2xl rounded-tl-none max-w-[80%]">
                                            <p class="text-xs font-medium">hello</p>
                                            <p class="text-[6px] text-slate-500 mt-1 text-right">08:59 PM</p>
                                        </div>
                                        
                                        {{-- Outgoing Message --}}
                                        <div class="self-end bg-gold text-navy p-3 rounded-2xl rounded-tr-none max-w-[80%]">
                                            <p class="text-xs font-bold">hy</p>
                                            <p class="text-[6px] text-navy/60 mt-1 text-right">09:05 PM</p>
                                        </div>

                                        {{-- Outgoing Image --}}
                                        <div class="self-end bg-gold text-navy p-2 rounded-2xl rounded-tr-none max-w-[80%] border border-gold-light">
                                            <div class="w-48 h-24 bg-navy rounded-lg overflow-hidden border border-navy/20 relative">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                                <div class="absolute top-1 right-1 w-2 h-2 bg-green-500 rounded-full"></div>
                                            </div>
                                            <p class="text-[6px] text-navy/60 mt-1 text-right">09:05 PM</p>
                                        </div>

                                        {{-- Incoming Message --}}
                                        <div class="self-start bg-[#05101f] border border-slate-700 text-white p-3 rounded-2xl rounded-tl-none max-w-[80%] mt-auto">
                                            <p class="text-xs font-medium">hy</p>
                                            <p class="text-[6px] text-slate-500 mt-1 text-right">09:07 PM</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex gap-2">
                                        <button class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white"><i class="fas fa-paperclip"></i></button>
                                        <input type="text" value="hy|" class="flex-1 bg-[#05101f] border border-slate-700 rounded-full px-4 text-xs text-white outline-none" readonly>
                                        <button class="w-10 h-10 rounded-full bg-emerald-500 text-navy flex items-center justify-center pl-1"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- 💼 EMPLOYEE DASHBOARD SIMULATOR --}}
                <div id="sim-emp-panel" class="w-full bg-[#010717] border border-gold/30 rounded-[2rem] shadow-[0_0_50px_rgba(210,166,61,0.15)] overflow-hidden hidden transition-all duration-500 flex flex-col md:flex-row relative h-[750px]">
                    <div class="absolute inset-0 bg-[linear-gradient(rgba(210,166,61,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(210,166,61,0.03)_1px,transparent_1px)] bg-[size:30px_30px] pointer-events-none z-0"></div>

                    {{-- Employee Sidebar --}}
                    <div class="w-[240px] bg-[#05101f] border-r border-gold/10 p-6 flex flex-col relative z-20 hidden lg:flex">
                        <div class="flex items-center gap-3 mb-12">
                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-light rounded-xl flex items-center justify-center text-navy font-black shadow-lg">E</div>
                            <div>
                                <h1 class="text-white font-black text-xs tracking-widest uppercase">EMPLOYEE</h1>
                                <p class="text-[8px] text-gold font-black uppercase tracking-[0.2em] mt-0.5">WORKSPACE</p>
                            </div>
                        </div>
                        <p class="text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-2">Core Operations</p>
                        <div class="space-y-2 flex-1 select-none">
                            <div onclick="switchEmpView(this, 'emp-view-dashboard', 'Active Terminal')" class="emp-nav-item border-l-2 border-gold bg-gold/10 text-gold rounded-r-lg p-3 text-[11px] font-bold tracking-wide flex items-center gap-3 shadow-inner cursor-pointer transition-colors"><i class="fa-solid fa-terminal w-4 text-gold"></i> Active Terminal</div>
                            <div onclick="switchEmpView(this, 'emp-view-projects', 'Project Board')" class="emp-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium hover:text-white flex items-center gap-3 cursor-pointer transition-colors"><i class="fa-solid fa-rocket w-4"></i> Project</div>
                            <div onclick="switchEmpView(this, 'emp-view-tasks', 'Assigned Objectives')" class="emp-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium hover:text-white flex items-center gap-3 cursor-pointer transition-colors"><i class="fa-solid fa-chart-pie w-4"></i> My Tasks</div>
                            <div onclick="switchEmpView(this, 'emp-view-attendance', 'Shift Records')" class="emp-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium hover:text-white flex items-center gap-3 cursor-pointer transition-colors"><i class="fa-solid fa-clipboard-list w-4"></i> Attendances</div>
                            
                            <p class="text-[8px] font-black text-gold/50 uppercase tracking-[0.3em] mb-3 mt-6 ml-2 pt-4 border-t border-slate-700/50">Team & Comms</p>
                            <div onclick="switchEmpView(this, 'emp-view-chat', 'Secure Comms')" class="emp-nav-item border-l-2 border-transparent text-slate-400 p-3 text-[11px] font-medium hover:text-white flex items-center justify-between cursor-pointer transition-colors">
                                <div class="flex items-center gap-3"><i class="fa-brands fa-whatsapp w-4"></i> Team Chat</div>
                                <span class="bg-red-500 text-white text-[9px] font-black px-1.5 rounded-full">1</span>
                            </div>
                        </div>
                        <div class="mt-auto border-t border-slate-700/50 pt-4">
                            <div class="bg-navy border border-slate-700 rounded-xl p-3 flex items-center gap-3 shadow-inner">
                                <div class="w-8 h-8 bg-[#d2a63d] rounded flex items-center justify-center text-navy font-black text-xs">JD</div>
                                <div>
                                    <p class="text-[10px] font-bold text-white truncate">John Doe</p>
                                    <p class="text-[8px] text-green-400 font-black uppercase tracking-widest flex items-center gap-1 mt-0.5"><span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> ACTIVE</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Employee Main Panel --}}
                    <div class="flex-1 bg-[#0a1225] flex flex-col relative z-10 overflow-hidden" id="emp-main-container">
                        
                        <div class="p-6 lg:p-8 pb-4 border-b border-slate-800 bg-[#0a1225] sticky top-0 z-20">
                            <h2 id="emp-sim-title" class="text-xl font-black text-white uppercase tracking-widest">ACTIVE TERMINAL</h2>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">JOHN DOE <span class="mx-2 text-slate-600">|</span> <span class="text-gold">DEVELOPMENT UNIT</span></p>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8" id="emp-views-wrapper">
                            
                            {{-- VIEW 1: DASHBOARD --}}
                            <div id="emp-view-dashboard" class="emp-sim-view block space-y-6">
                                <div class="flex flex-col xl:flex-row gap-4">
                                    <div class="bg-[#05101f] border border-slate-700/50 rounded-2xl p-5 flex-1 flex items-center gap-5 shadow-lg">
                                        <div class="w-16 h-16 bg-slate-800 rounded-xl flex items-center justify-center font-black text-2xl text-white shadow-inner relative">
                                            JD <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-[#05101f] rounded-full animate-pulse"></div>
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-black text-white uppercase tracking-wider mb-1">Employee 5</h2>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">DEVELOPMENT <span class="mx-2 text-slate-600">|</span> <span class="text-gold">SHIFT ACTIVE</span></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-4">
                                        <div class="bg-[#05101f] border border-slate-700/50 rounded-2xl p-5 flex flex-col items-center justify-center min-w-[130px] shadow-lg">
                                            <p class="text-[8px] text-slate-400 font-black uppercase tracking-widest mb-1">Local Time</p>
                                            <p class="text-xl font-black text-white tracking-widest font-mono" id="emp-live-clock">--:--:--</p>
                                        </div>
                                        <div class="bg-gradient-to-br from-gold to-gold-light border border-gold rounded-2xl p-5 flex flex-col items-center justify-center min-w-[150px] shadow-[0_0_20px_rgba(210,166,61,0.3)]">
                                            <p class="text-[8px] text-navy/80 font-black uppercase tracking-widest mb-1">Active Session</p>
                                            <p class="text-2xl font-black text-navy font-mono tracking-wider" id="emp-session-timer">00:00:00</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-[#05101f] border border-slate-700/50 rounded-2xl p-5 flex justify-around shadow-lg">
                                    <div class="text-center">
                                        <p class="text-[8px] text-slate-400 font-black uppercase tracking-widest mb-1">Active Tasks</p>
                                        <p class="text-2xl font-black text-white">2</p>
                                    </div>
                                    <div class="w-[1px] h-10 bg-slate-700/50"></div>
                                    <div class="text-center">
                                        <p class="text-[8px] text-slate-400 font-black uppercase tracking-widest mb-1">Hours Worked Today</p>
                                        <p class="text-2xl font-black text-gold">7h 0m</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-4">
                                    <div class="bg-[#05101f] border border-slate-700/50 rounded-2xl p-6 shadow-lg">
                                        <h4 class="text-[10px] font-black text-gold uppercase tracking-[0.2em] mb-5 flex items-center gap-2"><i class="fas fa-crosshairs text-lg"></i> Active Objectives</h4>
                                        <div class="bg-[#010717] border border-slate-700/50 rounded-xl p-5 shadow-inner mb-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <span class="text-[7px] bg-gold/10 text-gold px-2 py-0.5 rounded font-black tracking-widest uppercase border border-gold/20">IN PROGRESS</span>
                                                    <h5 class="text-sm font-bold text-white mt-2">API Integration Logic</h5>
                                                </div>
                                                <button class="bg-gold text-navy px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] hover:bg-gold-light transition-colors shadow-lg">UPDATE LOG</button>
                                            </div>
                                            <div class="mt-5 flex items-center gap-3">
                                                <div class="flex-1 h-2 bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-gold rounded-full w-[65%] relative overflow-hidden">
                                                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                                    </div>
                                                </div>
                                                <span class="text-[9px] font-black text-slate-400">65%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-[#05101f] border border-slate-700/50 rounded-2xl p-6 shadow-lg">
                                        <h4 class="text-[10px] font-black text-gold uppercase tracking-[0.2em] mb-5 flex items-center gap-2"><i class="fas fa-bullhorn text-lg"></i> Commander's Feedback</h4>
                                        <div class="bg-[#010717] border border-gold/20 border-l-2 border-l-gold rounded-xl p-5 shadow-inner">
                                            <div class="flex justify-between items-center mb-3">
                                                <span class="text-[9px] bg-gold/10 text-gold px-2.5 py-1 rounded font-black tracking-widest uppercase">SCORE: 9/10</span>
                                                <span class="text-[8px] text-slate-500 font-bold uppercase tracking-widest">2 Days Ago</span>
                                            </div>
                                            <p class="text-xs text-slate-300 italic leading-relaxed">"Excellent progress on the API endpoints. System logic is solid. Keep up the momentum."</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- VIEW 2: PROJECTS --}}
                            <div id="emp-view-projects" class="emp-sim-view hidden">
                                <div class="bg-navy border border-slate-700/60 rounded-2xl p-6 shadow-inner w-full max-w-sm">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="bg-gold/10 text-gold border border-gold/30 px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-widest">ASSIGNED</span>
                                        <span class="text-slate-500 font-mono text-[9px]">#PROJ-5</span>
                                    </div>
                                    <p class="text-[9px] font-bold text-gold uppercase tracking-widest">PAKISAN</p>
                                    <h3 class="text-xl font-black text-white uppercase tracking-wider mb-6">SHADI APP</h3>
                                    
                                    <div class="flex gap-4 border-b border-slate-700 pb-4 mb-4">
                                        <div>
                                            <p class="text-[7px] text-slate-500 uppercase font-black tracking-widest">Timeline Status</p>
                                            <p class="text-xs font-bold text-gold">NOMINAL</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-end pt-2 border-t border-slate-700">
                                        <div>
                                            <p class="text-[7px] text-slate-500 uppercase font-black tracking-widest">Calendar Boundary</p>
                                            <p class="text-[10px] font-bold text-white">10 Jun, 2026</p>
                                        </div>
                                        <button class="bg-gold text-navy px-4 py-1.5 rounded text-[8px] font-black uppercase tracking-widest hover:bg-gold-light">View details</button>
                                    </div>
                                </div>
                            </div>

                            {{-- VIEW 3: TASKS --}}
                            <div id="emp-view-tasks" class="emp-sim-view hidden">
                                <div class="bg-[#010717] border border-slate-700/50 rounded-xl p-5 shadow-inner mb-4 max-w-2xl">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-[7px] bg-gold/10 text-gold px-2 py-0.5 rounded font-black tracking-widest uppercase border border-gold/20">IN PROGRESS</span>
                                            <h5 class="text-sm font-bold text-white mt-2">API Integration Logic</h5>
                                        </div>
                                        <button class="bg-gold text-navy px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] hover:bg-gold-light transition-colors shadow-lg">UPDATE LOG</button>
                                    </div>
                                    <div class="mt-5 flex items-center gap-3">
                                        <div class="flex-1 h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gold rounded-full w-[65%] relative overflow-hidden">
                                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-black text-slate-400">65%</span>
                                    </div>
                                </div>
                            </div>

                            {{-- VIEW 4: ATTENDANCE --}}
                            <div id="emp-view-attendance" class="emp-sim-view hidden">
                                <div class="bg-navy border border-slate-700/60 rounded-xl overflow-hidden shadow-inner max-w-3xl">
                                    <table class="w-full text-left text-xs whitespace-nowrap">
                                        <thead class="text-[8px] text-gold uppercase tracking-[0.2em] font-black border-b border-slate-700/50 bg-[#05101f]">
                                            <tr>
                                                <th class="px-6 py-4">Date</th>
                                                <th class="px-4 py-4 text-center">Cycle</th>
                                                <th class="px-4 py-4 text-center">First Entry</th>
                                                <th class="px-4 py-4 text-center">Last Exit</th>
                                                <th class="px-4 py-4 text-center">Total Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-white divide-y divide-slate-800">
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 text-[10px] font-bold">20 JUN, 2026</td>
                                                <td class="px-4 py-4 text-center"><span class="border border-gold/30 text-gold px-2 py-0.5 rounded text-[7px] font-black uppercase">AFTERNOON</span></td>
                                                <td class="px-4 py-4 text-center font-mono text-slate-400 text-[10px]">13:06:40</td>
                                                <td class="px-4 py-4 text-center"><span class="bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded text-[8px] font-black animate-pulse">CONNECTED</span></td>
                                                <td class="px-4 py-4 text-center font-black text-gold">7h 14m</td>
                                            </tr>
                                            <tr class="hover:bg-slate-800/20">
                                                <td class="px-6 py-4 text-[10px] font-bold">19 JUN, 2026</td>
                                                <td class="px-4 py-4 text-center"><span class="border border-gold/30 text-gold px-2 py-0.5 rounded text-[7px] font-black uppercase">AFTERNOON</span></td>
                                                <td class="px-4 py-4 text-center font-mono text-slate-400 text-[10px]">13:10:00</td>
                                                <td class="px-4 py-4 text-center font-mono text-slate-400 text-[10px]">21:15:00</td>
                                                <td class="px-4 py-4 text-center font-black text-slate-300">8h 05m</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- VIEW 5: CHAT --}}
                            <div id="emp-view-chat" class="emp-sim-view hidden h-full">
                                <div class="bg-[#010717] border border-slate-700/60 rounded-xl p-4 flex flex-col h-full shadow-inner relative max-w-3xl" style="background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                                    <p class="text-[10px] font-black text-gold uppercase tracking-widest border-b border-slate-800 pb-3 mb-4"><i class="fas fa-crown mr-2"></i> ADMIN PRO</p>
                                    
                                    <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar flex flex-col">
                                        {{-- Outgoing Message --}}
                                        <div class="self-end bg-gold text-navy p-3 rounded-2xl rounded-tr-none max-w-[80%]">
                                            <p class="text-xs font-bold">hello</p>
                                            <p class="text-[6px] text-navy/60 mt-1 text-right">08:59 PM</p>
                                        </div>
                                        
                                        {{-- Incoming Message --}}
                                        <div class="self-start bg-[#05101f] border border-slate-700 text-white p-3 rounded-2xl rounded-tl-none max-w-[80%]">
                                            <p class="text-xs font-medium">hy</p>
                                            <p class="text-[6px] text-slate-500 mt-1 text-right">09:05 PM</p>
                                        </div>

                                        {{-- Incoming Image --}}
                                        <div class="self-start bg-[#05101f] border border-slate-700 text-white p-2 rounded-2xl rounded-tl-none max-w-[80%]">
                                            <div class="w-48 h-24 bg-navy rounded-lg overflow-hidden border border-slate-600 relative">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                                <div class="absolute top-1 right-1 w-2 h-2 bg-green-500 rounded-full"></div>
                                            </div>
                                            <p class="text-[6px] text-slate-500 mt-1 text-right">09:05 PM</p>
                                        </div>

                                        {{-- Outgoing Message --}}
                                        <div class="self-end bg-gold text-navy p-3 rounded-2xl rounded-tr-none max-w-[80%] mt-auto">
                                            <p class="text-xs font-bold">System reporting clear</p>
                                            <p class="text-[6px] text-navy/60 mt-1 text-right">09:07 PM</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex gap-2">
                                        <button class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white"><i class="fas fa-paperclip"></i></button>
                                        <input type="text" value="Noted|" class="flex-1 bg-[#05101f] border border-slate-700 rounded-full px-4 text-xs text-white outline-none" readonly>
                                        <button class="w-10 h-10 rounded-full bg-gold text-navy flex items-center justify-center pl-1"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- EXACT COMPARISON MATRIX TABLE (Detailed PDF Synced) --}}
        <section id="matrix" class="py-24 px-6 max-w-[1400px] mx-auto border-t border-gold/10">
            <div class="text-center mb-16">
                <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-2">Comprehensive System Feature Matrix</p>
                <h2 class="text-3xl md:text-5xl font-black text-white uppercase mb-4">Compare SaaS Benefits According to Plans</h2>
                <p class="text-xs text-slate-400 max-w-2xl mx-auto">Choose a plan group below to see only the plans you care about. Each column shows what that plan includes — green check means included, grey cross means not available.</p>
            </div>

            {{-- TIER FILTER TABS --}}
            <div class="flex justify-center mb-4">
                <div class="p-2 bg-[#05101f] rounded-2xl inline-flex mx-auto border border-gold/20 shadow-xl gap-2 flex-wrap justify-center">
                    <button onclick="filterMatrixTier('all', this)" class="matrix-tab-btn active bg-gold text-navy px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(210,166,61,0.4)]">
                        All Plans
                        <span class="matrix-tab-sub text-navy/80">Compare all 6 plans</span>
                    </button>
                    <button onclick="filterMatrixTier('foundational', this)" class="matrix-tab-btn bg-transparent text-slate-400 hover:text-white hover:bg-gold/10 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Starter & Basic
                        <span class="matrix-tab-sub text-slate-500">Small teams — essential tracking</span>
                    </button>
                    <button onclick="filterMatrixTier('intelligent', this)" class="matrix-tab-btn bg-transparent text-slate-400 hover:text-white hover:bg-gold/10 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Pro & Advanced
                        <span class="matrix-tab-sub text-slate-500">Smart monitoring & alerts</span>
                    </button>
                    <button onclick="filterMatrixTier('corporate', this)" class="matrix-tab-btn bg-transparent text-slate-400 hover:text-white hover:bg-gold/10 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Private-Label & Enterprise
                        <span class="matrix-tab-sub text-slate-500">Your brand, full control</span>
                    </button>
                </div>
            </div>
            <p id="matrix-tier-hint" class="matrix-tier-hint text-center text-[11px] text-slate-400 max-w-3xl mx-auto mb-8 px-4 leading-relaxed">
                <i class="fa-solid fa-circle-info text-gold mr-1"></i>
                Showing <strong class="text-white">all 6 plans</strong> — Starter, Basic, Pro, Advanced, Private-Label, and Enterprise.
            </p>

            <div class="matrix-table-wrap glass-border rounded-2xl shadow-[0_0_40px_rgba(0,0,0,0.5)]">
                <table class="matrix-comparison-table text-left text-slate-300 font-medium">
                    <thead class="bg-navy-light border-b border-gold/20 font-black text-slate-300">
                        <tr class="matrix-tier-row uppercase tracking-[0.12em]">
                            <th class="border-r border-gold/10 text-left normal-case tracking-normal font-bold text-[11px]">What You Get</th>
                            <th colspan="2" class="matrix-col-foundational text-center text-gold border-r border-gold/10">Starter & Basic Plans</th>
                            <th colspan="2" class="matrix-col-intelligent text-center text-gold border-r border-gold/10">Pro & Advanced Plans</th>
                            <th colspan="2" class="matrix-col-corporate text-center text-gold">Private-Label & Enterprise</th>
                        </tr>
                        <tr class="matrix-plan-row">
                            <th class="border-r border-gold/10 text-slate-400 font-semibold">Feature</th>
                            <th class="matrix-col-foundational text-center text-white">Starter<span class="block text-[9px] text-slate-500 font-medium mt-0.5">PKR 9,999/mo</span></th>
                            <th class="matrix-col-foundational text-center text-white">Basic<span class="block text-[9px] text-slate-500 font-medium mt-0.5">PKR 19,999/mo</span></th>
                            <th class="matrix-col-intelligent text-center text-navy bg-gold">Pro<span class="block text-[9px] font-bold mt-0.5 opacity-90">PKR 34,999 · Best Value</span></th>
                            <th class="matrix-col-intelligent text-center text-white">Advanced<span class="block text-[9px] text-slate-500 font-medium mt-0.5">PKR 59,999/mo</span></th>
                            <th class="matrix-col-corporate text-center text-white">Private-Label<span class="block text-[9px] text-slate-500 font-medium mt-0.5">PKR 99,999/mo</span></th>
                            <th class="matrix-col-corporate text-center text-white">Enterprise<span class="block text-[9px] text-slate-500 font-medium mt-0.5">Custom pricing</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gold/10 text-xs">
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Minimum Users<span class="feature-hint">Lowest number of employees you can subscribe with</span></td>
                            <td class="matrix-col-foundational text-center text-slate-300">5 users min</td>
                            <td class="matrix-col-foundational text-center text-slate-300">5 users min</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-slate-300">5 users min</td>
                            <td class="matrix-col-intelligent text-center text-slate-300">5 users min</td>
                            <td class="matrix-col-corporate text-center text-slate-300">5 users min</td>
                            <td class="matrix-col-corporate text-center text-slate-300">Flexible / custom</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Auto Screenshot Storage<span class="feature-hint">How long captured screen images are kept for review</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-green-400"><i class="fa-solid fa-check mr-1"></i>7 days</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-green-400"><i class="fa-solid fa-check mr-1"></i>15 days</td>
                            <td class="matrix-col-intelligent text-center text-green-400"><i class="fa-solid fa-check mr-1"></i>30 days</td>
                            <td class="matrix-col-corporate text-center text-green-400"><i class="fa-solid fa-check mr-1"></i>30 days</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Custom / permanent</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">On-Demand Screenshots<span class="feature-hint">Admin can capture an employee screen instantly, anytime</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — admin trigger</td>
                            <td class="matrix-col-intelligent text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — admin trigger</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — admin trigger</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — admin trigger</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Distraction & Idle Alerts<span class="feature-hint">Detects when employees visit non-work sites or go idle</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-green-400"><i class="fa-solid fa-check mr-1"></i>Basic alerts</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-green-400"><i class="fa-solid fa-check mr-1"></i>Basic alerts</td>
                            <td class="matrix-col-intelligent text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>OCR + live audio alerts</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>OCR + live audio alerts</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Real-time audio alerts</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Screenshot Text Scan (OCR)<span class="feature-hint">Reads text inside screenshots to flag sensitive or off-task content</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — OCR scan</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — OCR scan</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — OCR scan</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">AI Tool Usage Tracking<span class="feature-hint">Tracks when employees use ChatGPT, Copilot, and similar AI tools</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — tracked</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — tracked</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Yes — tracked</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Team Chat & Voice Notes<span class="feature-hint">In-app messaging between admin and employees, like WhatsApp</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-slate-400"><i class="fa-solid fa-check mr-1"></i>Limited chat only</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Full chat + voice</td>
                            <td class="matrix-col-intelligent text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Full chat + voice</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Full chat + voice</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Full chat + voice</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Your Brand & Custom Domain<span class="feature-hint">Replace EAMS branding with your company logo and website URL</span></td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-foundational text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-intelligent text-center text-slate-500"><i class="fa-solid fa-xmark mr-1"></i>Not included</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Full branding + domain</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>Fully white-labeled</td>
                        </tr>
                        <tr class="hover:bg-gold/5 transition-colors">
                            <td class="feature-label text-white border-r border-gold/10">Customer Support<span class="feature-hint">How fast and how personally we help your team</span></td>
                            <td class="matrix-col-foundational text-center text-slate-300">Email & web portal</td>
                            <td class="matrix-col-foundational text-center text-slate-300">Email & web portal</td>
                            <td class="matrix-col-intelligent text-center bg-gold/5 text-slate-300">Standard priority</td>
                            <td class="matrix-col-intelligent text-center text-slate-300">Priority support</td>
                            <td class="matrix-col-corporate text-center text-slate-300">Priority support</td>
                            <td class="matrix-col-corporate text-center text-green-400 font-bold"><i class="fa-solid fa-check mr-1"></i>24/7 dedicated manager</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>


        {{-- COST ENGINE --}}
        <section id="pricing" class="py-24 px-6 max-w-[1400px] mx-auto border-t border-gold/10">
            <div class="text-center mb-16">
                <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-2">Financial Engine</p>
                <h2 class="text-3xl md:text-5xl font-black text-white uppercase mb-4">SaaS Cost Estimator</h2>
                <p class="text-xs text-slate-400 max-w-2xl mx-auto">Calculate your exact monthly infrastructure cost. Each plan starts with a minimum 5-user subscription. Additional users are billed according to the selected plan's extra-user rate.</p>
            </div>

            <div class="glass-border rounded-3xl p-8 lg:p-12 mb-20 shadow-[0_0_50px_rgba(0,0,0,0.5)] flex flex-col xl:flex-row gap-12">
                <div class="flex-1 space-y-8">
                    <div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-wider mb-2">Configure Deployment</h3>
                        <p class="text-xs text-slate-400 border-b border-gold/20 pb-4">Plan selection is based on the required feature level, not only the number of employees.</p>
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-black text-gold uppercase tracking-[0.2em] mb-4">1. Select Required Plan Tier</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <button class="plan-btn border border-gold bg-gold/10 text-gold p-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center relative overflow-hidden group" data-base="9999" data-extra="1000" data-name="Starter">
                                <span class="relative z-10">Starter<br><span class="text-[9px] font-mono font-bold mt-1 block opacity-80">PKR 9,999</span></span>
                            </button>
                            <button class="plan-btn border border-slate-700 text-slate-400 hover:border-gold/50 hover:bg-gold/5 p-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center relative" data-base="19999" data-extra="1500" data-name="Basic">
                                Basic<br><span class="text-[9px] font-mono font-bold mt-1 block opacity-80">PKR 19,999</span>
                            </button>
                            <button class="plan-btn border border-slate-700 text-slate-400 hover:border-gold/50 hover:bg-gold/5 p-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center relative" data-base="34999" data-extra="2000" data-name="Pro">
                                Pro<br><span class="text-[9px] font-mono font-bold mt-1 block opacity-80">PKR 34,999</span>
                            </button>
                            <button class="plan-btn border border-slate-700 text-slate-400 hover:border-gold/50 hover:bg-gold/5 p-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center relative" data-base="59999" data-extra="3000" data-name="Advanced">
                                Advanced<br><span class="text-[9px] font-mono font-bold mt-1 block opacity-80">PKR 59,999</span>
                            </button>
                            <button class="plan-btn border border-slate-700 text-slate-400 hover:border-gold/50 hover:bg-gold/5 p-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-center relative" data-base="99999" data-extra="3500" data-name="Private-Label">
                                P-Label<br><span class="text-[9px] font-mono font-bold mt-1 block opacity-80">PKR 99,999</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-[11px] font-black text-gold uppercase tracking-[0.2em]">2. Total Operator Seats</label>
                            <span id="sliderValue" class="text-sm font-black text-navy bg-gold px-4 py-1.5 rounded-lg shadow-lg">5 Seats</span>
                        </div>
                        <div class="flex items-center gap-4 bg-navy-light p-4 rounded-xl border border-gold/10">
                            <button class="w-10 h-10 rounded-lg bg-navy border border-gold/30 text-gold font-bold hover:bg-gold hover:text-navy transition-colors flex items-center justify-center" onclick="updateSlider(-1)"><i class="fas fa-minus"></i></button>
                            <input type="range" id="empSlider" min="5" max="100" value="5" class="flex-1">
                            <button class="w-10 h-10 rounded-lg bg-navy border border-gold/30 text-gold font-bold hover:bg-gold hover:text-navy transition-colors flex items-center justify-center" onclick="updateSlider(1)"><i class="fas fa-plus"></i></button>
                            <button class="hidden md:flex w-10 h-10 rounded-lg bg-navy border border-gold/30 text-gold font-bold hover:bg-gold hover:text-navy transition-colors items-center justify-center text-xs" onclick="updateSlider(5)">+5</button>
                        </div>
                        <div class="flex justify-between text-[9px] text-slate-500 font-black uppercase tracking-widest mt-2 px-2">
                            <span>Min Baseline: 5</span>
                            <span>Max: Custom</span>
                        </div>
                    </div>
                </div>

                <div class="xl:w-[400px] bg-navy-light rounded-2xl p-8 border-2 border-gold/20 flex flex-col justify-between relative overflow-hidden shadow-[0_0_30px_rgba(0,0,0,0.8)]">
                    <div class="absolute -right-10 -top-10 text-8xl text-gold/5"><i class="fas fa-receipt"></i></div>
                    
                    <div>
                        <div class="bg-gold/10 border border-gold/30 rounded-lg p-3 mb-6">
                            <p class="text-[10px] text-gold font-bold leading-tight"><i class="fa-solid fa-circle-info mr-1"></i> Cost Logic: First 5 users are covered in the Base Plan. Every additional user is billed at the plan's Extra User Rate.</p>
                        </div>

                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-4 border-b border-slate-700 pb-2">Dynamic Fee Breakdown</h3>
                        
                        <div class="space-y-4 text-xs font-medium">
                            <div class="flex justify-between text-slate-300">
                                <span>Base Subscription <span class="text-slate-500">(First 5 Seats)</span></span><span class="font-mono text-white" id="calcBasePrice">PKR 9,999</span>
                            </div>
                            <div class="flex justify-between text-slate-300 border-b border-slate-800 pb-4">
                                <span>Additional Users</span><span class="font-bold text-white" id="calcReqSeats">0 User(s)</span>
                            </div>
                            
                            <div class="flex justify-between text-slate-400 text-[10px] uppercase tracking-widest pt-2">
                                <span>Extra User Rate</span><span class="font-mono" id="calcBundleRate">PKR 1,000 /mo</span>
                            </div>
                            <div class="flex justify-between text-slate-300 font-bold border-b border-gold/10 pb-4">
                                <span>Total Extra Fee</span><span class="font-mono text-white" id="calcExtraFee">PKR 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gold/30 flex justify-between items-end">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Estimated Rate:</span>
                        <div class="text-3xl font-black text-gold font-mono tracking-tight text-right" id="calcTotal">PKR 9,999<span class="text-sm text-slate-500 font-sans tracking-normal block">/mo</span></div>
                    </div>
                    <a href="{{ route('login') }}" class="mt-6 block w-full bg-gold hover:bg-gold-light text-navy text-center font-black text-[11px] uppercase tracking-[0.2em] py-4 rounded-xl transition-all shadow-[0_0_20px_rgba(210,166,61,0.3)]">
                        Deploy System
                    </a>
                </div>
            </div>

            {{-- PRICING CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                
                {{-- Starter --}}
                <div class="glass-border p-8 rounded-2xl flex flex-col relative group hover:border-gold/50 transition-colors">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1">Foundational Tier</p>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-widest">EAMS Starter</h3>
                    <p class="text-[11px] text-slate-400 mb-6 h-8 leading-relaxed">Basic tracking and task visibility for very small offices and freelancers.</p>
                    <div class="mb-6 pb-6 border-b border-gold/10">
                        <p class="text-[9px] uppercase text-slate-500 font-black tracking-widest mb-1">Monthly Cost (Min 5 Users):</p>
                        <span class="text-3xl font-black text-white">PKR 9,999</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Extra User Rate</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 1,000 /mo</p>
                        </div>
                        <div class="border-l border-slate-700 pl-4">
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Setup Fee</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 15k-25k</p>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-8 text-[11px] text-slate-300 flex-1 font-medium">
                        <li class="font-black text-[9px] text-gold uppercase tracking-[0.2em] mb-3">Included Features:</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Employee & Dept Management</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Project & Task Tracking</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Active & Idle Time Tracking</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Basic Shift Summaries</li>
                        <li><i class="fa-solid fa-ban text-slate-600 mr-2"></i> <span class="text-slate-500 line-through">Screenshot Monitoring</span></li>
                    </ul>
                    <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest border-t border-slate-700 pt-5 mb-5">
                        <span>Screenshot Duration:</span>
                        <span class="text-slate-600"><i class="fa-solid fa-ban"></i> Disabled</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full text-center border border-gold/30 text-gold hover:bg-gold hover:text-navy py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Select Starter</a>
                </div>

                {{-- Basic --}}
                <div class="glass-border p-8 rounded-2xl flex flex-col relative group hover:border-gold/50 transition-colors">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1">Foundational Tier</p>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-widest">EAMS Basic</h3>
                    <p class="text-[11px] text-slate-400 mb-6 h-8 leading-relaxed">Adds screenshot capabilities and distraction pattern detection logic.</p>
                    <div class="mb-6 pb-6 border-b border-gold/10">
                        <p class="text-[9px] uppercase text-slate-500 font-black tracking-widest mb-1">Monthly Cost (Min 5 Users):</p>
                        <span class="text-3xl font-black text-white">PKR 19,999</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Extra User Rate</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 1,500 /mo</p>
                        </div>
                        <div class="border-l border-slate-700 pl-4">
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Setup Fee</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 35k-50k</p>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-8 text-[11px] text-slate-300 flex-1 font-medium">
                        <li class="font-black text-[9px] text-gold uppercase tracking-[0.2em] mb-3">Included Features:</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Auto Screenshots Capture</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Basic Distraction Detection</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Pure Productive Time Calc</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Limited Team Communication</li>
                    </ul>
                    <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest border-t border-slate-700 pt-5 mb-5">
                        <span>Screenshot Duration:</span>
                        <span class="text-green-400">7 Days</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full text-center border border-gold/30 text-gold hover:bg-gold hover:text-navy py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Select Basic</a>
                </div>

                {{-- Pro --}}
                <div class="bg-navy-light border-2 border-gold p-8 rounded-2xl flex flex-col relative shadow-[0_0_40px_rgba(210,166,61,0.2)] md:scale-105 z-10">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gold text-navy px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest shadow-lg">Best Value</div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1">Intelligent Tier</p>
                    <h3 class="text-2xl font-black text-gold mb-2 uppercase tracking-widest">EAMS Pro</h3>
                    <p class="text-[11px] text-slate-400 mb-6 h-8 leading-relaxed">Communication, files, task control, and better reporting.</p>
                    <div class="mb-6 pb-6 border-b border-gold/20">
                        <p class="text-[9px] uppercase text-gold/70 font-black tracking-widest mb-1">Monthly Cost (Min 5 Users):</p>
                        <span class="text-3xl font-black text-gold">PKR 34,999</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Extra User Rate</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 2,000 /mo</p>
                        </div>
                        <div class="border-l border-slate-700 pl-4">
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Setup Fee</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 50k-75k</p>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-8 text-[11px] text-white flex-1 font-medium">
                        <li class="font-black text-[9px] text-gold uppercase tracking-[0.2em] mb-3">Included Features:</li>
                        <li><i class="fa-solid fa-check text-gold mr-2"></i> Manual Admin Screenshot Trigger</li>
                        <li><i class="fa-solid fa-check text-gold mr-2"></i> Full Team Chat & Voice Notes</li>
                        <li><i class="fa-solid fa-check text-gold mr-2"></i> Task Forwarding & File Sharing</li>
                        <li><i class="fa-solid fa-check text-gold mr-2"></i> Advanced Shift Summaries & Rating</li>
                    </ul>
                    <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest border-t border-slate-700 pt-5 mb-5">
                        <span>Screenshot Duration:</span>
                        <span class="text-gold font-bold">15 Days</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full text-center bg-gold hover:bg-gold-light text-navy py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-[0_0_15px_rgba(210,166,61,0.4)]">Select Pro</a>
                </div>

                {{-- Advanced --}}
                <div class="glass-border p-8 rounded-2xl flex flex-col relative group hover:border-gold/50 transition-colors">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-navy border border-gold text-gold px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest shadow-lg">Maximum Intelligence</div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1 mt-2">Enterprise Tier</p>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-widest">EAMS Advanced</h3>
                    <p class="text-[11px] text-slate-400 mb-6 h-8 leading-relaxed">OCR, AI-tool tracking, alerts, and audit visibility.</p>
                    <div class="mb-6 pb-6 border-b border-gold/10">
                        <p class="text-[9px] uppercase text-slate-500 font-black tracking-widest mb-1">Monthly Cost (Min 5 Users):</p>
                        <span class="text-3xl font-black text-white">PKR 59,999</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Extra User Rate</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 3,000 /mo</p>
                        </div>
                        <div class="border-l border-slate-700 pl-4">
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Setup Fee</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 100k-150k</p>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-8 text-[11px] text-slate-300 flex-1 font-medium">
                        <li class="font-black text-[9px] text-gold uppercase tracking-[0.2em] mb-3">Included Features:</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> OCR Screenshot Analysis</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> AI Detection (ChatGPT, Claude)</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Approved Coding IDE Detection</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Real-time Audio Alerts & Logs</li>
                    </ul>
                    <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest border-t border-slate-700 pt-5 mb-5">
                        <span>Screenshot Duration:</span>
                        <span class="text-green-400 font-bold">30 Days</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full text-center border border-gold/30 text-gold hover:bg-gold hover:text-navy py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Select Advanced</a>
                </div>

                {{-- Private-Label --}}
                <div class="glass-border p-8 rounded-2xl flex flex-col relative group hover:border-gold/50 transition-colors">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-navy border border-gold text-gold px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest shadow-lg">Complete Control</div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1 mt-2">Corporate Tier</p>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-widest">EAMS Private-Label</h3>
                    <p class="text-[11px] text-slate-400 mb-6 h-8 leading-relaxed">Client-branded internal platform operating securely.</p>
                    <div class="mb-6 pb-6 border-b border-gold/10">
                        <p class="text-[9px] uppercase text-slate-500 font-black tracking-widest mb-1">Monthly Cost (Min 5 Users):</p>
                        <span class="text-3xl font-black text-white">PKR 99,999</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Extra User Rate</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 3,500 /mo</p>
                        </div>
                        <div class="border-l border-slate-700 pl-4">
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Setup Fee</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 150k-300k</p>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-8 text-[11px] text-slate-300 flex-1 font-medium">
                        <li class="font-black text-[9px] text-gold uppercase tracking-[0.2em] mb-3">Included Features:</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Client Company Name & Logo</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Custom Brand Colors & Login</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Custom Domain or Subdomain</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Branded Reports & Emails</li>
                        <li><i class="fa-solid fa-ban text-red-400 mr-2"></i> <span class="text-slate-400">Resale Rights Not Allowed</span></li>
                    </ul>
                    <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest border-t border-slate-700 pt-5 mb-5">
                        <span>Screenshot Duration:</span>
                        <span class="text-green-400 font-bold">30 Days</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full text-center border border-gold/30 text-gold hover:bg-gold hover:text-navy py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Select Private Label</a>
                </div>

                {{-- Enterprise --}}
                <div class="bg-[#05101f] border border-slate-700 p-8 rounded-2xl flex flex-col relative group hover:border-slate-500 transition-colors">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1">Corporate Tier</p>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-widest">EAMS Enterprise</h3>
                    <p class="text-[11px] text-slate-400 mb-6 h-8 leading-relaxed">Large-scale requirements, custom rules, integrations, and SLA.</p>
                    <div class="mb-6 pb-6 border-b border-slate-800">
                        <p class="text-[9px] uppercase text-slate-500 font-black tracking-widest mb-1">Monthly Cost:</p>
                        <span class="text-3xl font-black text-white">Custom Pricing</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Extra User Rate</p>
                            <p class="text-[10px] font-mono text-white mt-1">Custom</p>
                        </div>
                        <div class="border-l border-slate-700 pl-4">
                            <p class="text-[8px] uppercase text-slate-500 font-black tracking-widest">Setup Fee</p>
                            <p class="text-[10px] font-mono text-white mt-1">PKR 300k+</p>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-8 text-[11px] text-slate-300 flex-1 font-medium">
                        <li class="font-black text-[9px] text-slate-500 uppercase tracking-[0.2em] mb-3">Included Features:</li>
                        <li><i class="fa-solid fa-check text-slate-400 mr-2"></i> Custom Integrations (HR/Payroll)</li>
                        <li><i class="fa-solid fa-check text-slate-400 mr-2"></i> Custom Data Retention Policies</li>
                        <li><i class="fa-solid fa-check text-slate-400 mr-2"></i> Priority SLA & Dedicated Mgr</li>
                    </ul>
                    <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest border-t border-slate-700 pt-5 mb-5">
                        <span>Screenshot Duration:</span>
                        <span class="text-white font-bold">Infinite / Permanent</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full text-center border border-slate-600 text-slate-300 hover:bg-slate-700 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Contact Sales</a>
                </div>

            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 4: THE "PURE TIME" TRACKING ENGINE
        ══════════════════════════════════════════════════════════ --}}
        <section class="py-24 px-6 max-w-[1400px] mx-auto border-t border-gold/10 flex flex-col md:flex-row gap-16 items-center gsap-fade-up">
            <div class="flex-1 space-y-8">
                <div>
                    <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-2">Algorithm & Logic</p>
                    <h2 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight mb-4">The "Pure Time" Tracking Engine</h2>
                    <p class="text-sm text-slate-400 leading-relaxed">Rather than relying on vague manual timesheets, <strong>EAMS</strong> logs work seconds directly using client keystroke and activity tracking. At the end of every work shift, the background mathematical framework deducts exact idle session periods to calculate the precise net productive hours used.</p>
                </div>
                
                <div class="space-y-4">
                    <div class="glass-border rounded-xl p-6 border-l-4 border-l-gold">
                        <p class="text-sm font-black text-white mb-2 flex items-center gap-2"><i class="fa-solid fa-shield text-gold"></i> OCR Distraction Detection Matrix</p>
                        <p class="text-[11px] text-slate-400 mb-1"><strong>Social channels excluded:</strong> youtube, youtu.be, facebook, fbcdn, whatsapp, netflix, snapchat, twitter.</p>
                        <p class="text-[11px] text-slate-400"><strong>AI Co-Pilot monitors:</strong> chatgpt, gemini, claude, deepseek, midjourney, perplexity, grok, copilot.</p>
                    </div>
                    <div class="glass-border rounded-xl p-6 border-l-4 border-l-[#1dd182]">
                        <p class="text-sm font-black text-white mb-2 flex items-center gap-2"><i class="fa-solid fa-code text-[#1dd182]"></i> Approved Workspace Environments Validated</p>
                        <p class="text-[11px] text-slate-400">Processes explicitly validate VS Code, Visual Studio and standard code.exe workspaces to protect legitimate engineer development from deduction counts.</p>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 w-full bg-navy-light border border-gold/20 rounded-3xl p-10 text-center shadow-[0_0_50px_rgba(0,0,0,0.5)] relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-gold/5 rounded-full blur-3xl"></div>
                <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-6 relative z-10">Active Calculator Algorithm</p>
                <h3 class="text-2xl font-black text-white uppercase mb-8 relative z-10">Shift Metrics Deduction Formula</h3>
                
                <div class="glass-border rounded-2xl p-8 mb-6 relative overflow-hidden z-10">
                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-4">Dynamic Shift Matrix Calculates:</p>
                    <p class="text-2xl md:text-3xl font-black text-white mb-3 tracking-wider">(Total Shift Seconds)</p>
                    <p class="text-sm text-red-500 font-black mb-3 uppercase tracking-[0.3em] bg-red-500/10 inline-block px-4 py-1 rounded border border-red-500/20">Minus</p>
                    <p class="text-xl md:text-2xl font-black text-gold mb-8 tracking-wider">(Sum of Distraction Seconds)</p>
                    <div class="border-t-2 border-dashed border-gold/30 pt-6">
                        <p class="text-2xl md:text-4xl font-black text-[#1dd182] uppercase drop-shadow-[0_0_10px_rgba(29,209,130,0.5)]">Pure Work Time Logged</p>
                    </div>
                </div>
                
                <p class="text-[10px] text-slate-500 italic text-left leading-relaxed relative z-10">* The computed Pure Productive Hour metric is displayed live on Admin command matrices and can be integrated into HR salary logs automatically.</p>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 5: SYSTEM OPERATIONAL ARCHITECTURE
        ══════════════════════════════════════════════════════════ --}}
        <section id="architecture" class="py-24 px-6 max-w-7xl mx-auto border-t border-gold/10 gsap-fade-up">
            <div class="text-center mb-16">
                <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-2">Access & Host Routing Structure</p>
                <h2 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight mb-4">System Operational Architecture</h2>
                <p class="text-xs text-slate-400 max-w-2xl mx-auto">Review how data custody is strictly segregated between client operations and underlying server infrastructures to ensure compliance.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                <div class="bg-navy-light border border-gold/20 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:-translate-y-2 transition-transform">
                    <div class="w-1.5 h-full bg-[#1dd182] absolute left-0 top-0"></div>
                    <h4 class="text-white font-black text-lg mb-2 uppercase tracking-widest">The Client's Domain <span class="text-[10px] text-[#1dd182] ml-2 tracking-normal">(Allowed Access)</span></h4>
                    <p class="text-xs text-slate-400 mb-6 leading-relaxed">Full administrative override of the client dashboard parameters. Authorized staff have complete access to:</p>
                    <ul class="text-[11px] text-slate-300 space-y-4 font-mono">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-[#1dd182] bg-[#1dd182]/10 p-1.5 rounded"></i> Admin command center panels & employee credentials.</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-[#1dd182] bg-[#1dd182]/10 p-1.5 rounded"></i> Company reports and application-level configuration.</li>
                    </ul>
                </div>
                
                <div class="bg-navy-light border border-gold/20 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:-translate-y-2 transition-transform">
                    <div class="w-1.5 h-full bg-gold absolute left-0 top-0"></div>
                    <h4 class="text-white font-black text-lg mb-2 uppercase tracking-widest">VWEB.DEV's Domain <span class="text-[10px] text-gold ml-2 tracking-normal">(Protected Environment)</span></h4>
                    <p class="text-xs text-slate-400 mb-6 leading-relaxed">We manage and protect underlying software integrity on our hosting spaces. Protected elements include:</p>
                    <ul class="text-[11px] text-slate-300 space-y-4 font-mono">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-lock text-gold bg-gold/10 p-1.5 rounded"></i> System-level & Database access.</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-lock text-gold bg-gold/10 p-1.5 rounded"></i> Source code & Deployment files.</li>
                    </ul>
                </div>
            </div>

            <div class="text-center mb-12 border-t border-gold/10 pt-16">
                <p class="text-[10px] text-gold uppercase tracking-[0.3em] font-black mb-2">Terms of Agreement</p>
                <h2 class="text-2xl md:text-4xl font-black text-white uppercase tracking-tight mb-4">Operational Logistics & Exclusions</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto mb-10">
                <div class="glass-border p-8 rounded-2xl bg-navy-light">
                    <h4 class="text-white font-black text-sm mb-4 uppercase tracking-widest"><i class="fas fa-file-contract text-gold mr-2 text-lg"></i> Deployment & Payments</h4>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-medium">Monthly subscriptions payable in advance. Setup/onboarding fees payable before deployment. Prices subject to revision if operational costs increase.</p>
                </div>
                <div class="glass-border p-8 rounded-2xl bg-navy-light">
                    <h4 class="text-white font-black text-sm mb-4 uppercase tracking-widest"><i class="fas fa-tools text-gold mr-2 text-lg"></i> Standard Maintenance</h4>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-medium">Routine platform updates and system maintenance are included. Basic technical and user access support with issue resolution based on selected plan tier.</p>
                </div>
                <div class="glass-border p-8 rounded-2xl bg-navy-light border-red-500/30">
                    <h4 class="text-white font-black text-sm mb-4 uppercase tracking-widest"><i class="fas fa-ban text-red-500 mr-2 text-lg"></i> Out of Scope (Exclusions)</h4>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-medium">Custom feature development (unless Enterprise). Third-party tool costs outside the selected package.</p>
                </div>
            </div>
            
            <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-6 max-w-6xl mx-auto flex items-start gap-4">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl mt-1 animate-pulse"></i>
                <p class="text-xs text-slate-300 leading-relaxed"><span class="text-red-400 font-black uppercase tracking-widest">Strict System Restriction Notice:</span> Client standard subscriptions permit internal workspace deployments only. No resale, distribution or commercial sublicensing rights are granted under standard core agreements.</p>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="py-12 text-center border-t border-gold/20 bg-navy-light mt-10">
            <img src="{{ asset('storage/logo/EAMS Logo.png') }}" alt="EAMS Logo" class="brand-logo-footer mx-auto mb-6 opacity-90 hover:opacity-100 transition-opacity">
            <p class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} VWEB.DEV | EAMS Enterprise Platform
            </p>
        </footer>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    <script>
        // GSAP Scroll Animations
        gsap.registerPlugin(ScrollTrigger);
        gsap.from(".bg-animation span", { opacity: 0, duration: 2, stagger: 0.2 });
        gsap.from(".gsap-hero", { y: 30, opacity: 0, duration: 1, stagger: 0.15, ease: "power3.out" });
        gsap.utils.toArray('.gsap-fade-up').forEach(section => {
            gsap.from(section, { scrollTrigger: { trigger: section, start: "top 85%" }, y: 40, opacity: 0, duration: 0.8, ease: "power2.out" });
        });

        // ⭐ EMPLOYEE CLOCK LOGIC
        function startEmployeeClocks() {
            setInterval(() => {
                const now = new Date();
                const clockEls = document.querySelectorAll('#emp-live-clock');
                clockEls.forEach(el => el.innerText = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }));
            }, 1000);
            let timerSeconds = 4485; // 01:14:45
            setInterval(() => {
                timerSeconds++;
                const h = Math.floor(timerSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((timerSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (timerSeconds % 60).toString().padStart(2, '0');
                const timerEls = document.querySelectorAll('#emp-session-timer');
                timerEls.forEach(el => el.innerText = `${h}:${m}:${s}`);
            }, 1000);
        }
        startEmployeeClocks();

        // ⭐ MATRIX TIER FILTER LOGIC
        const matrixTierHints = {
            all: '<i class="fa-solid fa-circle-info text-gold mr-1"></i> Showing <strong class="text-white">all 6 plans</strong> — Starter, Basic, Pro, Advanced, Private-Label, and Enterprise.',
            foundational: '<i class="fa-solid fa-circle-info text-gold mr-1"></i> Showing <strong class="text-white">Starter & Basic</strong> — best for small teams that need essential employee tracking.',
            intelligent: '<i class="fa-solid fa-circle-info text-gold mr-1"></i> Showing <strong class="text-white">Pro & Advanced</strong> — adds smart alerts, OCR scanning, and AI tool monitoring.',
            corporate: '<i class="fa-solid fa-circle-info text-gold mr-1"></i> Showing <strong class="text-white">Private-Label & Enterprise</strong> — your own branding, custom domain, and dedicated support.'
        };

        function filterMatrixTier(tier, btnEl) {
            document.querySelectorAll('.matrix-tab-btn').forEach(btn => {
                btn.className = 'matrix-tab-btn bg-transparent text-slate-400 hover:text-white hover:bg-gold/10 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all';
            });
            btnEl.className = 'matrix-tab-btn active bg-gold text-navy px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(210,166,61,0.4)]';

            const hintEl = document.getElementById('matrix-tier-hint');
            if (hintEl && matrixTierHints[tier]) {
                hintEl.innerHTML = matrixTierHints[tier];
            }

            const groups = ['foundational', 'intelligent', 'corporate'];
            groups.forEach(g => {
                const show = tier === 'all' || tier === g;
                document.querySelectorAll('.matrix-col-' + g).forEach(cell => {
                    cell.style.display = show ? 'table-cell' : 'none';
                });
            });
        }

        // ⭐ TOGGLE ROLE TABS
        function toggleSimulatorRole(role) {
            const btnAdmin = document.getElementById('btn-admin');
            const btnEmp = document.getElementById('btn-employee');
            const panelAdmin = document.getElementById('sim-admin-panel');
            const panelEmp = document.getElementById('sim-emp-panel');

            if(role === 'admin') {
                btnAdmin.className = 'flex items-center gap-2 bg-gold text-navy px-8 py-3.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(210,166,61,0.4)]';
                btnEmp.className = 'flex items-center gap-2 bg-transparent text-[#1dd182] hover:bg-[#1dd182]/10 px-8 py-3.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-all';
                panelEmp.classList.add('hidden');
                panelAdmin.classList.remove('hidden');
                gsap.fromTo(panelAdmin, { opacity: 0, scale: 0.98, y: 20 }, { opacity: 1, scale: 1, y: 0, duration: 0.5 });
            } else {
                btnEmp.className = 'flex items-center gap-2 bg-[#1dd182] text-navy px-8 py-3.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(29,209,130,0.4)]';
                btnAdmin.className = 'flex items-center gap-2 bg-transparent text-gold hover:bg-gold/10 px-8 py-3.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-all';
                panelAdmin.classList.add('hidden');
                panelEmp.classList.remove('hidden');
                gsap.fromTo(panelEmp, { opacity: 0, scale: 0.98, y: 20 }, { opacity: 1, scale: 1, y: 0, duration: 0.5 });
            }
        }

        // ⭐ ADMIN NAVIGATION LOGIC
        function switchAdminView(element, viewId, title, subtitle) {
            document.querySelectorAll('.admin-nav-item').forEach(nav => {
                nav.className = 'admin-nav-item text-slate-400 p-3 text-[11px] font-medium hover:text-white transition-colors cursor-pointer flex items-center gap-3 border-l-2 border-transparent';
            });
            element.className = 'admin-nav-item border-l-2 border-gold bg-gold/10 text-gold rounded-r-lg p-3 text-[11px] font-bold tracking-wide flex items-center gap-3 cursor-pointer shadow-inner transition-colors';
            
            document.getElementById('admin-sim-title').innerText = title;
            document.getElementById('admin-sim-subtitle').innerText = subtitle;
            
            document.querySelectorAll('.admin-sim-view').forEach(view => view.classList.add('hidden'));
            const targetView = document.getElementById(viewId);
            targetView.classList.remove('hidden');
            gsap.fromTo(targetView, { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 0.3 });
        }

        // ⭐ EMPLOYEE NAVIGATION LOGIC
        function switchEmpView(element, viewId, title) {
            document.querySelectorAll('.emp-nav-item').forEach(nav => {
                nav.className = 'emp-nav-item text-slate-400 p-3 text-[11px] font-medium hover:text-white flex items-center gap-3 cursor-pointer transition-colors border-l-2 border-transparent';
                nav.querySelector('i').classList.remove('text-[#1dd182]');
            });
            element.className = 'emp-nav-item border-l-2 border-[#1dd182] bg-[#1dd182]/10 text-white rounded-r-lg p-3 text-[11px] font-bold tracking-wide flex items-center gap-3 shadow-inner cursor-pointer transition-colors';
            element.querySelector('i').classList.add('text-[#1dd182]');

            document.getElementById('emp-sim-title').innerText = title.toUpperCase();
            
            document.querySelectorAll('.emp-sim-view').forEach(view => view.classList.add('hidden'));
            const targetView = document.getElementById(viewId);
            targetView.classList.remove('hidden');
            gsap.fromTo(targetView, { opacity: 0, x: -10 }, { opacity: 1, x: 0, duration: 0.3 });
        }

        // ⭐ TOAST ENGINE
        function showMockupToast(title, message, colorClass, borderClass, iconClass) {
            const toastArea = document.getElementById('mockup-toast-area');
            if(!toastArea) return;
            const toast = document.createElement('div');
            toast.className = `mockup-toast bg-[#05101f]/95 backdrop-blur-md border ${borderClass} p-4 rounded-xl shadow-2xl flex gap-4 items-start w-72 z-50`;
            toast.innerHTML = `<div class="mt-0.5"><i class="${iconClass} text-lg ${colorClass}"></i></div><div><h4 class="text-[10px] font-black uppercase tracking-widest ${colorClass} mb-1">${title}</h4><p class="text-[10px] text-slate-300 font-medium">${message}</p></div>`;
            toastArea.appendChild(toast);
            setTimeout(() => { gsap.to(toast, { x: 100, opacity: 0, duration: 0.3, onComplete: () => toast.remove() }); }, 3000);
        }

        // ⭐ SAAS PLAN SIMULATION LOGIC (New Lock Features)
        let currentSimPlan = 'basic';
        function setSimPlan(plan) {
            currentSimPlan = plan;
            ['basic', 'pro', 'adv'].forEach(p => { document.getElementById(`sim-plan-${p}`).className = 'text-[8px] font-black uppercase px-2.5 py-1.5 rounded text-slate-400 hover:text-white transition-all'; });
            document.getElementById(`sim-plan-${plan}`).className = 'text-[8px] font-black uppercase px-2.5 py-1.5 rounded bg-[#d2a63d] text-navy shadow-sm transition-all';

            const aiLock = document.getElementById('sim-ai-lock');
            const capLock = document.getElementById('sim-capture-lock');
            const capBtn = document.getElementById('sim-capture-btn');

            if(plan === 'basic') {
                aiLock.style.display = 'flex'; capLock.style.display = 'flex'; capBtn.style.display = 'none';
                document.getElementById('sim-ai-count').innerText = '0';
            } else if(plan === 'pro') {
                aiLock.style.display = 'flex'; capLock.style.display = 'none'; capBtn.style.display = 'flex';
                document.getElementById('sim-ai-count').innerText = '0';
            } else if(plan === 'adv') {
                aiLock.style.display = 'none'; capLock.style.display = 'none'; capBtn.style.display = 'flex';
            }
        }

        // ⭐ EVENT INJECTOR LOGIC
        function simulateAdminEvent(type) {
            const statusEl = document.getElementById('sim-status');
            const appEl = document.getElementById('sim-app');
            const distTimeEl = document.getElementById('sim-dist-time');
            const pureTimeEl = document.getElementById('sim-pure-time');
            const distCountEl = document.getElementById('sim-dist-count');
            const aiCountEl = document.getElementById('sim-ai-count');
            const rowEl = document.getElementById('sim-row');

            const boxDist = document.getElementById('box-dist');
            const boxAi = document.getElementById('box-ai');

            if(type === 'ai' && (currentSimPlan === 'basic' || currentSimPlan === 'pro')) {
                showMockupToast('Feature Locked', 'AI Tool Tracking requires the Advanced Plan.', 'text-rose-500', 'border-rose-500/50', 'fa-solid fa-lock');
                return;
            }

            statusEl.className = 'px-3 py-1.5 rounded text-[8px] font-black uppercase tracking-widest shadow-sm transition-all duration-300';
            rowEl.style.backgroundColor = 'transparent';
            boxDist.style.borderColor = 'rgba(239,68,68,0.2)';
            boxAi.style.borderColor = 'rgba(210,166,61,0.2)';

            if(type === 'vscode') {
                statusEl.innerText = 'Productive Session'; statusEl.classList.add('bg-blue-500/10', 'text-blue-400', 'border', 'border-blue-500/30');
                appEl.innerText = 'VS Code (code.exe)'; appEl.classList.remove('text-[#ef4444]', 'text-[#a855f7]'); appEl.classList.add('text-slate-300');
                distTimeEl.innerText = '- 0h 0m 0s Distraction'; pureTimeEl.innerText = '= 7h 0m 14s Pure Work Time';
                distCountEl.innerHTML = `0 <span class="text-xs font-medium text-slate-500">Detected</span>`;
                aiCountEl.innerHTML = `0 <span class="text-xs font-medium text-slate-500">Active</span>`;
                showMockupToast('Workspace Approved', 'Employee shifted to valid development environment.', 'text-blue-400', 'border-blue-500/30', 'fa-solid fa-code');
            } else if(type === 'facebook') {
                statusEl.innerText = 'Distracted'; statusEl.classList.add('bg-red-500/10', 'text-red-400', 'border', 'border-red-500/30');
                rowEl.style.backgroundColor = 'rgba(239,68,68,0.05)'; boxDist.style.borderColor = 'rgba(239,68,68,0.6)';
                appEl.innerText = 'Facebook Window (Chrome)'; appEl.classList.remove('text-slate-300', 'text-[#a855f7]'); appEl.classList.add('text-[#ef4444]');
                distTimeEl.innerText = '- 0h 1m 20s Distraction'; pureTimeEl.innerText = '= 6h 58m 54s Pure Work Time';
                distCountEl.innerHTML = `1 <span class="text-xs font-medium text-[#ef4444] animate-pulse">Detected</span>`;
                aiCountEl.innerHTML = `0 <span class="text-xs font-medium text-slate-500">Active</span>`;
                showMockupToast('Distraction Alert', 'Social media activity detected on John Doe node.', 'text-red-400', 'border-red-500/50', 'fa-brands fa-facebook');
            } else if(type === 'ai') {
                statusEl.innerText = 'AI Tracking Active'; statusEl.classList.add('bg-purple-500/10', 'text-purple-400', 'border', 'border-purple-500/30');
                rowEl.style.backgroundColor = 'rgba(168,85,247,0.05)'; boxAi.style.borderColor = 'rgba(168,85,247,0.6)';
                appEl.innerText = 'ChatGPT (Browser)'; appEl.classList.remove('text-slate-300', 'text-[#ef4444]'); appEl.classList.add('text-[#a855f7]');
                distTimeEl.innerText = '- 0h 0m 0s Distraction'; pureTimeEl.innerText = '= 7h 0m 14s Pure Work Time';
                distCountEl.innerHTML = `0 <span class="text-xs font-medium text-slate-500">Detected</span>`;
                aiCountEl.innerHTML = `1 <span class="text-xs font-medium text-[#a855f7] animate-pulse">Active</span>`;
                showMockupToast('AI Tool Detected', 'Generative AI platform usage logged on network.', 'text-purple-400', 'border-purple-500/50', 'fa-solid fa-robot');
            }
            gsap.fromTo(rowEl, { opacity: 0.5 }, { opacity: 1, duration: 0.5 });
        }

        // ⭐ PRICING CALCULATOR
        const slider = document.getElementById('empSlider');
        let currentBase = 9999; let currentExtraRate = 1000; const BASE_SEATS = 5;
        function updateSlider(change) {
            let val = parseInt(slider.value) + change;
            if(val < 5) val = 5; if(val > 100) val = 100;
            slider.value = val; calculateBundlePricing();
        }
        function calculateBundlePricing() {
            const seats = parseInt(slider.value);
            document.getElementById('sliderValue').innerText = seats + " Seats";
            const extra = Math.max(0, seats - BASE_SEATS);
            const fee = extra * currentExtraRate;
            document.getElementById('calcBasePrice').innerText = 'PKR ' + currentBase.toLocaleString();
            document.getElementById('calcReqSeats').innerText = extra + ' User(s)';
            document.getElementById('calcBundleRate').innerText = 'PKR ' + currentExtraRate.toLocaleString() + ' /mo';
            document.getElementById('calcExtraFee').innerText = 'PKR ' + fee.toLocaleString();
            document.getElementById('calcTotal').innerHTML = 'PKR ' + (currentBase + fee).toLocaleString() + '<span class="text-sm text-slate-500 font-sans tracking-normal block text-right mt-1">/mo</span>';
        }
        document.querySelectorAll('.plan-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.plan-btn').forEach(b => {
                    b.classList.remove('border-gold', 'bg-gold/10', 'text-gold');
                    b.classList.add('border-slate-700', 'text-slate-400');
                });
                const t = e.currentTarget;
                t.classList.remove('border-slate-700', 'text-slate-400');
                t.classList.add('border-gold', 'bg-gold/10', 'text-gold');
                currentBase = parseInt(t.getAttribute('data-base'));
                currentExtraRate = parseInt(t.getAttribute('data-extra'));
                calculateBundlePricing();
            });
        });
        if(slider) { slider.addEventListener('input', calculateBundlePricing); calculateBundlePricing(); }

        // Initialize state
        window.onload = () => { 
            window.scrollTo(0, 0); 
            toggleSimulatorRole('admin'); 
            setSimPlan('basic'); 
        }
    </script>
</body>
</html>