@extends('layouts.admin')

@section('content')
 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="p-6 md:p-10 bg-[#010717] min-h-screen font-sans text-[#eaeef2] flex justify-center items-start">
    <div class="max-w-4xl w-full space-y-8">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end pb-6 border-b border-[#d2a63d]/20 gap-4">
            <div>
                <p class="text-[10px] text-[#d2a63d] font-black tracking-[0.3em] uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-rocket animate-pulse"></i> Mission Control
                </p>
                <h2 class="text-3xl md:text-4xl font-black text-white uppercase tracking-widest">Deploy Objective</h2>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Back to Board">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
        </div>

        {{-- FORM CARD --}}
        <div class="bg-[#030712] rounded-[2rem] shadow-2xl border border-[#d2a63d]/30 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#d2a63d]/5 rounded-full blur-3xl translate-x-10 -translate-y-10 pointer-events-none"></div>

            <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-8 md:p-10 relative z-10">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Task Title --}}
                    <div class="md:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-widest text-[#eaeef2]/60 mb-2 pl-1">Objective Title</label>
                        <div class="relative">
                            <input type="text" name="title" class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-xl p-4 pr-12 text-xs font-bold text-[#eaeef2] uppercase tracking-wider outline-none focus:border-[#d2a63d] transition-all shadow-sm" placeholder="E.G. SERVER SYNCHRONIZATION" required>
                            <i class="fas fa-heading absolute right-4 top-1/2 -translate-y-1/2 text-[#d2a63d]/50 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Project Select --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-[#eaeef2]/60 mb-2 pl-1">Parent Project</label>
                        <div class="relative">
                            <select name="project_id" class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-xl p-4 pr-12 text-xs font-bold text-[#eaeef2] uppercase tracking-wider outline-none focus:border-[#d2a63d] appearance-none transition-all shadow-sm cursor-pointer" required>
                                <option value="" disabled selected class="bg-[#010717]">SELECT MISSION</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" class="bg-[#010717]">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[#d2a63d]/50 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Operator Assign --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-[#d2a63d] mb-2 pl-1">Assign Operator</label>
                        <div class="relative">
                            <select name="user_id" class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-xl p-4 pr-12 text-xs font-bold text-[#eaeef2] uppercase tracking-wider outline-none focus:border-[#d2a63d] appearance-none transition-all shadow-sm cursor-pointer" required>
                                <option value="" disabled selected class="bg-[#010717]">SELECT NODE</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" class="bg-[#010717]">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-user-astronaut absolute right-4 top-1/2 -translate-y-1/2 text-[#d2a63d] text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-[#eaeef2]/60 mb-2 pl-1">Priority Level</label>
                        <div class="relative">
                            <select name="priority" class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-xl p-4 pr-12 text-xs font-bold text-[#eaeef2] uppercase tracking-wider outline-none focus:border-[#d2a63d] appearance-none transition-all shadow-sm cursor-pointer">
                                <option value="low" class="bg-[#010717]">LOW</option>
                                <option value="medium" selected class="bg-[#010717]">MEDIUM</option>
                                <option value="high" class="bg-[#010717] text-amber-500">HIGH</option>
                                <option value="urgent" class="bg-[#010717] text-rose-500">URGENT</option>
                            </select>
                            <i class="fas fa-flag absolute right-4 top-1/2 -translate-y-1/2 text-[#d2a63d]/50 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-widest text-rose-400 mb-2 pl-1">Deadline Date</label>
                        <div class="relative">
                            <input type="date" name="due_date" class="w-full bg-[#010717] border border-rose-900/40 rounded-xl p-4 pr-12 text-xs font-bold text-[#eaeef2] uppercase tracking-wider outline-none focus:border-rose-500 transition-all shadow-sm cursor-pointer" style="color-scheme: dark;">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-widest text-[#eaeef2]/60 mb-2 pl-1">Mission Details / Briefing</label>
                        <div class="relative">
                            <textarea name="description" rows="5" class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-xl p-5 text-xs font-bold text-[#eaeef2] outline-none focus:border-[#d2a63d] transition-all shadow-sm resize-none custom-scrollbar" placeholder="Input tactical briefing here..."></textarea>
                            <div class="absolute bottom-4 right-4 text-[#eaeef2]/40 text-[9px] font-black">
                                <span id="char-count">0</span> / 500
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 mt-10">
                    <a href="{{ route('admin.tasks.index') }}" class="flex-1 bg-[#010717] text-[#eaeef2]/60 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#d2a63d]/10 transition-all text-center border border-[#d2a63d]/20">
                        Abort
                    </a>
                    <button type="submit" class="flex-[2] bg-[#d2a63d] text-[#010717] py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#b8860b] transition-all shadow-[0_0_20px_rgba(210,166,61,0.3)] flex items-center justify-center gap-2 group">
                        Deploy Task <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #010717; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(210,166,61,0.4); border-radius: 10px; }
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.7; cursor: pointer; }
    select option { background-color: #010717 !important; color: #eaeef2 !important; }
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