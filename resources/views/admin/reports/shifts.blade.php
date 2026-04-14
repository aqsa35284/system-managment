@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#020617] p-6 md:p-12 text-white">
    {{-- 🛰️ Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter">Shift <span class="text-indigo-500">Intelligence</span></h1>
            <p class="text-slate-400 text-xs mt-2 uppercase tracking-widest font-bold opacity-60">Archive: Automated Performance Reports</p>
        </div>
        <div class="bg-indigo-600/10 border border-indigo-500/20 px-6 py-4 rounded-3xl backdrop-blur-xl">
            <p class="text-[10px] font-black uppercase text-indigo-400 mb-1">Total Reports Processed</p>
            <p class="text-2xl font-mono font-bold">{{ $reports->total() }} Units</p>
        </div>
    </div>

    {{-- 📑 Reports Table --}}
    <div class="glass-effect rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10 text-[10px] uppercase font-black tracking-widest text-slate-400">
                        <th class="px-8 py-6 italic">Operator Node</th>
                        <th class="px-6 py-6 italic">Shift Cycle</th>
                        <th class="px-6 py-6 italic">Active Duration</th>
                        <th class="px-6 py-6 italic text-rose-400">AI Penetration</th>
                        <th class="px-6 py-6 italic">Visual Pulses</th>
                        <th class="px-6 py-6 italic">Temporal Log</th>
                        <th class="px-8 py-6 italic text-right">Intel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($reports as $report)
                    <tr class="hover:bg-indigo-600/5 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <img src="{{ $report->user->profile_image ? asset('storage/'.$report->user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($report->user->name).'&background=6366f1&color=fff' }}" 
                                     class="w-10 h-10 rounded-xl border border-white/10 object-cover">
                                <div>
                                    <p class="font-black uppercase italic text-xs tracking-tight">{{ $report->user->name }}</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-tighter">ID: {{ $report->user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            {{-- ⭐ Fixed: Shift Names match with Controller --}}
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black italic border 
                                {{ str_contains($report->shift_name, 'MORNING') ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5' : 
                                   (str_contains($report->shift_name, 'AFTERNOON') ? 'border-amber-500/30 text-amber-400 bg-amber-500/5' : 
                                   'border-purple-500/30 text-purple-400 bg-purple-500/5') }}">
                                {{ $report->shift_name }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            {{-- ⭐ Formatted Time from ShiftReport Model --}}
                            <p class="font-mono font-bold text-xs">{{ $report->formatted_time }}</p>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-1.5 w-24">
                                <div class="flex justify-between text-[8px] font-black italic uppercase">
                                    <span class="text-rose-400">Usage</span>
                                    <span>{{ $report->ai_percentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-1 overflow-hidden">
                                    <div class="bg-rose-500 h-full" style="width: {{ $report->ai_percentage }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="text-xs font-bold text-indigo-300">{{ $report->pulse_count }} Captures</span>
                        </td>
                        <td class="px-6 py-6">
                            {{-- ⭐ Uses report_date from casts in Model --}}
                            <p class="text-[10px] font-bold text-slate-400">{{ $report->report_date->format('d M, Y') }}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <button onclick="viewSummary('{{ addslashes($report->task_summary) }}')" class="p-3 bg-white/5 hover:bg-indigo-600 rounded-xl border border-white/10 transition-all group-hover:scale-110">
                                <i class="fas fa-file-alt text-xs"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-24 text-center">
                            <i class="fas fa-satellite-dish text-5xl text-slate-800 mb-4 animate-pulse"></i>
                            <p class="text-slate-500 uppercase font-black italic tracking-widest text-xs">No Shift Intel Records Found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 🛰️ Pagination --}}
    <div class="mt-10 flex justify-center">
        {{ $reports->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewSummary(text) {
        Swal.fire({
            title: 'SHIFT SUMMARY LOG',
            text: text || "No detailed summary provided by operator.",
            icon: 'info',
            background: '#020617',
            color: '#fff',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'ACKNOWLEDGED'
        });
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;700;900&display=swap');
    body { font-family: 'Space Grotesk', sans-serif; background: #020617; }
    .glass-effect { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); }
</style>
@endsectionx