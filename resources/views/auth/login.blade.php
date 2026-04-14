<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-[3.5rem] p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full opacity-50"></div>
        
        <div class="relative z-10">
            <h3 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter mb-2">Login <span class="text-blue-600">Portal</span></h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-8">Verify your identity to proceed</p>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4 mb-2 block tracking-widest italic tracking-widest">Email Identity</label>
                    <input type="email" name="email" class="w-full p-5 bg-slate-50 border-2 border-transparent rounded-2xl font-bold italic focus:border-blue-500 outline-none transition-all" placeholder="admin@system.com" required>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4 mb-2 block tracking-widest italic tracking-widest">Security Key</label>
                    <input type="password" name="password" class="w-full p-5 bg-slate-50 border-2 border-transparent rounded-2xl font-bold focus:border-blue-500 outline-none transition-all" placeholder="••••••••" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-6 rounded-[2.5rem] font-black uppercase italic tracking-tighter shadow-2xl shadow-blue-100 hover:bg-blue-700 transition transform hover:-translate-y-1">
                    Establish Connection
                </button>
            </form>
            
            @if($errors->any())
                <p class="text-rose-500 text-[10px] font-black mt-6 text-center uppercase italic tracking-widest">{{ $errors->first() }}</p>
            @endif
        </div>
    </div>
</body>
</html>