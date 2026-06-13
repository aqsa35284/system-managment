@extends('layouts.employee')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="min-h-screen bg-[#010717] text-[#eaeef2]" style="font-family: 'DM Mono', 'Fira Code', monospace;">

    {{-- ═══════════════════════════════════════════════════════════
         HEADER & REAL-TIME BADGE
    ═══════════════════════════════════════════════════════════ --}}
    <div class="px-8 pt-8 pb-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                {{-- ⭐ BACK BUTTON --}}
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 mb-3 px-4 py-2 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] text-[10px] font-black uppercase tracking-widest transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </a>
                <p class="text-[9px] text-[#d2a63d] font-bold tracking-[0.4em] uppercase mb-1">// SESSION_LOGS</p>
                <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-[#eaeef2]">My <span class="text-[#d2a63d]">Deployment</span> Log</h1>
                <p class="text-[#eaeef2]/30 text-[10px] mt-1 font-bold tracking-widest uppercase">Pakistan Standard Time · Auto-synced</p>
            </div>
            
            {{-- Live Time Badge --}}
            <div class="bg-[#d2a63d]/10 border border-[#d2a63d]/20 rounded-2xl px-5 py-3 text-left sm:text-right w-full sm:w-auto">
                <p class="text-[8px] text-[#d2a63d] font-black uppercase tracking-widest">Current Session</p>
                <p id="live-clock" class="text-lg font-black font-mono text-[#eaeef2] mt-0.5">--:--:--</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MAIN DUAL LAYOUT HUB
    ═══════════════════════════════════════════════════════════ --}}
    <div class="px-8 pb-10 flex flex-col xl:flex-row gap-6 mt-4">

        {{-- ══════════════════════════════════
             LEFT COLUMN — Dynamic Stats & History
        ══════════════════════════════════ --}}
        <div class="xl:w-[340px] flex-shrink-0 flex flex-col gap-5">

            {{-- 1. Today Card --}}
            @php
                $todayAtt = $attendances->firstWhere('date', \Carbon\Carbon::today('Asia/Karachi')->toDateString());
                if (!$todayAtt) {
                    $todayAtt = $attendances->first();
                }
            @endphp
            <div class="border border-[#d2a63d]/20 rounded-[2rem] p-6 relative overflow-hidden" style="background: linear-gradient(135deg, #d2a63d15 0%, #d2a63d08 100%);">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#d2a63d]/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                <p class="text-[9px] text-[#d2a63d] font-black tracking-[0.35em] uppercase mb-4">⚡ Today's Session</p>
                @if($todayAtt)
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-black/20 rounded-2xl p-3">
                        <p class="text-[8px] text-[#eaeef2]/40 uppercase font-bold mb-1">Login Time</p>
                        <p class="text-base sm:text-lg font-black text-emerald-400 font-mono">
                            {{ $todayAtt->check_in ? \Carbon\Carbon::parse($todayAtt->check_in)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                    <div class="bg-black/20 rounded-2xl p-3">
                        <p class="text-[8px] text-[#eaeef2]/40 uppercase font-bold mb-1">Logout Time</p>
                        <p class="text-base sm:text-lg font-black font-mono {{ $todayAtt->check_out ? 'text-rose-400' : 'text-amber-400 animate-pulse' }}">
                            {{ $todayAtt->check_out ? \Carbon\Carbon::parse($todayAtt->check_out)->format('H:i') : 'LIVE' }}
                        </p>
                    </div>
                </div>
                <div class="bg-black/20 rounded-2xl p-3 mb-3">
                    <p class="text-[8px] text-[#eaeef2]/40 uppercase font-bold mb-1">Total Active Time</p>
                    @php
                        $h = floor($todayAtt->work_time / 3600);
                        $m = floor(($todayAtt->work_time % 3600) / 60);
                        $s = $todayAtt->work_time % 60;
                    @endphp
                    <p class="text-xl sm:text-2xl font-black font-mono text-[#eaeef2]">
                        {{ str_pad($h,2,'0',STR_PAD_LEFT) }}<span class="text-[#d2a63d]">:</span>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}<span class="text-[#eaeef2]/30">:</span>{{ str_pad($s,2,'0',STR_PAD_LEFT) }}
                    </p>
                </div>
                
                {{-- Shift badge --}}
                @php
                    $checkInHour = $todayAtt->check_in ? \Carbon\Carbon::parse($todayAtt->check_in)->hour : -1;
                    if ($checkInHour >= 5 && $checkInHour < 12) { $shiftLabel = '🌅 MORNING CYCLE'; $shiftColor = 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'; }
                    elseif ($checkInHour >= 12 && $checkInHour < 17) { $shiftLabel = '☀️ AFTERNOON CYCLE'; $shiftColor = 'bg-orange-500/10 text-orange-400 border-orange-500/20'; }
                    elseif ($checkInHour >= 17 && $checkInHour < 21) { $shiftLabel = '%;">🌆 EVENING CYCLE'; $shiftColor = 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/20'; }
                    elseif ($checkInHour >= 0) { $shiftLabel = '🌙 NIGHT CYCLE'; $shiftColor = 'bg-purple-500/10 text-purple-400 border-purple-500/20'; }
                    else { $shiftLabel = '— NO SESSION'; $shiftColor = 'bg-[#eaeef2]/5 text-[#eaeef2]/30 border-[#eaeef2]/10'; }
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black border {{ $shiftColor }}">
                    {{ $shiftLabel }}
                </span>
                @else
                <p class="text-[#eaeef2]/20 text-xs italic font-bold text-center py-6">No session detected today</p>
                @endif
            </div>

            {{-- 2. Weekly Heatmap --}}
            <div class="bg-[#010717] border border-[#d2a63d]/10 rounded-[2rem] p-6">
                <p class="text-[9px] text-[#d2a63d]/60 font-black tracking-[0.35em] uppercase mb-4">📅 Last 7 Days Activity</p>
                <div class="space-y-2">
                    @php
                        $timezone = 'Asia/Karachi';
                        $weekDays = collect();
                        for ($i = 6; $i >= 0; $i--) {
                            $date = \Carbon\Carbon::now($timezone)->subDays($i);
                            $att = $attendances->firstWhere('date', $date->toDateString());
                            $secs = $att ? $att->work_time : 0;
                            $weekDays->push(['date' => $date, 'secs' => $secs]);
                        }
                        $maxSecs = $weekDays->max('secs') ?: 1;
                    @endphp
                    @foreach($weekDays as $day)
                    @php
                        $pct = round(($day['secs'] / $maxSecs) * 100);
                        $h = floor($day['secs'] / 3600);
                        $m = floor(($day['secs'] % 3600) / 60);
                        $isToday = $day['date']->isToday();
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-[9px] font-black {{ $isToday ? 'text-[#d2a63d]' : 'text-[#eaeef2]/20' }} w-8 uppercase">{{ $day['date']->format('D') }}</span>
                        <div class="flex-1 h-6 bg-white/5 rounded-lg overflow-hidden relative">
                            <div class="h-full rounded-lg transition-all {{ $isToday ? 'bg-[#d2a63d]' : 'bg-[#d2a63d]/30' }}" style="width: {{ $pct }}%"></div>
                            @if($day['secs'] > 0)
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[8px] font-black text-[#eaeef2]/50">{{ $h }}h {{ $m }}m</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. Shift Distribution --}}
            <div class="bg-[#010717] border border-[#d2a63d]/10 rounded-[2rem] p-6">
                <p class="text-[9px] text-[#d2a63d]/60 font-black tracking-[0.35em] uppercase mb-4">🕐 Allocation Summary</p>
                @php
                    $morningSecs = $attendances->sum(function($a) {
                        $h = $a->check_in ? \Carbon\Carbon::parse($a->check_in)->hour : -1;
                        return ($h >= 5 && $h < 12) ? $a->work_time : 0;
                    });
                    $afternoonSecs = $attendances->sum(function($a) {
                        $h = $a->check_in ? \Carbon\Carbon::parse($a->check_in)->hour : -1;
                        return ($h >= 12 && $h < 17) ? $a->work_time : 0;
                    });
                    $eveningSecs = $attendances->sum(function($a) {
                        $h = $a->check_in ? \Carbon\Carbon::parse($a->check_in)->hour : -1;
                        return ($h >= 17 && $h < 21) ? $a->work_time : 0;
                    });
                    $nightSecs = $attendances->sum(function($a) {
                        $h = $a->check_in ? \Carbon\Carbon::parse($a->check_in)->hour : -1;
                        return ($h >= 21 || $h < 5) ? $a->work_time : 0;
                    });
                    $totalAll = max($morningSecs + $afternoonSecs + $eveningSecs + $nightSecs, 1);
                    $shifts = [
                        ['label' => 'Morning', 'icon' => '🌅', 'secs' => $morningSecs, 'color' => 'bg-yellow-400', 'text' => 'text-yellow-400', 'range' => '05:00–12:00'],
                        ['label' => 'Afternoon', 'icon' => '☀️', 'secs' => $afternoonSecs, 'color' => 'bg-orange-400', 'text' => 'text-orange-400', 'range' => '12:00–17:00'],
                        ['label' => 'Evening', 'icon' => '🌆', 'secs' => $eveningSecs, 'color' => 'bg-[#d2a63d]', 'text' => 'text-[#d2a63d]', 'range' => '17:00–21:00'],
                        ['label' => 'Night', 'icon' => '🌙', 'secs' => $nightSecs, 'color' => 'bg-purple-400', 'text' => 'text-purple-400', 'range' => '21:00–05:00'],
                    ];
                @endphp
                <div class="space-y-3">
                    @foreach($shifts as $shift)
                    @php
                        $pct = round(($shift['secs'] / $totalAll) * 100);
                        $sh = floor($shift['secs'] / 3600);
                        $sm = floor(($shift['secs'] % 3600) / 60);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px]">{{ $shift['icon'] }}</span>
                                <span class="text-[9px] font-black uppercase {{ $shift['text'] }}">{{ $shift['label'] }}</span>
                                <span class="text-[8px] text-[#eaeef2]/20">{{ $shift['range'] }}</span>
                            </div>
                            <span class="text-[9px] font-black font-mono text-[#eaeef2]/40">{{ $sh }}h {{ $sm }}m</span>
                        </div>
                        <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full {{ $shift['color'] }} rounded-full opacity-70 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════
             RIGHT COLUMN — Simplified Clean Logs Table
        ══════════════════════════════════ --}}
        <div class="flex-1 min-w-0">
            <div class="bg-[#010717] border border-[#d2a63d]/10 rounded-[2.5rem] overflow-hidden shadow-2xl backdrop-blur-xl">

                {{-- Table Header --}}
                <div class="px-8 py-5 border-b border-[#d2a63d]/10 flex items-center justify-between">
                    <p class="text-[9px] font-black text-[#d2a63d]/60 tracking-[0.35em] uppercase">Historical Archive</p>
                    <span class="text-[8px] bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/20 px-3 py-1 rounded-full font-black">{{ $attendances->total() }} Sessions</span>
                </div>

                <div class="overflow-x-auto">
                    {{-- ⭐ CLEAN TABLE STRUCTURING: Columns compressed to remove confusing multi-cycle empty slots --}}
                    <table class="w-full min-w-[750px] text-left border-collapse">
                        <thead>
                            <tr class="text-[9px] uppercase tracking-[0.2em] font-black text-[#eaeef2]/20 border-b border-[#d2a63d]/10">
                                <th class="px-6 py-4">Logged Date</th>
                                <th class="px-4 py-4 text-center">Shift Type</th>
                                <th class="px-4 py-4 text-emerald-400 text-center">Sign In</th>
                                <th class="px-4 py-4 text-rose-400 text-center">Sign Out</th>
                                <th class="px-6 py-4 text-[#d2a63d] text-right">Total Hours Worked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d2a63d]/[0.06] font-medium">
                            @forelse($attendances as $att)
                            @php
                                $checkInHour = $att->check_in ? \Carbon\Carbon::parse($att->check_in)->hour : -1;
                                
                                // Shift UI mapping context
                                if ($checkInHour >= 5 && $checkInHour < 12) {
                                    $shiftBadge = '🌅 Morning'; $shiftBg = 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
                                } elseif ($checkInHour >= 12 && $checkInHour < 17) {
                                    $shiftBadge = '☀️ Afternoon'; $shiftBg = 'bg-orange-500/10 text-orange-400 border-orange-500/20';
                                } elseif ($checkInHour >= 17 && $checkInHour < 21) {
                                    $shiftBadge = '%;">🌆 Evening'; $shiftBg = 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/20';
                                } elseif ($checkInHour >= 0) {
                                    $shiftBadge = '🌙 Night'; $shiftBg = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                                } else {
                                    $shiftBadge = '— N/A'; $shiftBg = 'bg-[#eaeef2]/5 text-[#eaeef2]/30 border-[#eaeef2]/10';
                                }
                                
                                $wh = floor($att->work_time / 3600);
                                $wm = floor(($att->work_time % 3600) / 60);
                                $isToday = \Carbon\Carbon::parse($att->date)->isToday();
                            @endphp
                            <tr class="hover:bg-[#d2a63d]/[0.03] transition-colors group {{ $isToday ? 'bg-[#d2a63d]/5' : '' }}">
                                
                                {{-- Logged Date --}}
                                <td class="px-6 py-4.5">
                                    <div>
                                        <p class="font-black text-[11px] {{ $isToday ? 'text-[#d2a63d]' : 'text-[#eaeef2]/70' }} tracking-tight">
                                            {{ $isToday ? '→ CURRENT TODAY' : \Carbon\Carbon::parse($att->date)->format('d M, Y') }}
                                        </p>
                                        <p class="text-[8px] text-[#eaeef2]/20 font-bold uppercase mt-0.5">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</p>
                                    </div>
                                </td>

                                {{-- Shift Type --}}
                                <td class="px-4 py-4.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[8px] font-black border {{ $shiftBg }}">
                                        {{ $shiftBadge }}
                                    </span>
                                </td>

                                {{-- Sign In Time --}}
                                <td class="px-4 py-4.5 text-center">
                                    <span class="text-emerald-400 font-black font-mono text-[11px] bg-emerald-500/5 border border-emerald-500/10 px-2 py-0.5 rounded">
                                        {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s') : '—' }}
                                    </span>
                                </td>

                                {{-- Sign Out Time --}}
                                <td class="px-4 py-4.5 text-center">
                                    @if($att->check_out)
                                        <span class="text-rose-400 font-black font-mono text-[11px] bg-rose-500/5 border border-rose-500/10 px-2 py-0.5 rounded">
                                            {{ \Carbon\Carbon::parse($att->check_out)->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 px-2.5 py-1 rounded-lg text-[8px] font-black tracking-widest animate-pulse">
                                            ◉ LIVE STREAM
                                        </span>
                                    @endif
                                </td>

                                {{-- Total Hours Worked --}}
                                <td class="px-6 py-4.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-black text-[#eaeef2] text-sm font-mono bg-[#d2a63d]/5 border border-[#d2a63d]/10 px-3 py-1 rounded-xl shadow-inner">
                                            {{ $wh }}h {{ $wm }}m
                                        </span>
                                        @if($att->work_time >= 28800)
                                            <span class="text-[7px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-1.5 py-0.5 rounded font-black tracking-wide">COMPLETED</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <i class="fas fa-satellite-dish text-4xl text-[#d2a63d]/20 mb-3 block animate-pulse"></i>
                                    <p class="text-[#eaeef2]/20 uppercase font-black italic tracking-widest text-xs">No active session streams detected</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination controls --}}
                <div class="px-8 py-6 border-t border-[#d2a63d]/10 bg-[#010717]/60">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // System live sync clock engine 
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Karachi', hour12: false };
        document.getElementById('live-clock').textContent = new Intl.DateTimeFormat('en-PK', options).format(now);
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&display=swap');
    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: #010717; }
    ::-webkit-scrollbar-thumb { background: #d2a63d; border-radius: 10px; }
</style>
@endsection