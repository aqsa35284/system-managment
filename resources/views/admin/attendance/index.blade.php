@extends('layouts.admin')

@section('content')

{{-- Extract Active Period --}}
@php
    $period = request('period', 'today');
@endphp

<div class="min-h-screen bg-[#0B1120] p-6 md:p-10 text-slate-200 font-sans">
    <div class="max-w-[1600px] mx-auto space-y-8">

        {{-- ═══════════════════════════════════════════════════════
             HEADER & TOP STATS (PREMIUM DARK)
        ═══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end pb-6 border-b border-slate-800 gap-6">
            <div>
                <p class="text-[10px] text-blue-500 font-black tracking-[0.3em] uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-clock animate-pulse"></i> Time & Space Protocol
                </p>
                <h1 class="text-4xl font-black text-white uppercase tracking-widest">Fleet Attendance</h1>
                <p class="text-slate-400 text-[10px] mt-2 uppercase tracking-[0.2em] font-bold">Session Intelligence & Work Hours Archive</p>
            </div>
            
            {{-- Stat Cards --}}
            <div class="flex flex-wrap gap-3">
                <div class="bg-[#0F172A] border border-emerald-500/30 rounded-2xl px-6 py-4 text-center min-w-[120px] shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                    <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-1 flex justify-center items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Active Now</p>
                    <p class="text-3xl font-black text-white">{{ $stats['active_now'] }}</p>
                </div>
                <div class="bg-[#0F172A] border border-indigo-500/30 rounded-2xl px-6 py-4 text-center min-w-[120px] shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                    <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1">Total Hours</p>
                    <p class="text-3xl font-black text-white">{{ $stats['total_hours'] }}<span class="text-sm text-slate-500 ml-1">h</span></p>
                </div>
                <div class="bg-[#0F172A] border border-slate-700/50 rounded-2xl px-6 py-4 text-center min-w-[120px] shadow-lg">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Log Records</p>
                    <p class="text-3xl font-black text-white">{{ $stats['filtered_count'] }}</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             SHIFT DISTRIBUTION BAR (GLOBAL)
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
        
        <div class="bg-[#0F172A] border border-slate-800 rounded-3xl p-8 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 rounded-full blur-3xl translate-x-10 -translate-y-10"></div>
            
            <p class="text-[9px] font-black text-slate-400 tracking-[0.3em] uppercase mb-6 flex items-center gap-2 relative z-10">
                <i class="fas fa-chart-pie text-blue-500"></i> Shift Distribution ({{ ucfirst($period) }})
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 relative z-10">
                @foreach([
                    ['fas fa-sun text-emerald-500', 'Morning Cycle', '05:00–12:00', $allMorning, $mPct, 'text-emerald-400', 'from-emerald-600 to-emerald-400'],
                    ['fas fa-cloud-sun text-amber-500', 'Afternoon Cycle', '12:00–17:00', $allAfternoon, $aPct, 'text-amber-400', 'from-amber-600 to-amber-400'],
                    ['fas fa-moon text-indigo-500', 'Night/Eve Cycle', '17:00–05:00', $allNight, $nPct, 'text-indigo-400', 'from-indigo-600 to-indigo-400'],
                ] as [$ico, $name, $range, $secs, $pct, $textColor, $gradient])
                <div class="bg-[#1E293B] p-4 rounded-2xl border border-slate-700/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center border border-slate-700">
                            <i class="{{ $ico }} text-lg"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black {{ $textColor }} uppercase tracking-widest">{{ $name }}</span>
                            <span class="block text-[8px] text-slate-500 font-bold tracking-widest mt-0.5">{{ $range }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-sm font-black text-white">{{ floor($secs/3600) }}h {{ floor(($secs%3600)/60) }}m</span>
                        <span class="block text-[9px] {{ $textColor }} font-black">{{ $pct }}%</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Stacked Progress Bar --}}
            <div class="flex h-3 rounded-full overflow-hidden gap-1 bg-[#1E293B] relative z-10 border border-slate-700">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-400 transition-all duration-1000" style="width: {{ $mPct }}%" title="Morning: {{ $mPct }}%"></div>
                <div class="bg-gradient-to-r from-amber-600 to-amber-400 transition-all duration-1000" style="width: {{ $aPct }}%" title="Afternoon: {{ $aPct }}%"></div>
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-400 transition-all duration-1000" style="width: {{ $nPct }}%" title="Night: {{ $nPct }}%"></div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             SEARCH & FILTER COMMAND BAR (UPDATED)
        ═══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] border border-slate-800 rounded-[2rem] p-6 shadow-xl relative z-20">
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="flex flex-col xl:flex-row gap-6 items-center justify-between">
                
                {{-- ⭐ NEW: Quick Timeline Pills --}}
                <div class="flex flex-wrap gap-2 w-full xl:w-auto">
                    @foreach(['today' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $key => $label)
                        <button type="submit" name="period" value="{{ $key }}"
                           class="px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all flex-1 md:flex-none text-center
                           {{ $period == $key ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)] border border-blue-500' : 'bg-[#1E293B] text-slate-400 border border-slate-700 hover:text-white hover:bg-slate-800' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-4 w-full xl:w-auto items-end">
                    {{-- Specific Date Filter (Optional) --}}
                    <div class="flex flex-col gap-1.5 flex-1 sm:flex-none">
                        <label class="text-[8px] text-slate-400 font-black uppercase tracking-widest pl-1">Custom Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="bg-[#1E293B] border border-slate-700 rounded-xl px-4 py-2 text-[10px] text-white font-bold tracking-widest outline-none focus:border-blue-500 transition-all shadow-sm uppercase h-[40px]">
                    </div>
                    
                    {{-- Operator Filter --}}
                    <div class="flex flex-col gap-1.5 flex-1 sm:flex-none sm:w-64">
                        <label class="text-[8px] text-slate-400 font-black uppercase tracking-widest pl-1">Select Operator</label>
                        <div class="relative h-[40px]">
                            <select name="user_id" onchange="this.form.submit()" class="w-full h-full bg-[#1E293B] border border-slate-700 rounded-xl pl-4 pr-10 text-[10px] text-white font-black tracking-widest uppercase outline-none focus:border-blue-500 appearance-none transition-all shadow-sm cursor-pointer">
                                <option value="" class="bg-slate-900">All Fleet Operators</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }} class="bg-slate-900">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                        <button type="submit" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-500 text-white font-black uppercase text-[10px] tracking-[0.2em] rounded-xl px-6 h-[40px] transition-all shadow-[0_0_15px_rgba(37,99,235,0.4)] border border-blue-500 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i> Scan
                        </button>
                        @if(request()->hasAny(['date','month','period','user_id']))
                        <a href="{{ route('admin.attendance.index') }}" class="flex-1 sm:flex-none flex items-center justify-center bg-[#1E293B] hover:bg-rose-500 hover:text-white hover:border-rose-500 text-slate-400 font-black uppercase text-[10px] tracking-[0.2em] rounded-xl px-6 h-[40px] transition-all border border-slate-700 shadow-sm">
                            Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             MAIN INTELLIGENCE TABLE
        ═══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] border border-slate-800 rounded-[2rem] overflow-hidden shadow-2xl relative z-10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#1E293B]/50 border-b border-slate-800">
                            <th class="px-8 py-6 text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase">Operator Identity</th>
                            <th class="px-5 py-6 text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase">Date Log</th>
                            <th class="px-5 py-6 text-[9px] font-black tracking-[0.2em] text-blue-400 uppercase"><i class="fas fa-sign-in-alt mr-1"></i> Entry</th>
                            <th class="px-5 py-6 text-[9px] font-black tracking-[0.2em] text-rose-400 uppercase"><i class="fas fa-sign-out-alt mr-1"></i> Exit</th>
                            <th class="px-5 py-6 text-[9px] font-black tracking-[0.2em] text-emerald-400 uppercase text-center border-l border-slate-800/50">Morning</th>
                            <th class="px-5 py-6 text-[9px] font-black tracking-[0.2em] text-amber-400 uppercase text-center border-l border-slate-800/50">Afternoon</th>
                            <th class="px-5 py-6 text-[9px] font-black tracking-[0.2em] text-indigo-400 uppercase text-center border-l border-slate-800/50">Evening</th>
                            <th class="px-6 py-6 text-[9px] font-black tracking-[0.2em] text-white uppercase text-center border-l border-slate-800 bg-blue-600/5">Total Work</th>
                            <th class="px-8 py-6 text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase text-right">Node Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($attendances as $att)
                        @php
                            $fh = floor($att->total_work_seconds / 3600);
                            $fm = floor(($att->total_work_seconds % 3600) / 60);
                            
                            $mH = floor($att->morning_seconds / 3600);   $mM = floor(($att->morning_seconds % 3600) / 60);
                            $aH = floor($att->afternoon_seconds / 3600); $aM = floor(($att->afternoon_seconds % 3600) / 60);
                            $nH = floor($att->night_seconds / 3600);     $nM = floor(($att->night_seconds % 3600) / 60);
                            
                            $isPresent = strtolower($att->current_integrity ?? '') === 'present';
                        @endphp
                        <tr class="hover:bg-[#1E293B]/40 transition-colors group">
                            
                            {{-- Operator --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-900 border border-slate-700 overflow-hidden shadow-sm flex items-center justify-center text-blue-300 font-black">
                                        @if($att->user && $att->user->profile_image)
                                            <img src="{{ asset('storage/'.$att->user->profile_image) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($att->user->name ?? 'U', 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black uppercase tracking-wider text-xs text-white group-hover:text-blue-400 transition-colors">{{ $att->user->name ?? 'Unknown' }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $att->user->department->name ?? 'Fleet Dept' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="px-5 py-5">
                                <p class="font-black text-slate-300 text-[10px] tracking-widest uppercase">{{ \Carbon\Carbon::parse($att->date)->format('d M, Y') }}</p>
                                <p class="text-[8px] text-slate-500 font-bold uppercase mt-1">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</p>
                            </td>

                            {{-- Entry --}}
                            <td class="px-5 py-5">
                                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 px-2 py-1 rounded-md text-[10px] font-black tracking-widest">
                                    {{ $att->first_entry ? \Carbon\Carbon::parse($att->first_entry)->format('H:i:s') : '—' }}
                                </span>
                            </td>

                            {{-- Exit --}}
                            <td class="px-5 py-5">
                                @if($att->last_exit)
                                    <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-2 py-1 rounded-md text-[10px] font-black tracking-widest">
                                        {{ \Carbon\Carbon::parse($att->last_exit)->format('H:i:s') }}
                                    </span>
                                @else
                                    <span class="text-[9px] text-amber-400 font-black animate-pulse flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> LIVE</span>
                                @endif
                            </td>

                            {{-- Morning --}}
                            <td class="px-5 py-5 text-center border-l border-slate-800/50">
                                @if($att->morning_seconds > 0)
                                    <span class="text-[10px] text-emerald-400 font-black bg-emerald-500/5 px-2 py-1 rounded">{{ $mH }}h {{ $mM }}m</span>
                                @else
                                    <span class="text-slate-700 font-black text-[10px]">—</span>
                                @endif
                            </td>

                            {{-- Afternoon --}}
                            <td class="px-5 py-5 text-center border-l border-slate-800/50">
                                @if($att->afternoon_seconds > 0)
                                    <span class="text-[10px] text-amber-400 font-black bg-amber-500/5 px-2 py-1 rounded">{{ $aH }}h {{ $aM }}m</span>
                                @else
                                    <span class="text-slate-700 font-black text-[10px]">—</span>
                                @endif
                            </td>

                            {{-- Night --}}
                            <td class="px-5 py-5 text-center border-l border-slate-800/50">
                                @if($att->night_seconds > 0)
                                    <span class="text-[10px] text-indigo-400 font-black bg-indigo-500/5 px-2 py-1 rounded">{{ $nH }}h {{ $nM }}m</span>
                                @else
                                    <span class="text-slate-700 font-black text-[10px]">—</span>
                                @endif
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-5 bg-blue-600/[0.02] border-l border-slate-800 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-black text-white text-[11px]">{{ $fh }}h {{ $fm }}m</span>
                                    @php $pct = min(round(($att->total_work_seconds / 28800) * 100), 100); @endphp
                                    <div class="w-12 h-1 bg-[#1E293B] rounded-full mt-1.5 overflow-hidden border border-slate-700">
                                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-8 py-5 text-right">
                                <span class="px-3 py-1.5 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] shadow-md border 
                                    {{ $isPresent ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-[#1E293B] text-slate-500 border-slate-700' }}">
                                    {{ $isPresent ? '● Active' : '○ Offline' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-28 text-center bg-[#1E293B]/20">
                                <i class="fas fa-calendar-times text-5xl text-slate-700 mb-4 block"></i>
                                <p class="text-slate-400 uppercase font-black tracking-[0.3em] text-[10px]">No Attendance Records Match Criteria</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-8 py-6 border-t border-slate-800 bg-[#0F172A]">
                {{ $attendances->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>

    </div>
</div>
@endsection