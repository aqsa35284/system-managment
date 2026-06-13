@extends('layouts.admin')

@section('content')

 <link rel="icon" type="image/png" href="{{ asset('storage/logo/Favicon.png') }}">

{{-- Extract Active Timeline Protocol --}}
@php
    $period = request('period', 'today');
@endphp

<div class="min-h-screen bg-[#010717] p-4 sm:p-6 md:p-10 text-[#eaeef2] font-sans selection:bg-[#d2a63d]/30">
    <div class="max-w-[1700px] mx-auto space-y-6 md:space-y-8">

        {{-- ⭐ BACK BUTTON --}}
        <div class="flex justify-start">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] text-[10px] font-black uppercase tracking-widest transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             1. RADAR MONITORING CONTROL HEADER & CORE METRICS
        ═══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end pb-6 border-b border-[#d2a63d]/20 gap-6 w-full">
            <div>
                <p class="text-[10px] text-[#d2a63d] font-black tracking-[0.3em] uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-satellite-dish animate-pulse"></i> Core Telemetry Control
                </p>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-[#eaeef2] uppercase tracking-widest">Fleet Log Intelligence</h1>
                <p class="text-[#eaeef2]/50 text-[10px] mt-1.5 uppercase tracking-[0.2em] font-bold">Comprehensive Session Audit, Distraction Analytics & Shift Integrity</p>
            </div>
            
            {{-- Professional Metrics Hub --}}
            <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                <div class="bg-[#010717] border border-emerald-500/20 rounded-2xl px-5 py-3.5 text-center min-w-[130px] flex-1 sm:flex-none shadow-lg">
                    <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-1 flex justify-center items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span> Live Signals
                    </p>
                    <p class="text-2xl md:text-3xl font-black text-[#eaeef2]">{{ $stats['active_now'] ?? 0 }}</p>
                </div>
                <div class="bg-[#010717] border border-[#d2a63d]/30 rounded-2xl px-5 py-3.5 text-center min-w-[130px] flex-1 sm:flex-none shadow-lg">
                    <p class="text-[8px] font-black text-[#d2a63d] uppercase tracking-widest mb-1">Accumulated Hours</p>
                    <p class="text-2xl md:text-3xl font-black text-[#eaeef2]">{{ $stats['total_hours'] ?? 0 }}<span class="text-xs text-[#eaeef2]/50 ml-0.5">h</span></p>
                </div>
                <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-2xl px-5 py-3.5 text-center min-w-[130px] flex-1 sm:flex-none shadow-lg">
                    <p class="text-[8px] font-black text-[#eaeef2]/50 uppercase tracking-widest mb-1">Database Records</p>
                    <p class="text-2xl md:text-3xl font-black text-[#eaeef2]">{{ $stats['filtered_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             2. SHIFT GLOBAL METRIC BAR (PREMIUM COMPONENT)
        ═══════════════════════════════════════════════════════ --}}
        @php
            $allMorning   = $attendances->sum('morning_seconds');
            $allAfternoon = $attendances->sum('afternoon_seconds');
            $allNight     = $attendances->sum('night_seconds');
            $allTotal     = max($allMorning + $allAfternoon + $allNight, 1);
            $mPct  = round(($allMorning   / $allTotal) * 100);
            $aPct  = round(($allAfternoon / $allTotal) * 100);
            $nPct  = round(($allNight     / $allTotal) * 100);
        @endphp
        
        <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-5 md:p-8 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#d2a63d]/5 rounded-full blur-3xl translate-x-10 -translate-y-10"></div>
            
            <p class="text-[9px] font-black text-[#eaeef2]/50 tracking-[0.3em] uppercase mb-6 flex items-center gap-2 relative z-10">
                <i class="fas fa-chart-pie text-[#d2a63d]"></i> Shift Allocation Analytics ({{ ucfirst($period) }})
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6 relative z-10">
                @foreach([
                    ['fas fa-sun text-emerald-500', 'Morning Block', '05:00–12:00', $allMorning, $mPct, 'text-emerald-400'],
                    ['fas fa-cloud-sun text-amber-500', 'Afternoon Block', '12:00–17:00', $allAfternoon, $aPct, 'text-amber-400'],
                    ['fas fa-moon text-indigo-500', 'Night Cycle', '17:00–05:00', $allNight, $nPct, 'text-indigo-400'],
                ] as [$ico, $name, $range, $secs, $pct, $textColor])
                <div class="bg-[#d2a63d]/5 p-4 rounded-2xl border border-[#d2a63d]/10 flex items-center justify-between shadow-inner">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#010717] flex items-center justify-center border border-[#d2a63d]/20">
                            <i class="{{ $ico }} text-sm"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black {{ $textColor }} uppercase tracking-widest">{{ $name }}</span>
                            <span class="block text-[8px] text-[#eaeef2]/40 font-bold tracking-widest mt-0.5">{{ $range }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs sm:text-sm font-black text-[#eaeef2]">{{ floor($secs/3600) }}h {{ floor(($secs%3600)/60) }}m</span>
                        <span class="block text-[9px] {{ $textColor }} font-black">{{ $pct }}%</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex h-2.5 rounded-full overflow-hidden bg-[#010717] relative z-10 border border-[#d2a63d]/30 p-[2px]">
                <div class="bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $mPct }}%" title="Morning: {{ $mPct }}%"></div>
                <div class="bg-amber-500 rounded-full transition-all duration-1000" style="width: {{ $aPct }}%" title="Afternoon: {{ $aPct }}%"></div>
                <div class="bg-indigo-500 rounded-full transition-all duration-1000" style="width: {{ $nPct }}%" title="Night: {{ $nPct }}%"></div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             3. TIMELINE PILLES & ENGINE FILTERS
        ═══════════════════════════════════════════════════════ --}}
        <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-5 md:p-6 shadow-xl relative z-20">
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="flex flex-col xl:flex-row gap-6 items-center justify-between w-full">
                
                {{-- Quick Change Context Switch --}}
                <div class="w-full xl:w-auto overflow-x-auto custom-scrollbar pb-1">
                    <div class="flex gap-1.5 p-1 bg-[#010717] rounded-xl border border-[#d2a63d]/30 w-max">
                        @foreach(['today' => 'Daily Context', 'weekly' => 'Weekly Sync', 'monthly' => 'Monthly Audit', 'yearly' => 'Yearly Scope'] as $key => $label)
                            <button type="submit" name="period" value="{{ $key }}"
                               class="px-5 py-2 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all
                               {{ $period == $key ? 'bg-[#d2a63d] text-[#010717] shadow-[0_0_10px_rgba(210,166,61,0.3)]' : 'text-[#eaeef2]/50 hover:text-[#eaeef2] hover:bg-[#d2a63d]/10' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-4 w-full xl:w-auto items-stretch sm:items-end">
                    {{-- Calendar Engine --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[8px] text-[#eaeef2]/50 font-black uppercase tracking-widest pl-1">Custom Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="bg-[#010717] border border-[#d2a63d]/30 rounded-xl px-4 text-[10px] text-[#eaeef2] font-black tracking-widest outline-none focus:border-[#d2a63d] transition-all h-[40px] w-full sm:w-44 uppercase" style="color-scheme: dark;">
                    </div>
                    
                    {{-- Operator Matrix Dropdown --}}
                    <div class="flex flex-col gap-1.5 flex-1 sm:flex-none sm:w-64">
                        <label class="text-[8px] text-[#eaeef2]/50 font-black uppercase tracking-widest pl-1">Target Operator</label>
                        <div class="relative h-[40px]">
                            <select name="user_id" onchange="this.form.submit()" class="w-full h-full bg-[#010717] border border-[#d2a63d]/30 rounded-xl pl-4 pr-10 text-[10px] text-[#eaeef2] font-black tracking-widest uppercase outline-none focus:border-[#d2a63d] transition-all cursor-pointer appearance-none">
                                <option value="" class="bg-[#010717] text-[#d2a63d]">All Fleet Nodes</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }} class="bg-[#010717]">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[#eaeef2]/50 text-[9px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Actions Hub --}}
                    <div class="flex gap-2 h-[40px]">
                        <button type="submit" class="flex-1 sm:flex-none bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] font-black uppercase text-[10px] tracking-widest rounded-xl px-6 transition-all shadow-md flex items-center justify-center">
                            <i class="fas fa-search-dollar mr-2 text-[9px]"></i> Query Node
                        </button>
                        @if(request()->hasAny(['date','month','period','user_id']))
                        <a href="{{ route('admin.attendance.index') }}" class="flex-1 sm:flex-none flex items-center justify-center bg-[#010717] hover:bg-red-600 text-[#eaeef2]/50 hover:text-white rounded-xl px-6 transition-all border border-[#d2a63d]/30 text-[10px] font-black uppercase tracking-widest">
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             4. HIGH-DENSITY AUDIT MATRIX (UPGRADED 3 CORE COLUMNS)
        ═══════════════════════════════════════════════════════ --}}
        <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-2xl relative z-10 w-full">
            <div class="overflow-x-auto custom-scrollbar w-full">
                {{-- ⭐ FIXED Layout structure: Set baseline min-width to avoid narrow compressing on smartphones --}}
                <table class="w-full min-w-[1250px] text-left border-collapse">
                    <thead>
                        <tr class="bg-[#d2a63d]/5 border-b border-[#d2a63d]/20">
                            <th class="px-6 py-5 text-[9px] font-black tracking-[0.2em] text-[#eaeef2]/60 uppercase">Operator Base</th>
                            <th class="px-4 py-5 text-[9px] font-black tracking-[0.2em] text-[#eaeef2]/60 uppercase text-center">Timeline</th>
                            <th class="px-4 py-5 text-[9px] font-black tracking-[0.2em] text-[#d2a63d] uppercase text-center border-l border-[#d2a63d]/20">First Entry</th>
                            <th class="px-4 py-5 text-[9px] font-black tracking-[0.2em] text-rose-400 uppercase text-center">Last Exit</th>
                            
                            {{-- ⭐ UPGRADED: 3 SEPARATE INDEPENDENT WORK TIME COLUMNS FOR MAXIMUM CLARITY --}}
                            <th class="px-5 py-5 text-[9px] font-black tracking-[0.2em] text-[#eaeef2]/80 uppercase text-center bg-[#010717]/50 border-l border-[#d2a63d]/20">Total Session</th>
                            <th class="px-5 py-5 text-[9px] font-black tracking-[0.2em] text-rose-400 uppercase text-center bg-rose-950/10 border-l border-[#d2a63d]/10">Tracked Deductions</th>
                            <th class="px-6 py-5 text-[10px] font-black tracking-[0.2em] text-emerald-400 uppercase text-center bg-emerald-950/10 border-r border-l border-[#d2a63d]/10">Pure Work Hours</th>
                            
                            <th class="px-6 py-5 text-[9px] font-black tracking-[0.2em] text-[#eaeef2]/60 uppercase text-right">Node Vector</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2a63d]/10 text-xs font-medium">
                        @forelse($attendances as $att)
                        @php
                            // Raw Time Mathematical Processing
                            $totalSecs = $att->total_work_seconds;
                            $wastedSecs = $att->total_distraction_seconds ?? 0;
                            $pureSecs = max(0, $totalWorkSeconds = $totalSecs - $wastedSecs);

                            // 1. Session Variables
                            $sfh = floor($totalSecs / 3600); $sfm = floor(($totalSecs % 3600) / 60);
                            
                            // 2. Distraction Variables
                            $dfh = floor($wastedSecs / 3600); $dfm = floor(($wastedSecs % 3600) / 60);

                            // 3. Pure Active Variables
                            $pfh = floor($pureSecs / 3600); $pfm = floor(($pureSecs % 3600) / 60);
                            
                            $isPresent = strtolower($att->current_integrity ?? '') === 'present';
                        @endphp
                        <tr class="hover:bg-[#d2a63d]/5 transition-all duration-200 group">
                            
                            {{-- Identity Vector --}}
                            <td class="px-6 py-4.5">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-9 h-9 rounded-xl bg-[#010717] border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] font-black shrink-0 relative shadow-inner">
                                        @if($att->user && $att->user->profile_image)
                                            <img src="{{ asset('storage/'.$att->user->profile_image) }}" class="w-full h-full object-cover rounded-xl">
                                        @else
                                            {{ substr($att->user->name ?? 'U', 0, 1) }}
                                        @endif
                                        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border border-[#010717] {{ $isPresent ? 'bg-emerald-500 animate-pulse' : 'bg-[#eaeef2]/30' }}"></div>
                                    </div>
                                    <div class="truncate">
                                        <p class="font-black uppercase text-[11px] text-[#eaeef2] group-hover:text-[#d2a63d] transition-colors truncate">{{ $att->user->name ?? 'Unknown Node' }}</p>
                                        <p class="text-[8px] text-[#eaeef2]/50 font-bold uppercase tracking-wider mt-0.5 truncate">{{ $att->user->department->name ?? 'General Fleet' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Timestamp Calendar --}}
                            <td class="px-4 py-4.5 text-center">
                                <p class="font-black text-[#eaeef2]/80 text-[10px] tracking-wider uppercase">{{ \Carbon\Carbon::parse($att->date)->format('d M, Y') }}</p>
                                <p class="text-[8px] text-[#eaeef2]/40 uppercase tracking-widest font-black mt-0.5">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</p>
                            </td>

                            {{-- First Access Matrix --}}
                            <td class="px-4 py-4.5 text-center border-l border-[#d2a63d]/10">
                                <span class="bg-[#d2a63d]/10 border border-[#d2a63d]/30 text-[#d2a63d] px-2.5 py-1 rounded-lg text-[9px] font-black font-mono tracking-wider shadow-sm">
                                    {{ $att->first_entry ? \Carbon\Carbon::parse($att->first_entry)->format('H:i:s') : '—' }}
                                </span>
                            </td>

                            {{-- Final Broadcast Matrix --}}
                            <td class="px-4 py-4.5 text-center">
                                @if($att->last_exit)
                                    <span class="bg-[#010717] border border-[#d2a63d]/20 text-[#eaeef2]/60 px-2.5 py-1 rounded-lg text-[9px] font-black font-mono tracking-wider">
                                        {{ \Carbon\Carbon::parse($att->last_exit)->format('H:i:s') }}
                                    </span>
                                @else
                                    <span class="bg-amber-500/10 border border-amber-500/30 text-amber-400 px-2.5 py-1 rounded-lg text-[8px] font-black tracking-widest animate-pulse">
                                        ● CONNECTED
                                    </span>
                                @endif
                            </td>

                            {{-- 🛑 COLUMN A: TOTAL TIMER SESSION TIME --}}
                            <td class="px-5 py-4.5 text-center bg-[#d2a63d]/[0.02] border-l border-[#d2a63d]/10">
                                <span class="font-bold text-[#eaeef2]/70 font-mono text-[11px]">{{ $sfh }}h {{ $sfm }}m</span>
                                <p class="text-[6px] font-black text-[#eaeef2]/40 uppercase tracking-widest mt-1">RAW CLOCK RUN</p>
                            </td>

                            {{-- 🛑 COLUMN B: DISTRACTION TIME DEDUCTED --}}
                            <td class="px-5 py-4.5 text-center bg-rose-950/[0.04]">
                                @if($wastedSecs > 0)
                                    <span class="font-black text-rose-400 font-mono text-[11px] bg-rose-500/5 px-2 py-0.5 rounded border border-rose-500/10">
                                        - {{ $dfh }}h {{ $dfm }}m
                                    </span>
                                    <p class="text-[6px] font-black text-rose-500 uppercase tracking-widest mt-1">NON-WORK TRACKED</p>
                                @else
                                    <span class="text-[#eaeef2]/30 font-black text-[10px] tracking-widest">— CLEAN</span>
                                @endif
                            </td>

                            {{-- 🛑 COLUMN C: PURE WORK HOURS (EXACT MINUS SYSTEM VALUE) --}}
                            <td class="px-6 py-4.5 text-center bg-emerald-950/[0.04] border-r border-[#d2a63d]/10">
                                <span class="font-black text-emerald-400 font-mono text-xs shadow-sm bg-emerald-500/5 px-3 py-1 rounded-xl border border-emerald-500/10">
                                    {{ $pfh }}h {{ $pfm }}m
                                </span>
                                
                                {{-- Smart Visual Indicator Bar --}}
                                @php $barPct = min(round(($pureSecs / 28800) * 100), 100); @endphp
                                <div class="w-16 h-1 bg-[#010717] rounded-full mx-auto mt-2 overflow-hidden p-[0.5px] border border-[#d2a63d]/20">
                                    <div class="h-full {{ $barPct >= 85 ? 'bg-emerald-400 shadow-[0_0_8px_#10B981]' : 'bg-[#d2a63d]' }} rounded-full transition-all duration-500" style="width: {{ $barPct }}%"></div>
                                </div>
                            </td>

                            {{-- Signal Integrity Metric --}}
                            <td class="px-6 py-4.5 text-right">
                                <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase tracking-widest border 
                                    {{ $isPresent ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-sm' : 'bg-[#010717] text-[#eaeef2]/50 border-[#d2a63d]/30' }}">
                                    {{ $isPresent ? 'Present' : 'Offline' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-24 text-center bg-[#d2a63d]/5">
                                <i class="fas fa-radar text-4xl text-[#d2a63d]/30 mb-3 block"></i>
                                <p class="text-[#d2a63d]/60 uppercase font-black tracking-[0.2em] text-[9px]">No Log Streams Fetched</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Telemetry Framework Pagination links --}}
            @if($attendances->count() > 0)
            <div class="px-6 py-4 border-t border-[#d2a63d]/20 bg-[#010717]">
                {{ $attendances->appends(request()->query())->links('pagination::tailwind') }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection