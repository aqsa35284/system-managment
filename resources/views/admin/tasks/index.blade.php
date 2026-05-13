@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#0B1120] p-6 md:p-10 text-slate-200 font-sans">
    <div class="max-w-[1600px] mx-auto space-y-8">

        {{-- ═══════════════════════════════════════════════════════
             HEADER SECTION
        ═══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end pb-6 border-b border-slate-800 gap-4">
            <div>
                <p class="text-[10px] text-indigo-500 font-black tracking-[0.3em] uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-layer-group animate-pulse"></i> Project & Task Control
                </p>
                <h1 class="text-4xl font-black text-white uppercase tracking-widest">Task Board</h1>
                <p class="text-slate-400 text-[10px] mt-2 uppercase tracking-[0.2em] font-bold">Monitor Fleet Objectives & Deadlines</p>
            </div>
            <a href="{{ route('admin.tasks.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(99,102,241,0.4)] flex items-center gap-2">
                <i class="fas fa-plus"></i> Deploy New Task
            </a>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             TASK GRID (KANBAN STYLE)
        ═══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($tasks as $task)
            @php
                // Deadline Logic (Check if time is over)
                $isOverdue = false;
                if ($task->due_date && $task->status !== 'completed' && $task->completion_percentage < 100) {
                    $isOverdue = \Carbon\Carbon::parse($task->due_date)->isPast();
                }

                // Forward Logic (Check if employee forwarded the task)
                $isForwarded = $task->original_user_id && $task->original_user_id !== $task->user_id;
                
                // Review Logic (Check if employee marked 100%)
                $isReviewPending = ($task->status == 'review' || $task->completion_percentage == 100);
            @endphp

            <div class="bg-[#0F172A] border {{ $isOverdue ? 'border-rose-500/50 shadow-[0_0_20px_rgba(225,29,72,0.15)]' : 'border-slate-800' }} rounded-3xl p-6 relative overflow-hidden group hover:border-indigo-500/50 transition-all flex flex-col justify-between">
                
                <div class="absolute -right-6 -top-6 text-7xl opacity-[0.03] text-white group-hover:scale-110 transition-transform pointer-events-none">
                    <i class="fas {{ $isReviewPending ? 'fa-check-double' : ($isOverdue ? 'fa-fire' : 'fa-tasks') }}"></i>
                </div>

                <div>
                    {{-- TOP BADGES --}}
                    <div class="flex justify-between items-start mb-5 relative z-10">
                        <div class="flex flex-wrap gap-2">
                            @if($task->status == 'completed')
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 rounded text-[8px] font-black uppercase tracking-widest shadow-sm">COMPLETED</span>
                            @elseif($isReviewPending)
                                <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2.5 py-1 rounded text-[8px] font-black uppercase tracking-widest animate-pulse shadow-sm">REVIEW PENDING</span>
                            @else
                                <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2.5 py-1 rounded text-[8px] font-black uppercase tracking-widest shadow-sm">{{ str_replace('_', ' ', $task->status) }}</span>
                            @endif

                            @if($isOverdue)
                                <span class="bg-rose-500 text-white px-2.5 py-1 rounded text-[8px] font-black uppercase tracking-widest shadow-md animate-bounce"><i class="fas fa-exclamation-triangle mr-1"></i> OVERDUE</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.tasks.edit', $task->id) }}" class="text-slate-500 hover:text-indigo-400 transition-colors"><i class="fas fa-edit text-xs"></i></a>
                        </div>
                    </div>

                    {{-- TASK INFO --}}
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1 relative z-10">{{ $task->project->name ?? 'Standalone Task' }}</p>
                    <h3 class="text-lg font-bold text-white mb-3 group-hover:text-indigo-300 transition-colors relative z-10 line-clamp-2" title="{{ $task->title }}">{{ $task->title }}</h3>

                    {{-- PROGRESS BAR --}}
                    <div class="mb-6 relative z-10">
                        <div class="flex justify-between text-[9px] font-black uppercase tracking-widest mb-2">
                            <span class="text-slate-400">Completion</span>
                            <span class="{{ $isReviewPending ? 'text-emerald-400' : 'text-blue-400' }}">{{ $task->completion_percentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-[#1E293B] h-1.5 rounded-full overflow-hidden border border-slate-700/50">
                            <div class="h-full {{ $isReviewPending ? 'bg-emerald-500' : 'bg-blue-500' }} transition-all duration-1000" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- ASSIGNEE & DEADLINE --}}
                <div class="flex items-center justify-between border-t border-slate-800 pt-4 mt-auto relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#1E293B] border border-slate-700 overflow-hidden flex items-center justify-center font-black text-blue-300 text-xs shadow-sm">
                            @if($task->user && $task->user->profile_image)
                                <img src="{{ asset('storage/'.$task->user->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($task->user->name ?? 'U', 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-white uppercase tracking-wider">{{ $task->user->name ?? 'Unassigned' }}</p>
                            @if($isForwarded)
                                <p class="text-[7px] font-bold text-amber-500 uppercase tracking-widest flex items-center gap-1 mt-0.5"><i class="fas fa-exchange-alt"></i> Forwarded</p>
                            @else
                                <p class="text-[7px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Active Owner</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-[7px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">Deadline</p>
                        <p class="text-[9px] font-black {{ $isOverdue ? 'text-rose-500' : 'text-slate-300' }} uppercase tracking-widest">
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M, Y') }}
                            @else
                                <span class="text-slate-600">No Limit</span>
                            @endif
                        </p>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full py-24 text-center bg-[#0F172A] border border-slate-800 rounded-[2rem] shadow-xl">
                <i class="fas fa-satellite-dish text-5xl text-slate-700 mb-4 block"></i>
                <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-500">No Tasks in Queue</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection