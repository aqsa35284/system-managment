@extends('layouts.employee')

@section('content')
<div class="min-h-screen bg-[#020617] p-8">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-black text-white uppercase italic mb-8 tracking-tighter">My Deployment Log</h2>

        <div class="bg-white/5 border border-white/10 rounded-[2.5rem] overflow-hidden backdrop-blur-xl shadow-2xl">
            <table class="w-full text-left">
                <thead class="bg-white/5">
                    <tr class="text-indigo-400 uppercase text-[10px] tracking-[0.2em] font-black">
                        <th class="p-6">Cycle Date</th>
                        <th class="p-6">Shift Node</th>
                        <th class="p-6">Entry</th>
                        <th class="p-6">Exit</th>
                        <th class="p-6">Total Active</th>
                    </tr>
                </thead>
                <tbody class="text-white/80 text-xs">
                    @forelse($attendances as $att)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="p-6 font-bold text-slate-300">{{ $att->date->format('d M, Y') }}</td>
                        <td class="p-6">
                            <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 rounded-lg text-[9px] font-black italic">
                                {{ $att->shift_name ?? 'OPERATIONAL' }}
                            </span>
                        </td>
                        <td class="p-6 text-emerald-400 font-mono">{{ $att->check_in->format('H:i:s') }}</td>
                        <td class="p-6 font-mono text-rose-400">
                            {{ $att->check_out ? $att->check_out->format('H:i:s') : 'BROADCASTING...' }}
                        </td>
                        <td class="p-6">
                            <span class="font-black text-white italic">
                                {{ floor($att->work_time / 3600) }}h {{ floor(($att->work_time % 3600) / 60) }}m
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-20 text-center text-white/20 italic uppercase tracking-widest">No signal logs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection