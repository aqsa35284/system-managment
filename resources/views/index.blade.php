<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office OS | Digital Command & Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-950 text-white overflow-x-hidden">

    <nav class="max-w-7xl mx-auto px-8 py-10 flex justify-between items-center relative z-10">
        <h1 class="text-3xl font-black italic tracking-tighter uppercase">
            Admin <span class="text-blue-600">Pro</span>
        </h1>
        <div class="flex items-center gap-8">
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}" 
                   class="bg-blue-600 px-8 py-4 rounded-2xl font-black italic text-xs uppercase shadow-2xl shadow-blue-600/20 hover:-translate-y-1 transition-all">
                    Go to Dashboard <i class="fas fa-th-large ml-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="bg-white text-slate-950 px-8 py-4 rounded-2xl font-black italic text-xs uppercase hover:bg-blue-600 hover:text-white transition-all shadow-xl">
                    Establish Access <i class="fas fa-lock ml-2"></i>
                </a>
            @endauth
        </div>
    </nav>

    <div class="relative min-h-[80vh] flex flex-col items-center justify-center text-center px-6">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] -z-0"></div>

        <div class="relative z-10 space-y-8">
            <div class="inline-block glass border border-white/10 px-6 py-2 rounded-full mb-4">
                <p class="text-[10px] font-black uppercase tracking-[0.5em] text-blue-400 italic">
                    <i class="fas fa-microchip mr-2"></i> AI-Powered Fleet Monitoring
                </p>
            </div>
            
            <h2 class="text-6xl md:text-9xl font-black italic tracking-tighter leading-none uppercase">
                Remote <span class="text-blue-600">Control</span> <br> 
                <span class="text-slate-200">Total</span> Oversight.
            </h2>

            <p class="max-w-2xl mx-auto text-slate-400 font-medium italic text-sm md:text-lg leading-relaxed">
                The ultimate operating system for modern offices. Live screen monitoring, 
                AI tool detection, and automated productivity reports—all from your browser.
            </p>

            <div class="pt-10 flex flex-wrap justify-center gap-6">
                <div class="glass border border-white/5 p-6 rounded-[2.5rem] w-48 text-center">
                    <i class="fas fa-video text-blue-500 text-2xl mb-4"></i>
                    <h4 class="text-[10px] font-black uppercase italic tracking-widest">Live Stream</h4>
                </div>
                <div class="glass border border-white/5 p-6 rounded-[2.5rem] w-48 text-center">
                    <i class="fas fa-brain text-emerald-500 text-2xl mb-4"></i>
                    <h4 class="text-[10px] font-black uppercase italic tracking-widest">AI Detection</h4>
                </div>
                <div class="glass border border-white/5 p-6 rounded-[2.5rem] w-48 text-center">
                    <i class="fas fa-history text-indigo-500 text-2xl mb-4"></i>
                    <h4 class="text-[10px] font-black uppercase italic tracking-widest">10Yr Archives</h4>
                </div>
            </div>
        </div>
    </div>

    <footer class="p-10 text-center relative z-10 border-t border-white/5 mt-20">
        <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.5em] italic">
            &copy; 2026 Admin Pro OS | Structural Management Unit
        </p>
    </footer>

</body>
</html>