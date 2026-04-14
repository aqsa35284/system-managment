@extends('layouts.employee')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 p-6 md:p-12">
    
    {{-- 🛰️ HEADER SECTIONS --}}
    <div class="mb-10 flex flex-col md:flex-row justify-between items-center bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-2xl gap-6">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold text-white uppercase tracking-tight">
                My <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">Fleet Logs</span>
            </h2>
            <p class="text-white/60 text-xs md:text-sm font-medium uppercase tracking-[0.3em] mt-2 italic">
                Personal Operational Node History
            </p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-white/5 px-6 py-3 rounded-2xl border border-white/10 text-center min-w-[120px]">
                <p class="text-[8px] font-black text-white/40 uppercase mb-1 tracking-widest">Total Sessions</p>
                <p class="text-xl font-mono font-bold text-white">{{ $attendances->total() }}</p>
            </div>
            <a href="{{ route('employee.dashboard') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest hover:scale-105 transition-all shadow-xl">
                <i class="fas fa-arrow-left mr-2"></i> Return to Node
            </a>
        </div>
    </div>

    {{-- 📑 LOGS TABLE --}}
    <div class="bg-white/10 backdrop-blur-md rounded-[2.5rem] shadow-2xl border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 text-white/40">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest italic">Date Cycle</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest italic">Assigned Shift</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest italic">Inbound Signal</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest italic">Outbound Signal</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest italic">Active Hours</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest italic">Integrity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($attendances as $log)
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="p-6 text-sm font-bold text-white/80">
                            {{ \Carbon\Carbon::parse($log->date)->format('d M, Y') }}
                            @if(\Carbon\Carbon::parse($log->date)->isToday())
                                <span class="ml-2 text-[8px] bg-indigo-500 text-white px-2 py-0.5 rounded-full uppercase animate-pulse">Live</span>
                            @endif
                        </td>
                        <td class="p-6">
                            @php
                                $sRaw = $log->shift ?? 'General';
                                $shiftName = ucfirst(strtolower($sRaw));
                                $shiftColors = [
                                    'Morning' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                    'Evening' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                    'Night'   => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                ];
                            @endphp
                            <span class="text-[10px] font-black px-3 py-1.5 rounded-lg border uppercase {{ $shiftColors[$shiftName] ?? 'bg-white/5 text-white/40 border-white/10' }}">
                                {{ $shiftName }}
                            </span>
                        </td>
                        <td class="p-6 text-xs font-mono font-bold text-white/60">
                            {{ $log->check_in ? $log->check_in->format('h:i A') : '---' }}
                        </td>
                        <td class="p-6 text-xs font-mono font-bold text-white/60">
                            @if($log->check_out)
                                {{ $log->check_out->format('h:i A') }}
                            @else
                                <span class="text-rose-400 animate-pulse font-black uppercase italic text-[10px]">Connected...</span>
                            @endif
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                @php
                                    // Live hours calculation logic
                                    $valHours = $log->check_out ? (float)$log->total_hours : ($log->work_time / 3600);
                                    $progress = min(($valHours / 9) * 100, 100); 
                                @endphp
                                <div class="w-16 bg-white/5 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full transition-all duration-1000" 
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-xs font-mono font-bold text-white">
                                    {{ number_format($valHours, 2) }}h
                                </span>
                            </div>
                        </td>
                        <td class="p-6">
                            @if($log->check_out)
                                <div class="flex items-center gap-2 text-emerald-500">
                                    <i class="fas fa-check-circle text-[10px]"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Stored</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-indigo-400">
                                    <i class="fas fa-satellite-dish animate-bounce text-[10px]"></i>
                                    <span class="text-[9px] font-black uppercase tracking-tighter">Syncing</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-database text-6xl mb-4 text-white"></i>
                                <p class="italic text-white uppercase tracking-[0.4em] text-xs font-black">No operational data detected</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="p-8 border-t border-white/5 bg-white/5 flex justify-center">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Fixed Pagination Styling for Dark Theme */
    .pagination { display: flex; list-style: none; gap: 8px; }
    .page-item .page-link { 
        background: rgba(255,255,255,0.05); 
        border: 1px solid rgba(255,255,255,0.1); 
        color: rgba(255,255,255,0.6); 
        padding: 8px 16px; 
        border-radius: 12px; 
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }
    .page-item.active .page-link { 
        background: #6366f1; 
        color: white; 
        border-color: #818cf8;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
    }
    .page-item:hover:not(.active) .page-link { 
        background: rgba(255,255,255,0.15); 
        color: white; 
    }
</style>
@endsection