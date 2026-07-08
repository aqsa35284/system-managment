<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAMS | Login Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#010717] min-h-screen flex items-center justify-center p-6">
    
    {{-- Main Login Card --}}
    <div class="w-full max-w-md bg-[#030712] border border-[#d2a63d]/30 rounded-[3.5rem] p-12 shadow-[0_0_40px_rgba(210,166,61,0.1)] relative overflow-hidden">
        
        {{-- Back to Index Button --}}
        <a href="{{ url('/') }}" class="absolute top-8 left-8 text-[#d2a63d]/50 hover:text-[#d2a63d] transition-all flex items-center gap-2 text-[10px] font-black uppercase tracking-widest italic group z-20">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Index
        </a>

        {{-- Top Right Decorative Glow --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-[#d2a63d] rounded-bl-full opacity-10"></div>
        
        <div class="relative z-10 mt-6">
            <h3 class="text-3xl font-black text-[#eaeef2] uppercase italic tracking-tighter mb-2">
                Login <span class="text-[#d2a63d]">Portal</span>
            </h3>
            <p class="text-[10px] font-bold text-[#eaeef2]/50 uppercase tracking-widest mb-8">
                Verify your identity to proceed
            </p>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Email Field --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-[#d2a63d] ml-4 mb-2 block tracking-widest italic">Email Identity</label>
                    <input type="email" name="email" 
                           class="w-full p-5 bg-[#010717] text-[#eaeef2] border-2 border-[#d2a63d]/20 rounded-2xl font-bold italic focus:border-[#d2a63d] outline-none transition-all placeholder-[#eaeef2]/20" 
                           placeholder="admin@system.com" required>
                </div>
                
                {{-- Password Field --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-[#d2a63d] ml-4 mb-2 block tracking-widest italic">Security Key</label>
                    <input type="password" name="password" 
                           class="w-full p-5 bg-[#010717] text-[#eaeef2] border-2 border-[#d2a63d]/20 rounded-2xl font-bold focus:border-[#d2a63d] outline-none transition-all placeholder-[#eaeef2]/20" 
                           placeholder="••••••••" required>
                </div>
                
                {{-- Submit Button --}}
                <button type="submit" 
                        class="w-full bg-[#d2a63d] text-[#010717] py-6 rounded-[2.5rem] font-black uppercase italic tracking-tighter shadow-[0_10px_30px_rgba(210,166,61,0.2)] hover:bg-[#b8860b] hover:shadow-[0_10px_40px_rgba(210,166,61,0.4)] transition transform hover:-translate-y-1">
                    Establish Connection
                </button>
            </form>
            
            {{-- Error Handling --}}
            @if($errors->any())
                <p class="text-red-500 text-[10px] font-black mt-6 text-center uppercase italic tracking-widest bg-red-500/10 p-2 rounded-xl">
                    {{ $errors->first() }}
                </p>
            @endif
        </div>
    </div>
</body>
</html>