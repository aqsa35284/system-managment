@extends('layouts.employee')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="space-y-8">
    <div class="bg-[#010717] rounded-[2.5rem] shadow-xl border border-[#d2a63d]/20 p-8 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-[#eaeef2] uppercase tracking-tight">My Objectives</h2>
            <p class="text-xs font-bold text-[#d2a63d]/60 uppercase tracking-widest mt-1">Pending & Completed Fleet Tasks</p>
        </div>
        <div class="w-12 h-12 bg-[#d2a63d] rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-tasks text-[#010717]"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tasks as $task)
            <div class="bg-[#010717] rounded-[2rem] shadow-lg border border-[#d2a63d]/20 overflow-hidden hover:shadow-2xl hover:border-[#d2a63d]/50 transition-all group">
                <div class="p-6 bg-[#010717]/80 border-b border-[#d2a63d]/10 flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full
                        {{ $task->status === 'completed' ? 'bg-emerald-900/40 text-emerald-400 border border-emerald-500/20' : 'bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30' }}">
                        {{ str_replace('_', ' ', $task->status) }}
                    </span>
                    <span class="text-[10px] font-bold text-[#eaeef2]/40">
                        <i class="fas fa-clock mr-1 text-rose-400"></i> {{ \Carbon\Carbon::parse($task->deadline)->format('d M') }}
                    </span>
                </div>

                <div class="p-6 space-y-4">
                    <h3 class="text-lg font-black text-[#eaeef2] leading-tight group-hover:text-[#d2a63d] transition-colors">{{ $task->title }}</h3>
                    
                    <p class="text-[10px] font-black text-[#d2a63d]/70 uppercase tracking-widest flex items-center">
                        <i class="fas fa-project-diagram mr-2"></i> {{ $task->project->name ?? 'Standalone Task' }}
                    </p>
                    
                    <p class="text-xs text-[#eaeef2]/50 font-medium line-clamp-2 leading-relaxed">{{ $task->description }}</p>

                    <a href="{{ route('employee.tasks.show', $task->id) }}" class="mt-4 block w-full bg-[#d2a63d]/10 hover:bg-[#d2a63d] text-[#d2a63d] hover:text-[#010717] py-3 rounded-xl font-black uppercase text-[10px] tracking-[0.2em] transition-all text-center border border-[#d2a63d]/30 hover:border-[#d2a63d]">
                        View Objective
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center bg-[#010717] rounded-[2.5rem] shadow-xl border border-[#d2a63d]/20 py-16">
                <div class="w-20 h-20 bg-[#d2a63d]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-double text-3xl text-[#d2a63d]"></i>
                </div>
                <h3 class="text-xl font-black text-[#eaeef2] uppercase tracking-tight">All Caught Up!</h3>
                <p class="text-sm font-bold text-[#eaeef2]/40 mt-2">No pending objectives currently assigned to you.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection