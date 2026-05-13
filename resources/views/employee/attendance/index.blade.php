@extends('layouts.employee')

@section('content')
<div class="min-h-screen bg-[#020617] text-white" style="font-family: 'DM Mono', 'Fira Code', monospace;">

    {{-- ═══════════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════════ --}}
    <div class="px-8 pt-8 pb-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[9px] text-indigo-400 font-bold tracking-[0.4em] uppercase mb-1">// SESSION_LOGS</p>
                <h1 class="text-3xl font-black uppercase tracking-tight">My <span class="text-indigo-400">Deployment</span> Log</h1>
                <p class="text-slate-500 text-[10px] mt-1 font-bold tracking-widest uppercase">Pakistan Standard Time · Auto-synced</p>
            </div>
            {{-- Live time badge --}}
            <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-2xl px-5 py-3 text-right">
                <p class="text-[8px] text-indigo-400 font-black uppercase tracking-widest">Current Session</p>
                <p id="live-clock" class="text-lg font-black font-mono text-white mt-0.5">--:--:--</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         DUAL COLUMN LAYOUT
    ═══════════════════════════════════════════════════════════ --}}
    <div class="px-8 pb-10 flex flex-col xl:flex-row gap-6 mt-4">

        {{-- ══════════════════════════════════
             LEFT COLUMN — Stats & Heatmap
        ══════════════════════════════════ --}}
        <div class="xl:w-[340px] flex-shrink-0 flex flex-col gap-5">

            {{-- 1. Today Card --}}
            @php
                $todayAtt = $attendances->firstWhere('date', \Carbon\Carbon::today('Asia/Karachi')->toDateString());
                if (!$todayAtt) {
                    $todayAtt = $attendances->first();
                }
            @endphp
            <div class="bg-gradient-to-br from-indigo-600/20 to-purple-600/10 border border-indigo-500/30 rounded-[2rem] p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                <p class="text-[9px] text-indigo-300 font-black tracking-[0.35em] uppercase mb-4">⚡ Today's Session</p>
                @if($todayAtt)
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-black/20 rounded-2xl p-3">
                        <p class="text-[8px] text-slate-400 uppercase font-bold mb-1">Entry</p>
                        <p class="text-lg font-black text-emerald-400 font-mono">
                            {{ $todayAtt->check_in ? \Carbon\Carbon::parse($todayAtt->check_in)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                    <div class="bg-black/20 rounded-2xl p-3">
                        <p class="text-[8px] text-slate-400 uppercase font-bold mb-1">Exit</p>
                        <p class="text-lg font-black font-mono {{ $todayAtt->check_out ? 'text-rose-400' : 'text-amber-400 animate-pulse' }}">
                            {{ $todayAtt->check_out ? \Carbon\Carbon::parse($todayAtt->check_out)->format('H:i') : 'LIVE' }}
                        </p>
                    </div>
                </div>
                <div class="bg-black/20 rounded-2xl p-3 mb-3">
                    <p class="text-[8px] text-slate-400 uppercase font-bold mb-1">Total Active Time</p>
                    @php
                        $h = floor($todayAtt->work_time / 3600);
                        $m = floor(($todayAtt->work_time % 3600) / 60);
                        $s = $todayAtt->work_time % 60;
                    @endphp
                    <p class="text-2xl font-black font-mono text-white">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}<span class="text-indigo-400">:</span>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}<span class="text-slate-500">:</span>{{ str_pad($s,2,'0',STR_PAD_LEFT) }}</p>
                </div>
                {{-- Shift badge --}}
                @php
                    $checkInHour = $todayAtt->check_in ? \Carbon\Carbon::parse($todayAtt->check_in)->hour : -1;
                    if ($checkInHour >= 5 && $checkInHour < 12) { $shiftLabel = '🌅 MORNING'; $shiftColor = 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30'; }
                    elseif ($checkInHour >= 12 && $checkInHour < 17) { $shiftLabel = '☀️ AFTERNOON'; $shiftColor = 'bg-orange-500/20 text-orange-300 border-orange-500/30'; }
                    elseif ($checkInHour >= 17 && $checkInHour < 21) { $shiftLabel = '🌆 EVENING'; $shiftColor = 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30'; }
                    elseif ($checkInHour >= 0) { $shiftLabel = '🌙 NIGHT'; $shiftColor = 'bg-purple-500/20 text-purple-300 border-purple-500/30'; }
                    else { $shiftLabel = '— NO SESSION'; $shiftColor = 'bg-slate-500/20 text-slate-400 border-slate-500/30'; }
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black border {{ $shiftColor }}">
                    {{ $shiftLabel }}
                </span>
                @else
                <p class="text-slate-500 text-xs italic font-bold text-center py-6">No session detected today</p>
                @endif
            </div>

            {{-- 2. Weekly Heatmap --}}
            <div class="bg-slate-900/60 border border-white/5 rounded-[2rem] p-6">
                <p class="text-[9px] text-slate-400 font-black tracking-[0.35em] uppercase mb-4">📅 Last 7 Days</p>
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
                        <span class="text-[9px] font-black {{ $isToday ? 'text-indigo-400' : 'text-slate-500' }} w-8 uppercase">{{ $day['date']->format('D') }}</span>
                        <div class="flex-1 h-6 bg-white/5 rounded-lg overflow-hidden relative">
                            <div class="h-full rounded-lg transition-all {{ $isToday ? 'bg-indigo-500' : 'bg-indigo-600/40' }}" style="width: {{ $pct }}%"></div>
                            @if($day['secs'] > 0)
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[8px] font-black text-white/60">{{ $h }}h {{ $m }}m</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. Shift Distribution --}}
            <div class="bg-slate-900/60 border border-white/5 rounded-[2rem] p-6">
                <p class="text-[9px] text-slate-400 font-black tracking-[0.35em] uppercase mb-4">🕐 Shift Distribution</p>
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
                        ['label' => 'Evening', 'icon' => '🌆', 'secs' => $eveningSecs, 'color' => 'bg-indigo-400', 'text' => 'text-indigo-400', 'range' => '17:00–21:00'],
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
                                <span class="text-[8px] text-slate-600">{{ $shift['range'] }}</span>
                            </div>
                            <span class="text-[9px] font-black font-mono text-white/60">{{ $sh }}h {{ $sm }}m</span>
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
             RIGHT COLUMN — Detailed Logs Table
        ══════════════════════════════════ --}}
        <div class="flex-1 min-w-0">
            <div class="bg-slate-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl backdrop-blur-xl">

                {{-- Table Header --}}
                <div class="px-8 py-5 border-b border-white/5 flex items-center justify-between">
                    <p class="text-[9px] font-black text-slate-400 tracking-[0.35em] uppercase">All Session Logs</p>
                    <span class="text-[8px] bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-3 py-1 rounded-full font-black">{{ $attendances->total() }} Records</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] uppercase tracking-[0.2em] font-black text-slate-500 border-b border-white/5">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-4 py-4">Shift</th>
                                <th class="px-4 py-4 text-emerald-500">Entry</th>
                                <th class="px-4 py-4 text-rose-500">Exit</th>
                                <th class="px-4 py-4 text-yellow-500">Morning</th>
                                <th class="px-4 py-4 text-orange-500">Afternoon</th>
                                <th class="px-4 py-4 text-indigo-400">Evening</th>
                                <th class="px-4 py-4 text-purple-400">Night</th>
                                <th class="px-6 py-4 text-white/60">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @forelse($attendances as $att)
                            @php
                                $checkInHour = $att->check_in ? \Carbon\Carbon::parse($att->check_in)->hour : -1;
                                // Shift detection
                                if ($checkInHour >= 5 && $checkInHour < 12) {
                                    $shiftBadge = '🌅 Morning';
                                    $shiftBg = 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
                                    $rowMorningSecs = $att->work_time;
                                    $rowAftSecs = $rowEveSecs = $rowNightSecs = 0;
                                } elseif ($checkInHour >= 12 && $checkInHour < 17) {
                                    $shiftBadge = '☀️ Afternoon';
                                    $shiftBg = 'bg-orange-500/10 text-orange-400 border-orange-500/20';
                                    $rowAftSecs = $att->work_time;
                                    $rowMorningSecs = $rowEveSecs = $rowNightSecs = 0;
                                } elseif ($checkInHour >= 17 && $checkInHour < 21) {
                                    $shiftBadge = '🌆 Evening';
                                    $shiftBg = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                                    $rowEveSecs = $att->work_time;
                                    $rowMorningSecs = $rowAftSecs = $rowNightSecs = 0;
                                } elseif ($checkInHour >= 0) {
                                    $shiftBadge = '🌙 Night';
                                    $shiftBg = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                                    $rowNightSecs = $att->work_time;
                                    $rowMorningSecs = $rowAftSecs = $rowEveSecs = 0;
                                } else {
                                    $shiftBadge = '— N/A';
                                    $shiftBg = 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                    $rowMorningSecs = $rowAftSecs = $rowEveSecs = $rowNightSecs = 0;
                                }
                                $wh = floor($att->work_time / 3600);
                                $wm = floor(($att->work_time % 3600) / 60);
                                $isToday = \Carbon\Carbon::parse($att->date)->isToday();
                            @endphp
                            <tr class="hover:bg-white/[0.03] transition-colors group {{ $isToday ? 'bg-indigo-600/5' : '' }}">
                                {{-- Date --}}
                                <td class="px-6 py-5">
                                    <div>
                                        <p class="font-black text-[11px] {{ $isToday ? 'text-indigo-300' : 'text-white/80' }} tracking-tight">
                                            {{ $isToday ? '→ TODAY' : \Carbon\Carbon::parse($att->date)->format('d M, Y') }}
                                        </p>
                                        <p class="text-[8px] text-slate-600 font-bold uppercase">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</p>
                                    </div>
                                </td>

                                {{-- Shift --}}
                                <td class="px-4 py-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[8px] font-black border {{ $shiftBg }}">
                                        {{ $shiftBadge }}
                                    </span>
                                </td>

                                {{-- Entry --}}
                                <td class="px-4 py-5">
                                    <span class="text-emerald-400 font-black font-mono text-[11px]">
                                        {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i:s') : '—' }}
                                    </span>
                                </td>

                                {{-- Exit --}}
                                <td class="px-4 py-5">
                                    @if($att->check_out)
                                        <span class="text-rose-400 font-black font-mono text-[11px]">{{ \Carbon\Carbon::parse($att->check_out)->format('H:i:s') }}</span>
                                    @else
                                        <span class="text-amber-400 font-black text-[9px] animate-pulse">◉ LIVE</span>
                                    @endif
                                </td>

                                {{-- Morning --}}
                                <td class="px-4 py-5 font-mono text-[10px] text-yellow-400/70">
                                    {{ $rowMorningSecs > 0 ? floor($rowMorningSecs/3600).'h '.floor(($rowMorningSecs%3600)/60).'m' : '<span class="text-slate-700">—</span>' }}
                                </td>

                                {{-- Afternoon --}}
                                <td class="px-4 py-5 font-mono text-[10px] text-orange-400/70">
                                    {{ $rowAftSecs > 0 ? floor($rowAftSecs/3600).'h '.floor(($rowAftSecs%3600)/60).'m' : '<span class="text-slate-700">—</span>' }}
                                </td>

                                {{-- Evening --}}
                                <td class="px-4 py-5 font-mono text-[10px] text-indigo-400/70">
                                    {{ $rowEveSecs > 0 ? floor($rowEveSecs/3600).'h '.floor(($rowEveSecs%3600)/60).'m' : '<span class="text-slate-700">—</span>' }}
                                </td>

                                {{-- Night --}}
                                <td class="px-4 py-5 font-mono text-[10px] text-purple-400/70">
                                    {{ $rowNightSecs > 0 ? floor($rowNightSecs/3600).'h '.floor(($rowNightSecs%3600)/60).'m' : '<span class="text-slate-700">—</span>' }}
                                </td>

                                {{-- Total --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-white text-sm font-mono">{{ $wh }}h {{ $wm }}m</span>
                                        @if($att->work_time >= 28800)
                                            <span class="text-[7px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded font-black">FULL</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-24 text-center">
                                    <i class="fas fa-satellite-dish text-4xl text-slate-800 mb-3 block animate-pulse"></i>
                                    <p class="text-slate-600 uppercase font-black italic tracking-widest text-xs">No signal logs found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-8 py-6 border-t border-white/5">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live clock
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
    ::-webkit-scrollbar-track { background: #020617; }
    ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 10px; }
</style>
@endsection