@extends('layouts.employee')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="min-h-screen bg-[#010717] text-[#eaeef2] p-4 lg:p-8 rounded-[2.5rem] border border-[#d2a63d]/10 shadow-2xl relative overflow-hidden">
    
    <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-[#d2a63d]/5 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-[#d2a63d]/3 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10 pb-6 border-b border-[#d2a63d]/10 relative z-10">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#d2a63d] flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-[#d2a63d] rounded-full animate-pulse"></span> Mission Control
            </span>
            <h1 class="text-3xl font-extrabold text-[#eaeef2] tracking-tight uppercase mt-1">Objective Parameters</h1>
        </div>
        <a href="{{ route('employee.tasks.index') }}" class="px-5 py-2.5 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#eaeef2] hover:text-[#010717] hover:bg-[#d2a63d] text-xs font-bold uppercase tracking-widest transition-all flex items-center shadow-lg">
            <i class="fas fa-arrow-left mr-2 text-[#d2a63d]"></i> Back to Tasks List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[#010717] border border-[#d2a63d]/10 rounded-[2rem] p-6 md:p-8 shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #010717 0%, #0a1225 100%);">
                
                <div class="absolute top-0 right-0 p-6">
                    <span class="px-3 py-1 bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/20 rounded-md text-[9px] font-black uppercase tracking-widest">
                        {{ str_replace('_', ' ', $task->status) }}
                    </span>
                </div>

                <div class="mb-8">
                    <h4 class="text-[10px] font-black text-[#eaeef2]/30 uppercase tracking-[0.2em] mb-1">Objective Title</h4>
                    <h2 class="text-2xl font-bold text-[#eaeef2] tracking-wide">{{ $task->title }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-[#010717]/60 rounded-2xl border border-[#d2a63d]/10 mb-8">
                    <div>
                        <h4 class="text-[10px] font-black text-[#eaeef2]/30 uppercase tracking-[0.2em] mb-1">Parent Project</h4>
                        <p class="text-[#d2a63d] text-sm font-bold mt-1 flex items-center gap-2">
                            <i class="fas fa-project-diagram text-xs"></i> {{ $task->project->name ?? 'Standalone Mission' }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-[#eaeef2]/30 uppercase tracking-[0.2em] mb-1">Deadline Date</h4>
                        <p class="text-rose-400 text-sm font-bold mt-1 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-xs"></i> {{ \Carbon\Carbon::parse($task->deadline)->format('m/d/Y') }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-[#eaeef2]/30 uppercase tracking-[0.2em] mb-1">Completion Progress</h4>
                        <p class="text-emerald-400 text-sm font-bold mt-1 flex items-center gap-2">
                            <i class="fas fa-circle-notch text-xs animate-spin" style="animation-duration: 4s;"></i> {{ $task->completion_percentage ?? 0 }}% Finished
                        </p>
                    </div>
                </div>

                <div>
                    <h4 class="text-[10px] font-black text-[#eaeef2]/30 uppercase tracking-[0.2em] mb-3">Tactical Briefing / Description</h4>
                    <div class="text-[#eaeef2]/70 text-sm leading-relaxed bg-[#010717]/80 p-6 rounded-xl border border-[#d2a63d]/10 min-h-[150px]">
                        {{ $task->description ?? 'No tactical parameters provided for this objective.' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="border border-[#d2a63d]/10 rounded-[2rem] p-6 shadow-xl" style="background: linear-gradient(135deg, #010717 0%, #0a1225 100%);">
                <h3 class="text-sm font-black text-[#eaeef2] uppercase tracking-wider border-b border-[#d2a63d]/10 pb-4 mb-5 flex items-center gap-2">
                    <i class="fas fa-shield-halved text-[#d2a63d] text-xs"></i> Node Matrix Intel
                </h3>

                <div class="space-y-4">
                    <div class="bg-[#010717]/80 p-4 rounded-xl border border-[#d2a63d]/10">
                        <h5 class="text-[9px] font-black text-[#eaeef2]/30 uppercase tracking-wider mb-1">Active Blockers Reported</h5>
                        <p class="text-xs font-bold {{ $task->blockers ? 'text-[#d2a63d]' : 'text-[#eaeef2]/40' }}">
                            <i class="fas fa-triangle-exclamation mr-1"></i> {{ $task->blockers ?? 'None Reported' }}
                        </p>
                    </div>

                    <div class="bg-[#010717]/80 p-4 rounded-xl border border-[#d2a63d]/10">
                        <h5 class="text-[9px] font-black text-[#eaeef2]/30 uppercase tracking-wider mb-1">Assigned Unit</h5>
                        <p class="text-xs font-bold text-[#eaeef2]">
                            <i class="fas fa-user-gear mr-1 text-[#d2a63d]"></i> {{ auth()->user()->name }}
                        </p>
                    </div>

                    <div class="bg-[#010717]/80 p-4 rounded-xl border border-[#d2a63d]/10">
                        <h5 class="text-[9px] font-black text-[#eaeef2]/30 uppercase tracking-wider mb-1">Task Sync Timeline</h5>
                        <p class="text-[11px] text-[#eaeef2]/40 font-medium">
                            Created: {{ $task->created_at ? $task->created_at->diffForHumans() : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-[10px] font-black text-[#d2a63d] uppercase tracking-[0.3em] ml-2 flex items-center gap-2">
                    <i class="fas fa-star text-xs"></i> Commander Reviews
                </p>
                
                @if($task->adminFeedbacks && $task->adminFeedbacks->count() > 0)
                    @foreach($task->adminFeedbacks as $review)
                    <div class="bg-[#d2a63d]/5 border border-[#d2a63d]/20 p-5 rounded-2xl shadow-xl flex gap-3">
                        <div class="flex-1">
                            <div class="flex justify-between items-center border-b border-[#d2a63d]/10 pb-2 mb-2">
                                <span class="text-[9px] font-black text-[#d2a63d] bg-[#d2a63d]/10 border border-[#d2a63d]/20 px-2 py-0.5 rounded">Score: {{ $review->rating_score }}/10</span>
                                <span class="text-[8px] font-bold uppercase text-[#eaeef2]/30 italic">{{ $review->status }}</span>
                            </div>
                            <p class="text-xs text-[#eaeef2]/60 font-medium leading-relaxed">{{ $review->review_note }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="bg-[#010717]/80 p-5 rounded-2xl text-center border border-[#d2a63d]/10">
                        <p class="text-[11px] text-[#eaeef2]/30 font-bold uppercase tracking-wider">No evaluations synced.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection