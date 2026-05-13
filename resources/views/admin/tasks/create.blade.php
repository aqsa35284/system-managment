@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-10 bg-[#0B1120] min-h-screen font-sans text-slate-200 flex justify-center items-start">
    <div class="max-w-4xl w-full space-y-8">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end pb-6 border-b border-slate-800 gap-4">
            <div>
                <p class="text-[10px] text-indigo-500 font-black tracking-[0.3em] uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-rocket animate-pulse"></i> Mission Control
                </p>
                <h2 class="text-3xl md:text-4xl font-black text-white uppercase tracking-widest">Deploy Objective</h2>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="text-slate-400 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Board
            </a>
        </div>

        {{-- FORM CARD --}}
        <div class="bg-[#0F172A] rounded-[2rem] shadow-2xl border border-slate-800 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/5 rounded-full blur-3xl translate-x-10 -translate-y-10 pointer-events-none"></div>

            <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-8 md:p-10 relative z-10">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Task Title --}}
                    <div class="md:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Objective Title</label>
                        <div class="relative">
                            <input type="text" name="title" class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-4 pr-12 text-xs font-bold text-white uppercase tracking-wider outline-none focus:border-indigo-500 transition-all shadow-sm" placeholder="E.G. SERVER SYNCHRONIZATION" required>
                            <i class="fas fa-heading absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Project Select --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Parent Project</label>
                        <div class="relative">
                            <select name="project_id" class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-4 pr-12 text-xs font-bold text-slate-300 uppercase tracking-wider outline-none focus:border-indigo-500 appearance-none transition-all shadow-sm cursor-pointer" required>
                                <option value="" disabled selected class="bg-slate-900">SELECT MISSION</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" class="bg-slate-900">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Operator Assign --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-blue-400 mb-2 pl-1">Assign Operator</label>
                        <div class="relative">
                            <select name="user_id" class="w-full bg-[#1E293B] border border-blue-900/50 rounded-xl p-4 pr-12 text-xs font-bold text-blue-100 uppercase tracking-wider outline-none focus:border-blue-500 appearance-none transition-all shadow-sm cursor-pointer" required>
                                <option value="" disabled selected class="bg-slate-900">SELECT NODE</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" class="bg-slate-900">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-user-astronaut absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Priority Level</label>
                        <div class="relative">
                            <select name="priority" class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-4 pr-12 text-xs font-bold text-slate-300 uppercase tracking-wider outline-none focus:border-rose-500 appearance-none transition-all shadow-sm cursor-pointer">
                                <option value="low" class="bg-slate-900">LOW</option>
                                <option value="medium" selected class="bg-slate-900">MEDIUM</option>
                                <option value="high" class="bg-slate-900 text-amber-500">HIGH</option>
                                <option value="urgent" class="bg-slate-900 text-rose-500">URGENT</option>
                            </select>
                            <i class="fas fa-flag absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-rose-400 mb-2 pl-1">Deadline Date</label>
                        <div class="relative">
                            <input type="date" name="due_date" class="w-full bg-[#1E293B] border border-rose-900/30 rounded-xl p-4 pr-12 text-xs font-bold text-rose-100 uppercase tracking-wider outline-none focus:border-rose-500 transition-all shadow-sm cursor-pointer" style="color-scheme: dark;">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Mission Details / Briefing</label>
                        <div class="relative">
                            <textarea name="description" rows="5" class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-5 text-xs font-bold text-slate-300 outline-none focus:border-indigo-500 transition-all shadow-sm resize-none custom-scrollbar" placeholder="Input tactical briefing here..."></textarea>
                            <div class="absolute bottom-4 right-4 text-slate-500 text-[9px] font-black">
                                <span id="char-count">0</span> / 500
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 mt-10">
                    <a href="{{ route('admin.tasks.index') }}" class="flex-1 bg-slate-800 text-slate-400 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-700 transition-all text-center border border-slate-700">
                        Abort
                    </a>
                    <button type="submit" class="flex-[2] bg-indigo-600 text-white py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-500 transition-all shadow-[0_0_20px_rgba(79,70,229,0.4)] flex items-center justify-center gap-2 group">
                        Deploy Task <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #0F172A; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
</style>

<script>
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        if (this.value.length > 500) {
            this.value = this.value.substring(0, 500);
            charCount.textContent = 500;
        }
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Deploying...';
        submitBtn.disabled = true;
    });
</script>
@endsection