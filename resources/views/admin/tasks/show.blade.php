@extends('layouts.admin') {{-- Apni layout file ka naam yahan likhein --}}

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="min-h-screen bg-[#010717] py-8 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Header & Back Button --}}
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-black uppercase tracking-widest text-[#d2a63d]">Task Command Center</h1>
            <a href="{{ route('admin.tasks.index') }}" class="px-6 py-2 bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_10px_rgba(210,166,61,0.1)]">
                &larr; Return to Base
            </a>
        </div>

        {{-- Success Message Alert (Jab review submit hoga) --}}
        @if(session('success'))
        <div class="bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-2xl p-4 flex items-center">
            <span class="text-[#d2a63d] text-xs font-bold uppercase tracking-wider">{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Mission / Task Details --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-[#030712] rounded-[2.5rem] shadow-2xl border border-[#d2a63d]/20 overflow-hidden p-8">
                    <h2 class="text-xs font-black uppercase text-[#eaeef2]/50 tracking-[0.2em] mb-4">Mission Details</h2>
                    <h3 class="text-xl font-bold text-[#eaeef2] mb-2">{{ $task->title }}</h3>
                    <p class="text-sm text-[#eaeef2]/70 mb-8">{{ $task->description }}</p>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-[#d2a63d]/10 pb-3">
                            <span class="text-[10px] uppercase font-bold text-[#eaeef2]/50">Assigned To</span>
                            <span class="text-xs font-bold text-[#d2a63d]">{{ $task->user->name ?? 'Unknown Agent' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-[#d2a63d]/10 pb-3">
                            <span class="text-[10px] uppercase font-bold text-[#eaeef2]/50">Deadline</span>
                            <span class="text-xs font-bold text-[#eaeef2]">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M, Y') : 'No Deadline' }}</span>
                        </div>
                        <div class="flex justify-between pt-2 items-center">
                            <span class="text-[10px] uppercase font-bold text-[#eaeef2]/50">Status</span>
                            <span class="px-3 py-1 bg-[#d2a63d]/20 rounded-full text-[8px] font-black text-[#d2a63d] uppercase tracking-wider">{{ $task->status ?? 'Pending' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Technical Review Terminal & Logs (Aapka Code) --}}
            <div class="lg:col-span-2">
                <div class="space-y-8">
                    
                    {{-- Feedback Form Container --}}
                    <div class="bg-[#030712] rounded-[2.5rem] shadow-2xl border border-[#d2a63d]/30 overflow-hidden">
                        <div class="p-6 bg-[#d2a63d]/10 border-b border-[#d2a63d]/30 flex justify-between items-center">
                            <h3 class="text-xs font-black uppercase italic tracking-widest text-[#d2a63d]">Technical Review Terminal</h3>
                            <span class="px-3 py-1 bg-[#d2a63d]/20 rounded-full text-[8px] font-black text-[#d2a63d] uppercase italic border border-[#d2a63d]/30">Commander Mode</span>
                        </div>

                        <form action="{{ route('admin.feedback.store') }}" method="POST" class="p-8 space-y-6">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $task->user_id }}">
                            <input type="hidden" name="task_id" value="{{ $task->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 ml-2 mb-2 block">Performance Score (1-10)</label>
                                    <input type="number" name="rating_score" min="1" max="10" required 
                                           class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-2xl px-6 py-4 text-xs font-bold focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 ml-2 mb-2 block">Efficiency Status</label>
                                    <select name="status" class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-2xl px-6 py-4 text-xs font-bold focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                                        <option value="excellent" class="bg-[#010717]">Excellent Execution</option>
                                        <option value="good" class="bg-[#010717]">Standard Met</option>
                                        <option value="needs_improvement" class="bg-[#010717]">Needs Optimization</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 ml-2 mb-2 block">Technical Review Note</label>
                                <textarea name="review_note" rows="3" required
                                          class="w-full bg-[#010717] border border-[#d2a63d]/30 rounded-2xl px-6 py-4 text-xs font-bold focus:border-[#d2a63d] outline-none text-[#eaeef2]"
                                          placeholder="Log technical analysis..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#d2a63d] hover:bg-[#b8860b] text-[#010717] py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all shadow-[0_0_15px_rgba(210,166,61,0.3)] active:scale-95">
                                Transmit Official Review
                            </button>
                        </form>
                    </div>

                    {{-- Employee Updates Logs Container --}}
                    <div class="space-y-4">
                        <p class="text-[10px] font-black text-[#eaeef2]/50 uppercase tracking-[0.3em] ml-6">Operational Log (Employee Updates)</p>
                        
                        @forelse($task->employeeUpdates as $update)
                        <div class="bg-[#030712] p-6 rounded-[2rem] border border-[#d2a63d]/20 shadow-sm flex gap-4 transition-all hover:border-[#d2a63d]/40">
                            <div class="w-10 h-10 rounded-xl bg-[#d2a63d]/10 border border-[#d2a63d]/30 flex items-center justify-center font-black text-[#d2a63d] text-xs italic">
                                {{ substr($update->user->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-xs font-black uppercase italic text-[#eaeef2]">{{ $update->user->name ?? 'Agent' }}</h4>
                                    <span class="text-[8px] font-black text-[#d2a63d] bg-[#d2a63d]/10 border border-[#d2a63d]/20 px-2 py-1 rounded-full">{{ $update->completion_percentage }}% COMPLETE</span>
                                </div>
                                <p class="text-xs text-[#eaeef2]/60 mt-2 font-medium">{{ $update->update_note }}</p>
                                @if($update->blockers)
                                <div class="mt-3 bg-red-900/20 border border-red-500/20 p-3 rounded-xl text-[10px] font-bold text-red-400 uppercase italic">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Blocker: {{ $update->blockers }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="bg-[#030712] p-6 rounded-[2rem] border border-[#d2a63d]/10 flex justify-center items-center">
                            <p class="text-xs text-[#eaeef2]/40 font-bold uppercase tracking-wider">No Operational Logs Found</p>
                        </div>
                        @endforelse
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    select option { background-color: #010717 !important; color: #eaeef2 !important; }
</style>
@endsection