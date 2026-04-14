@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#020617] p-8">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter">Fleet Attendance</h1>
            <p class="text-[10px] text-white/40 uppercase tracking-[0.3em] mt-1">Operational Node Activity & Session Logs</p>
        </div>
        <div class="flex gap-4">
             <div class="bg-indigo-600/20 border border-indigo-500/30 px-6 py-2 rounded-2xl text-center">
                 <p class="text-[8px] text-indigo-300 uppercase font-black">Active Nodes Now</p>
                 <p class="text-xl font-black text-white">{{ $attendances->whereNull('check_out')->count() }}</p>
             </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="glass-effect rounded-[3rem] border border-white/10 overflow-hidden shadow-2xl">
        <table class="w-full text-left">
            <thead class="bg-white/5 border-b border-white/10">
                <tr class="text-indigo-400 uppercase text-[9px] tracking-widest font-black">
                    <th class="p-6">Node Operator</th>
                    <th class="p-6 text-center">Assigned Shift</th>
                    <th class="p-6">Entry Signal</th>
                    <th class="p-6">Exit Signal</th>
                    <th class="p-6">Total Active Time</th>
                    <th class="p-6">Integrity</th>
                </tr>
            </thead>
            <tbody class="text-white/80 text-[11px]">
                @foreach($attendances as $att)
                <tr class="border-b border-white/5 hover:bg-white/5 transition-all group">
                    <td class="p-6 flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($att->user->name) }}&background=4f46e5&color=fff" class="w-10 h-10 rounded-xl shadow-lg border border-white/10">
                        <div>
                            <p class="font-black uppercase text-white tracking-tight">{{ $att->user->name }}</p>
                            <p class="text-[8px] text-white/30 uppercase italic">{{ $att->user->department->name ?? 'MEMBER FLEET' }}</p>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="px-4 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-full text-[8px] font-black uppercase italic border border-emerald-500/20">
                            {{ $att->shift_name ?? 'OPERATIONAL' }}
                        </span>
                    </td>
                    <td class="p-6 font-mono font-bold text-slate-300">{{ $att->check_in->format('H:i:s') }}</td>
                    <td class="p-6 font-mono">
                        @if($att->check_out)
                            <span class="text-rose-400 font-bold">{{ $att->check_out->format('H:i:s') }}</span>
                        @else
                            <span class="text-emerald-500 animate-pulse font-black uppercase text-[8px] italic tracking-tighter">● BROADCASTING...</span>
                        @endif
                    </td>
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-16 bg-white/5 h-1 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-full" style="width: 70%"></div>
                            </div>
                            <span class="font-black text-white italic">
                                {{ floor($att->work_time / 3600) }}h {{ floor(($att->work_time % 3600) / 60) }}m
                            </span>
                        </div>
                    </td>
                    <td class="p-6">
                        <span class="px-3 py-1 rounded bg-{{ $att->check_out ? 'rose' : 'emerald' }}-500 text-white text-[8px] font-black uppercase">
                            {{ $att->check_out ? 'OFFLINE' : 'PRESENT' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .glass-effect { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); }
</style>
@endsection