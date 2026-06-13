<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAMS | Employee Activity Monitoring System</title>
    
    <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #010717; /* Deepest black/dark */
            color: #eaeef2; /* Off-white text */
        }
        
        /* Gold Gradient matching your logo */
        .text-gradient-gold {
            background: linear-gradient(135deg, #d2a63d 0%, #f4d03f 50%, #b8860b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .glow-bg {
            position: absolute;
            width: 800px;
            height: 600px;
            /* Gold Glow */
            background: radial-gradient(circle, rgba(210,166,61,0.06) 0%, rgba(1,7,23,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="overflow-x-hidden relative min-h-screen flex flex-col">

    <div class="glow-bg"></div>

    {{-- NAVBAR --}}
    <nav class="max-w-7xl w-full mx-auto px-6 py-8 flex justify-between items-center relative z-10">
        
        <a href="/" class="block hover:scale-105 transition-transform duration-300">
            <img src="{{ asset('storage/logo/EAMS Logo.png') }}" 
                 alt="EAMS Logo" 
                 class="h-12 md:h-16 w-auto object-contain drop-shadow-[0_0_15px_rgba(210,166,61,0.2)] rounded-lg">
        </a>
        
        <div class="flex items-center gap-6">
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}" 
                   class="border border-[#d2a63d]/50 bg-[#d2a63d]/10 text-[#eaeef2] hover:bg-[#d2a63d] hover:text-[#010717] px-6 md:px-8 py-3 rounded-full font-black text-[10px] uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(210,166,61,0.15)]">
                    Dashboard <i class="fas fa-arrow-right ml-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="border border-[#d2a63d]/50 bg-transparent text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] px-6 md:px-8 py-3 rounded-full font-black text-[10px] uppercase tracking-widest transition-all shadow-[0_0_20px_rgba(210,166,61,0.1)]">
                    Establish Access <i class="fas fa-lock ml-2"></i>
                </a>
            @endauth
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <main class="flex-1 flex flex-col items-center justify-center text-center px-6 relative z-10 w-full mt-10 md:mt-0">
        
        <div class="border border-[#d2a63d]/30 bg-[#010717] backdrop-blur-md px-6 py-2 rounded-full mb-8 shadow-xl inline-flex items-center gap-3">
            <i class="fas fa-bolt text-[#d2a63d] text-[10px]"></i>
            <span class="text-[9px] font-black uppercase tracking-[0.4em] text-[#eaeef2] italic">
                Intelligent Surveillance Engine
            </span>
        </div>
        
        <h1 class="text-5xl md:text-7xl lg:text-[6rem] font-black italic uppercase tracking-tighter leading-[1] mb-8">
            <span class="text-[#eaeef2]">Employee</span> 
            <span class="text-gradient-gold drop-shadow-[0_0_25px_rgba(210,166,61,0.2)]">Activity</span><br>
            <span class="text-[#eaeef2]">Monitoring</span> 
            <span class="text-[#eaeef2]/60">System</span>
        </h1>

        <p class="max-w-3xl mx-auto text-[#eaeef2]/70 font-medium italic text-xs md:text-sm leading-relaxed mb-16">
            The ultimate EAMS architecture for modern enterprises. Seamlessly track productivity through stealth live screen monitoring, AI distraction detection, and automated operational logs—all from your browser.
        </p>

        {{-- Oval Feature Cards --}}
        <div class="flex flex-col md:flex-row justify-center gap-6 w-full max-w-4xl mx-auto">
            
            <div class="bg-[#030712] border border-slate-800 hover:border-[#d2a63d]/50 rounded-[2rem] px-6 py-8 flex-1 flex flex-col items-center justify-center gap-4 transition-all shadow-2xl group">
                <i class="fas fa-video text-[#d2a63d] text-2xl group-hover:scale-110 transition-transform"></i>
                <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-[#eaeef2]">Live Stealth<br>Tracking</h4>
            </div>
            
            <div class="bg-[#030712] border border-slate-800 hover:border-[#d2a63d]/50 rounded-[2rem] px-6 py-8 flex-1 flex flex-col items-center justify-center gap-4 transition-all shadow-2xl group">
                <i class="fas fa-robot text-[#d2a63d] text-2xl group-hover:scale-110 transition-transform"></i>
                <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-[#eaeef2]">AI Distraction<br>Radar</h4>
            </div>
            
            <div class="bg-[#030712] border border-slate-800 hover:border-[#d2a63d]/50 rounded-[2rem] px-6 py-8 flex-1 flex flex-col items-center justify-center gap-4 transition-all shadow-2xl group">
                <i class="fas fa-database text-[#d2a63d] text-2xl group-hover:scale-110 transition-transform"></i>
                <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-[#eaeef2]">Permanent<br>Telemetry</h4>
            </div>

        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="py-8 text-center relative z-10 mt-auto">
        <p class="text-[9px] font-black text-[#eaeef2]/30 uppercase tracking-[0.4em]">
            &copy; {{ date('Y') }} Vweb EAMS | Structural Management Unit
        </p>
    </footer>

</body>
</html>