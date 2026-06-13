@extends('layouts.admin')

@section('content')
{{-- Favicon Inject --}}
<link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

<div class="p-6 md:p-10 bg-[#010717] min-h-screen font-sans text-[#eaeef2]">
    <div class="max-w-[1600px] mx-auto space-y-8">
        
        {{-- Header Section with Back Button --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-[#d2a63d]/20 gap-4">
            <div class="flex items-center gap-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Back to Dashboard">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-widest">Project <span class="text-[#d2a63d]">Fleet</span></h2>
                    <p class="text-[10px] font-bold text-[#eaeef2]/60 uppercase tracking-widest mt-1 italic">// Active Operational Mission Directory</p>
                </div>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="bg-[#d2a63d] hover:bg-[#b8860b] text-[#010717] px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-[0_0_20px_rgba(210,166,61,0.3)] hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <i class="fas fa-rocket"></i> Launch New Mission
            </a>
        </div>

        {{-- Projects Grid Block --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($projects as $project)
            @php
                $projectDaysWorked = $project->tasks ? $project->tasks->sum('days_worked') : 0;
                $today = \Carbon\Carbon::today('Asia/Karachi');
                $projectOverdueDays = 0;
                
                if ($project->deadline && $project->status !== 'completed') {
                    $deadlineDate = \Carbon\Carbon::parse($project->deadline);
                    if ($today->gt($deadlineDate)) {
                        $projectOverdueDays = (int) $today->diffInDays($deadlineDate);
                    }
                }
            @endphp
            <div class="bg-[#030712] rounded-[2.5rem] p-8 shadow-2xl border border-[#d2a63d]/20 hover:border-[#d2a63d]/60 transition-all flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#d2a63d]/5 rounded-full blur-3xl"></div>

                <div>
                    {{-- Status Badge --}}
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        @php
                            $statusStr = strtoupper($project->status ?? 'PENDING');
                            $statusClass = match($statusStr) {
                                'ACTIVE'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'COMPLETED' => 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/20',
                                'ON_HOLD'   => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                default     => 'bg-slate-800 text-slate-400 border-slate-700',
                            };
                        @endphp
                        <span class="text-[8px] font-black uppercase px-4 py-1 rounded-full tracking-widest border {{ $statusClass }}">
                            {{ $statusStr }}
                        </span>
                        <p class="text-[9px] font-mono font-bold text-[#eaeef2]/50">#PROJ-{{ $project->id }}</p>
                    </div>

                    {{-- Title Area --}}
                    <div class="mb-6 relative z-10">
                        <p class="text-[9px] font-black text-[#d2a63d] uppercase tracking-widest mb-1 truncate">{{ $project->client_name ?? 'Internal Enterprise Core' }}</p>
                        <h3 class="text-xl font-black text-white uppercase tracking-tight group-hover:text-[#d2a63d] transition-colors leading-tight">{{ $project->name }}</h3>
                    </div>

                    {{-- Data Ribbon Indicators --}}
                    <div class="grid grid-cols-2 gap-3 mb-6 bg-[#010717]/80 border border-[#d2a63d]/20 rounded-2xl p-3 shadow-inner">
                        <div>
                            <p class="text-[7px] font-black text-[#eaeef2]/60 uppercase tracking-widest mb-0.5">Uptime Allocation</p>
                            <p class="text-xs font-black font-mono text-[#eaeef2]">{{ $projectDaysWorked }} <span class="text-[9px] text-[#eaeef2]/50">Days</span></p>
                        </div>
                        <div>
                            <p class="text-[7px] font-black text-[#eaeef2]/60 uppercase tracking-widest mb-0.5">Timeline Status</p>
                            @if($projectOverdueDays > 0)
                                <p class="text-xs font-black font-mono text-rose-400 animate-pulse">+{{ $projectOverdueDays }}d <span class="text-[8px] text-rose-500/80 uppercase font-sans">Delay</span></p>
                            @else
                                <p class="text-xs font-black font-mono text-[#d2a63d]">NOMINAL</p>
                            @endif
                        </div>
                    </div>

                    {{-- Team Avatars --}}
                    <div class="flex items-center mb-6 relative z-10">
                        <div class="flex -space-x-2.5 overflow-hidden">
                            @forelse($project->employees ?? [] as $emp)
                                <div class="inline-block h-8 w-8 rounded-xl ring-2 ring-[#010717] bg-[#030712] border border-[#d2a63d]/30 overflow-hidden" title="{{ $emp->name }}">
                                    @if($emp->profile_image)
                                        <img src="{{ asset('storage/'.$emp->profile_image) }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center bg-[#d2a63d]/20 text-[#d2a63d] font-black text-[10px] uppercase">
                                            {{ substr($emp->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <span class="text-[8px] text-[#eaeef2]/50 font-bold uppercase tracking-wider">Unassigned Node</span>
                            @endforelse
                        </div>
                        @if($project->employees && $project->employees->count() > 0)
                            <span class="ml-3 text-[8px] font-black text-[#eaeef2]/60 uppercase tracking-widest">+{{ $project->employees->count() }} Units</span>
                        @endif
                    </div>
                </div>

                {{-- Action Footer --}}
                <div class="pt-5 border-t border-[#d2a63d]/20 relative z-10">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[8px] font-black text-[#eaeef2]/60 uppercase tracking-widest">Calendar Boundary</p>
                            <p class="text-xs font-black font-mono mt-0.5 {{ $projectOverdueDays > 0 ? 'text-rose-400' : 'text-[#eaeef2]' }}">
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M, Y') : 'Ongoing Protocol' }}
                            </p>
                        </div>

                        <div class="flex gap-1.5">
                            {{-- Show Button --}}
                            <a href="{{ route('admin.projects.show', $project->id) }}" 
                               class="w-8 h-8 bg-[#010717] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] rounded-xl transition-all flex items-center justify-center shadow" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>

                            {{-- Edit Button --}}
                            <a href="{{ route('admin.projects.edit', $project->id) }}" 
                               class="w-8 h-8 bg-[#010717] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] rounded-xl transition-all flex items-center justify-center shadow" title="Edit">
                                <i class="fas fa-cog text-xs"></i>
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Abort Mission?')" 
                                        class="w-8 h-8 bg-[#010717] border border-rose-500/30 text-rose-400 hover:bg-rose-500 hover:text-white rounded-xl transition-all flex items-center justify-center shadow" title="Delete">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($projects->isEmpty())
        <div class="bg-[#030712] rounded-[2.5rem] p-20 border border-dashed border-[#d2a63d]/30 flex flex-col items-center justify-center text-center shadow-inner">
            <h3 class="text-sm font-black text-[#eaeef2]/60 uppercase tracking-widest">No Active Missions</h3>
        </div>
        @endif
    </div>
</div>
@endsection