@extends('layouts.admin')

@section('content')
 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="p-6 md:p-10 bg-[#010717] min-h-screen font-sans text-[#eaeef2]">
    <div class="max-w-[1600px] mx-auto space-y-6">
        
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-[#d2a63d]/20 gap-4">
            <div class="flex items-center gap-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Back to Dashboard">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-widest">Operational <span class="text-[#d2a63d]">Tasks</span></h2>
                    <p class="text-[10px] font-bold text-[#eaeef2]/60 uppercase tracking-widest mt-1 italic">// Live Core Objective Log Matrix</p>
                </div>
            </div>
            <a href="{{ route('admin.tasks.create') }}" class="bg-[#d2a63d] hover:bg-[#b8860b] text-[#010717] px-6 py-3.5 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-[0_0_20px_rgba(210,166,61,0.3)] transition-all flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Create New Objective
            </a>
        </div>

        {{-- Main Tasks Ledger Matrix --}}
        <div class="bg-[#030712] rounded-3xl shadow-xl border border-[#d2a63d]/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#d2a63d]/5 border-b border-[#d2a63d]/20 text-[8px] md:text-[9px] font-black text-[#eaeef2]/50 uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">Objective Details</th>
                            <th class="px-4 py-4">Assigned Operator</th>
                            <th class="px-4 py-4 text-center">Completion Stage</th>
                            <th class="px-4 py-4 text-center">Telemetry Metrics</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2a63d]/10">
                        @forelse($tasks as $task)
                        @php
                            $stageStr = $task->completion_stage ?? 'not_started';
                            $stageBadge = match($stageStr) {
                                'completed'   => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'halfway'     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'progress'    => 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/20',
                                default       => 'bg-[#eaeef2]/5 text-[#eaeef2]/50 border-[#eaeef2]/10',
                            };
                            $hasForwarded = $task->forwards && $task->forwards->count() > 0;
                        @endphp
                        <tr class="hover:bg-[#d2a63d]/5 transition-colors group">
                            
                            {{-- Task Info --}}
                            <td class="px-6 py-4 max-w-[280px]">
                                <div class="flex items-center gap-2">
                                    <p class="text-[8px] font-black font-mono text-[#d2a63d] uppercase tracking-wider mb-0.5">
                                        {{ $task->project->name ?? 'Standalone Operations' }}
                                    </p>
                                    @if($hasForwarded)
                                        <span onclick="openIntelModal({{ json_encode($task->forwards) }}, '{{ addslashes($task->title) }}')" 
                                              class="cursor-pointer text-[6px] font-black bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30 px-1.5 py-0.5 rounded tracking-widest uppercase hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow animate-pulse">
                                            <i class="fas fa-share-square"></i> Forwarded
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-xs font-black text-white group-hover:text-[#d2a63d] transition-colors uppercase tracking-wide truncate mt-1">
                                    {{ $task->title }}
                                </h4>
                                @if($task->due_date)
                                <p class="text-[8px] font-bold text-[#eaeef2]/40 mt-1 uppercase tracking-widest">
                                    Due: <span class="{{ $task->due_date->isPast() ? 'text-rose-400' : 'text-[#eaeef2]/60' }}">
                                        {{ $task->due_date->format('d M Y') }}
                                    </span>
                                </p>
                                @endif
                                <div class="w-full bg-[#eaeef2]/10 h-1 rounded-full mt-2 overflow-hidden max-w-[150px]">
                                    <div class="bg-[#d2a63d] h-full" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                                </div>
                            </td>

                            {{-- Employee Name --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-[#d2a63d]/10 border border-[#d2a63d]/20 flex items-center justify-center text-[#d2a63d] font-black text-[9px] uppercase">
                                        {{ substr($task->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-[#eaeef2] uppercase tracking-wide">
                                        {{ $task->user->name ?? 'Unassigned Node' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Dynamic Stage Column --}}
                            <td class="px-4 py-4 text-center">
                                <span class="text-[7px] md:text-[8px] font-black px-2.5 py-1 rounded border uppercase tracking-widest shadow-sm {{ $stageBadge }}">
                                    {{ str_replace('_', ' ', $stageStr) }}
                                </span>
                                <p class="text-[8px] font-black text-[#eaeef2] mt-1">{{ $task->completion_percentage ?? 0 }}%</p>
                            </td>

                            {{-- Telemetry --}}
                            <td class="px-4 py-4 text-center font-mono text-[10px]">
                                <div class="text-[#eaeef2] font-black">Active: {{ $task->days_worked ?? 0 }}d</div>
                                @if(($task->overdue_days ?? 0) > 0)
                                    <span class="text-[7px] font-sans font-black bg-rose-500/10 border border-rose-500/20 text-rose-400 px-1 py-0.5 rounded mt-0.5 inline-block animate-pulse">
                                        ⚠️ +{{ $task->overdue_days }}d OVERDUE
                                    </span>
                                @endif
                            </td>

                            {{-- Configuration Buttons --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-1 justify-end">
                                    @if($hasForwarded)
                                        <button onclick="openIntelModal({{ json_encode($task->forwards) }}, '{{ addslashes($task->title) }}')" 
                                                class="w-7 h-7 bg-[#030712] border border-[#d2a63d]/20 text-[#d2a63d] hover:text-[#010717] hover:bg-[#d2a63d] rounded-lg flex items-center justify-center shadow" title="View Forward Logs">
                                            <i class="fas fa-history text-[10px]"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.tasks.show', $task->id) }}" 
                                       class="w-7 h-7 bg-[#030712] border border-[#d2a63d]/20 text-[#eaeef2]/60 hover:text-emerald-400 rounded-lg flex items-center justify-center shadow" title="View Details">
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </a>
                                    <a href="{{ route('admin.tasks.edit', $task->id) }}" 
                                       class="w-7 h-7 bg-[#030712] border border-[#d2a63d]/20 text-[#eaeef2]/60 hover:text-[#d2a63d] rounded-lg flex items-center justify-center shadow" title="Edit">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </a>
                                    <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Purge Objective?')" 
                                                class="w-7 h-7 bg-[#030712] border border-rose-500/20 text-[#eaeef2]/60 hover:text-rose-400 rounded-lg flex items-center justify-center shadow" title="Delete">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[10px] font-black text-[#eaeef2]/40 uppercase tracking-widest">
                                No analytical task configurations registered.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Forward Transmission History Modal --}}
<div id="intelModal" class="fixed inset-0 z-[10000] hidden bg-[#010717]/95 backdrop-blur-md flex items-center justify-center p-4">
    <div id="intelModalContent" class="bg-[#030712] max-w-xl w-full rounded-[2.5rem] shadow-[0_0_50px_rgba(0,0,0,0.6)] border border-[#d2a63d]/30 overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <div class="p-6 bg-[#d2a63d]/5 flex justify-between items-center border-b border-[#d2a63d]/20">
            <div>
                <h3 class="text-[10px] font-black text-[#d2a63d] uppercase tracking-[0.2em]">Forward Routing Telemetry</h3>
                <p id="intel_task_title" class="text-xs text-[#eaeef2] font-bold uppercase tracking-wide mt-1 truncate max-w-[350px]"></p>
            </div>
            <button onclick="closeIntelModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-[#010717] text-[#eaeef2]/60 hover:bg-rose-600 hover:text-white border border-[#d2a63d]/20 transition-all shadow">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-8 space-y-4 max-h-[450px] overflow-y-auto custom-scrollbar">
            <div id="forward_logs_container" class="space-y-4 relative before:absolute before:inset-y-0 before:left-3.5 before:w-[1px] before:bg-[#d2a63d]/20"></div>
        </div>
    </div>
</div>

<script>
    function openIntelModal(forwards, taskTitle) {
        document.getElementById('intel_task_title').innerText = taskTitle;
        const container = document.getElementById('forward_logs_container');
        container.innerHTML = '';

        forwards.forEach(log => {
            const dateFormatted = log.created_at
                ? new Date(log.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                : 'Ongoing';
            
            const fromName = log.from_user ? log.from_user.name : 'Operator';
            const toName   = log.to_user   ? log.to_user.name   : 'Next Agent';

            const item = document.createElement('div');
            item.className = "flex items-start gap-4 relative z-10";
            item.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-[#d2a63d]/10 border border-[#d2a63d]/30 flex items-center justify-center flex-shrink-0 text-[#d2a63d] text-xs shadow-md">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-2xl p-4 flex-1 shadow-md">
                    <div class="flex justify-between items-center mb-2 flex-wrap gap-2">
                        <span class="text-[9px] font-black text-[#eaeef2] uppercase tracking-wide">
                            <b class="text-rose-400">${fromName}</b> ➔ <b class="text-emerald-400">${toName}</b>
                        </span>
                        <span class="text-[8px] font-mono font-bold text-[#eaeef2]/40 uppercase tracking-widest">${dateFormatted}</span>
                    </div>
                    <p class="text-[11px] text-[#eaeef2]/60 italic font-medium leading-relaxed">"${log.note ?? 'No justification logs appended.'}"</p>
                </div>
            `;
            container.appendChild(item);
        });

        const modal   = document.getElementById('intelModal');
        const content = document.getElementById('intelModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => content.classList.remove('scale-95', 'opacity-0'), 10);
    }

    function closeIntelModal() {
        const content = document.getElementById('intelModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => document.getElementById('intelModal').classList.add('hidden'), 300);
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(210, 166, 61, 0.4); border-radius: 10px; }
</style>
@endsection