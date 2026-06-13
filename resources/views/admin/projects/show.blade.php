@extends('layouts.admin')

@section('content')
{{-- Favicon Inject --}}
<link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

<div class="p-6 md:p-10 bg-[#010717] min-h-screen font-sans text-[#eaeef2]">
    <div class="max-w-[1600px] mx-auto space-y-8">
        
        {{-- Header Section with Back & Actions --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-[#d2a63d]/20 gap-4">
            <div class="flex items-center gap-5">
                <a href="{{ route('admin.projects.index') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Return to Fleet">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-widest">{{ $project->name }}</h2>
                    <p class="text-[10px] font-bold text-[#eaeef2]/60 uppercase tracking-widest mt-1 italic">
                        // CLIENT: <span class="text-[#d2a63d]">{{ $project->client_name ?? 'Internal Enterprise' }}</span>
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.projects.edit', $project->id) }}" class="bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all flex items-center gap-2">
                    <i class="fas fa-cog"></i> Modify Mission
                </a>
            </div>
        </div>

        {{-- TIME & DATE LOGIC (Calculations) --}}
        @php
            $today = \Carbon\Carbon::today('Asia/Karachi');
            $startDate = \Carbon\Carbon::parse($project->created_at);
            $deadlineDate = $project->deadline ? \Carbon\Carbon::parse($project->deadline) : null;
            
            // 1. Kitne din ho gaye hain? (Elapsed Days)
            $elapsedDays = $startDate->diffInDays($today);
            
            // 2. Total kitne din ka project tha? (Allocated Days)
            $totalAllocatedDays = $deadlineDate ? $startDate->diffInDays($deadlineDate) : 'N/A';
            
            // 3. Deadline se upar kitne din ho gaye? (Overdue Days)
            $overdueDays = 0;
            if ($deadlineDate && $project->status !== 'completed' && $today->gt($deadlineDate)) {
                $overdueDays = $deadlineDate->diffInDays($today);
            }

            // 4. Complete kab hua?
            $completedDate = null;
            if (strtolower($project->status) === 'completed') {
                // Agar aapke pas alag se 'completed_at' column hai toh wo lagayein, werna 'updated_at' use karein
                $completedDate = $project->updated_at ? \Carbon\Carbon::parse($project->updated_at)->format('d M, Y') : 'N/A';
            }
        @endphp

        {{-- Mission Metrics (Top Stats Cards) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Stat 1: Elapsed Days --}}
            <div class="bg-[#030712] p-6 rounded-3xl border border-[#d2a63d]/20 relative overflow-hidden group hover:border-[#d2a63d]/50 transition-all">
                <div class="absolute top-0 right-0 w-24 h-24 bg-[#d2a63d]/5 rounded-full blur-2xl"></div>
                <p class="text-[9px] font-black uppercase text-[#eaeef2]/50 tracking-widest mb-2 z-10 relative"><i class="fas fa-clock mr-1 text-[#d2a63d]"></i> Active Time (Days Passed)</p>
                <h4 class="text-3xl font-mono font-black text-white z-10 relative">{{ $elapsedDays }} <span class="text-xs text-[#eaeef2]/50">DAYS</span></h4>
            </div>

            {{-- Stat 2: Total Allocated Days --}}
            <div class="bg-[#030712] p-6 rounded-3xl border border-[#d2a63d]/20 relative overflow-hidden group hover:border-[#d2a63d]/50 transition-all">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl"></div>
                <p class="text-[9px] font-black uppercase text-[#eaeef2]/50 tracking-widest mb-2 z-10 relative"><i class="fas fa-calendar-alt mr-1 text-blue-400"></i> Total Allocated Duration</p>
                <h4 class="text-3xl font-mono font-black text-white z-10 relative">{{ $totalAllocatedDays }} <span class="text-xs text-[#eaeef2]/50">DAYS</span></h4>
            </div>

            {{-- Stat 3: Overdue Status --}}
            <div class="bg-[#030712] p-6 rounded-3xl border {{ $overdueDays > 0 ? 'border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.1)]' : 'border-[#d2a63d]/20' }} relative overflow-hidden group transition-all">
                @if($overdueDays > 0)
                    <div class="absolute top-0 right-0 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl"></div>
                    <p class="text-[9px] font-black uppercase text-rose-400/80 tracking-widest mb-2 z-10 relative animate-pulse"><i class="fas fa-exclamation-triangle mr-1"></i> Deadline Overflow</p>
                    <h4 class="text-3xl font-mono font-black text-rose-500 z-10 relative">+{{ $overdueDays }} <span class="text-xs text-rose-500/50 uppercase font-sans tracking-widest">Delay</span></h4>
                @else
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl"></div>
                    <p class="text-[9px] font-black uppercase text-[#eaeef2]/50 tracking-widest mb-2 z-10 relative"><i class="fas fa-check-circle mr-1 text-emerald-400"></i> Deadline Overflow</p>
                    <h4 class="text-xl font-black text-emerald-500 z-10 relative mt-2">NOMINAL / ON TRACK</h4>
                @endif
            </div>

            {{-- Stat 4: Completion Status / Date --}}
            <div class="bg-[#030712] p-6 rounded-3xl border border-[#d2a63d]/20 relative overflow-hidden group hover:border-[#d2a63d]/50 transition-all">
                <div class="absolute top-0 right-0 w-24 h-24 bg-[#d2a63d]/5 rounded-full blur-2xl"></div>
                <p class="text-[9px] font-black uppercase text-[#eaeef2]/50 tracking-widest mb-2 z-10 relative"><i class="fas fa-flag-checkered mr-1 text-[#d2a63d]"></i> Completion Protocol</p>
                @if($completedDate)
                    <h4 class="text-lg font-mono font-black text-[#d2a63d] z-10 relative mt-2">{{ $completedDate }}</h4>
                    <span class="inline-block mt-1 bg-[#d2a63d]/20 text-[#d2a63d] text-[8px] px-2 py-0.5 rounded-full uppercase font-black tracking-widest border border-[#d2a63d]/30">Mission Accomplished</span>
                @else
                    <h4 class="text-xl font-black text-[#eaeef2]/40 z-10 relative mt-2 italic">PENDING...</h4>
                @endif
            </div>

        </div>

        {{-- Details & Team Split Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Project Details --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[#030712] rounded-[2.5rem] shadow-2xl border border-[#d2a63d]/20 overflow-hidden p-8">
                    <div class="flex justify-between items-center mb-6 border-b border-[#d2a63d]/10 pb-4">
                        <h3 class="text-xs font-black uppercase text-[#eaeef2]/60 tracking-[0.2em]">Mission Briefing (Description)</h3>
                        
                        {{-- Status Badge --}}
                        @php
                            $statusStr = strtoupper($project->status ?? 'PENDING');
                            $statusClass = match($statusStr) {
                                'ACTIVE'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'COMPLETED' => 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/30',
                                'ON_HOLD'   => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                default     => 'bg-slate-800 text-slate-400 border-slate-700',
                            };
                        @endphp
                        <span class="text-[9px] font-black uppercase px-4 py-1.5 rounded-full tracking-widest border {{ $statusClass }}">
                            {{ $statusStr }}
                        </span>
                    </div>
                    
                    <div class="prose prose-invert max-w-none text-sm text-[#eaeef2]/80 leading-relaxed font-medium">
                        {!! nl2br(e($project->description ?? 'No briefing data available for this mission.')) !!}
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-4 bg-[#010717] p-5 rounded-2xl border border-[#d2a63d]/10">
                        <div>
                            <p class="text-[8px] font-black text-[#eaeef2]/50 uppercase tracking-widest">Initialization Date</p>
                            <p class="text-sm font-mono text-white mt-1">{{ \Carbon\Carbon::parse($project->created_at)->format('d M, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-[#eaeef2]/50 uppercase tracking-widest">Hard Deadline</p>
                            <p class="text-sm font-mono {{ $overdueDays > 0 ? 'text-rose-400' : 'text-white' }} mt-1">
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M, Y') : 'UNLIMITED' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Assigned Team --}}
            <div class="lg:col-span-1">
                <div class="bg-[#030712] rounded-[2.5rem] shadow-2xl border border-[#d2a63d]/20 overflow-hidden p-8 h-full">
                    <h3 class="text-xs font-black uppercase text-[#eaeef2]/60 tracking-[0.2em] mb-6 border-b border-[#d2a63d]/10 pb-4">Assigned Operatives</h3>
                    
                    <div class="space-y-4">
                        @forelse($project->employees ?? [] as $emp)
                            <div class="flex items-center gap-4 bg-[#010717] p-4 rounded-2xl border border-[#d2a63d]/10 hover:border-[#d2a63d]/40 transition-all">
                                <div class="w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 overflow-hidden flex-shrink-0 flex items-center justify-center text-[#d2a63d] font-black uppercase">
                                    @if($emp->profile_image)
                                        <img src="{{ asset('storage/'.$emp->profile_image) }}" alt="avatar" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($emp->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-black uppercase text-white">{{ $emp->name }}</h4>
                                    <p class="text-[9px] font-bold text-[#d2a63d] uppercase tracking-widest mt-0.5">{{ $emp->role ?? 'Agent' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 opacity-50">
                                <i class="fas fa-users-slash text-3xl text-[#eaeef2]/30 mb-3"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest">No Operatives Assigned</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection