@extends('layouts.employee')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="min-h-screen bg-[#010717] py-8 px-4 sm:px-6 lg:px-8 rounded-2xl border border-[#d2a63d]/10 shadow-2xl relative overflow-hidden">

    <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-[#d2a63d]/5 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-[#d2a63d]/3 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-6xl mx-auto relative z-10">
        <div class="flex justify-between items-center mb-8">
            <div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#d2a63d] flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 bg-[#d2a63d] rounded-full animate-pulse"></span> Mission Control
                </span>
                <h1 class="text-4xl font-bold text-[#eaeef2] mb-2">Mission Details</h1>
                <p class="text-[#eaeef2]/40">View your assigned project and pending objectives</p>
            </div>
            <a href="{{ route('employee.projects.index') }}" class="px-5 py-2.5 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#eaeef2] hover:text-[#010717] hover:bg-[#d2a63d] font-bold uppercase text-xs tracking-widest transition-all shadow-lg flex items-center">
                <i class="fas fa-arrow-left mr-2 text-[#d2a63d]"></i> Back to Fleet
            </a>
        </div>

        <div class="shadow-xl rounded-2xl overflow-hidden mb-8 border border-[#d2a63d]/20" style="background: linear-gradient(135deg, #010717 0%, #0a1225 100%);">
            <div class="px-8 py-6 flex justify-between items-center border-b border-[#d2a63d]/20" style="background: linear-gradient(135deg, #0a1225 0%, #010717 100%);">
                <h2 class="text-2xl font-bold text-[#eaeef2] flex items-center">
                    <i class="fas fa-project-diagram mr-3 text-[#d2a63d]"></i>
                    {{ $project->name }}
                </h2>
                <span class="px-3 py-1 bg-[#d2a63d]/10 text-[#d2a63d] rounded-full text-xs font-bold uppercase tracking-wider border border-[#d2a63d]/30">
                    {{ str_replace('_', ' ', $project->status ?? 'ACTIVE') }}
                </span>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 pb-6 border-b border-[#d2a63d]/10">
                    <div>
                        <p class="text-xs font-bold text-[#d2a63d]/60 uppercase tracking-wider mb-1">Client / Target</p>
                        <p class="text-[#eaeef2] font-semibold">{{ $project->client_name ?? 'Internal Operation' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#d2a63d]/60 uppercase tracking-wider mb-1">Start Date</p>
                        <p class="text-[#eaeef2] font-semibold">{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#d2a63d]/60 uppercase tracking-wider mb-1">Project Deadline</p>
                        <p class="text-rose-400 font-semibold">
                            <i class="fas fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-[#d2a63d]/60 uppercase tracking-wider mb-3">Mission Brief</h3>
                    <p class="text-[#eaeef2]/70 leading-relaxed bg-[#010717]/60 p-5 rounded-xl border border-[#d2a63d]/10">
                        {{ $project->description ?? 'No specific details provided for this mission.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="shadow-xl rounded-2xl overflow-hidden p-8 border border-[#d2a63d]/20" style="background: linear-gradient(135deg, #010717 0%, #0a1225 100%);">
            <h3 class="text-xl font-bold text-[#eaeef2] mb-6 flex items-center pb-4 border-b border-[#d2a63d]/10">
                <i class="fas fa-tasks text-[#d2a63d] mr-3"></i> My Assigned Objectives
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($myTasks as $task)
                    <div class="p-6 border border-[#d2a63d]/10 rounded-xl hover:shadow-lg hover:border-[#d2a63d]/40 transition-all bg-[#010717]/60 flex flex-col group">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-bold text-[#eaeef2] text-lg group-hover:text-[#d2a63d] transition-colors">{{ $task->title }}</h4>
                            <span class="text-[10px] px-2 py-1 rounded font-bold uppercase tracking-wider whitespace-nowrap
                                {{ $task->status === 'completed' ? 'bg-emerald-900/30 text-emerald-400 border border-emerald-500/20' : 'bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30' }}">
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-[#eaeef2]/40 mb-5 flex-grow line-clamp-3">{{ $task->description }}</p>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-[#d2a63d]/10 mt-auto">
                            <div class="text-xs font-semibold text-[#eaeef2]/40">
                                <i class="fas fa-calendar-alt text-[#d2a63d] mr-1"></i> Due: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                            </div>
                            <a href="{{ route('employee.tasks.show', $task->id) }}" class="text-[#d2a63d] text-xs font-bold uppercase tracking-wider hover:text-[#eaeef2] transition-colors flex items-center">
                                Go to Task <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-[#010717]/60 rounded-xl border-2 border-dashed border-[#d2a63d]/20">
                        <i class="fas fa-check-circle text-5xl text-[#d2a63d] mb-4 opacity-50"></i>
                        <p class="text-[#eaeef2] font-medium text-lg">No pending tasks assigned to you in this project.</p>
                        <p class="text-[#eaeef2]/40 text-sm mt-1">You're all caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection