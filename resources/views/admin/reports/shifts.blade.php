@extends('layouts.admin')

@section('content')

{{-- ⭐ DYNAMIC QUERY & KPI ENGINE (No Controller Changes Needed) --}}
@php
    $period = request('period', 'today');
    $operator = request('user_id', '');

    $query = \App\Models\ShiftReport::with('user');

    // 1. Operator Filter
    if ($operator) {
        $query->where('user_id', $operator);
    }

    // 2. Timeline Filter
    if ($period == 'today') {
        $query->whereDate('report_date', \Carbon\Carbon::today('Asia/Karachi'));
    } elseif ($period == 'weekly') {
        $query->whereDate('report_date', '>=', \Carbon\Carbon::today('Asia/Karachi')->subDays(7));
    } elseif ($period == 'monthly') {
        $query->whereDate('report_date', '>=', \Carbon\Carbon::today('Asia/Karachi')->subDays(30));
    } elseif ($period == 'yearly') {
        $query->whereDate('report_date', '>=', \Carbon\Carbon::today('Asia/Karachi')->subDays(365));
    }

    // 3. Extract KPIs before pagination
    $kpiData = (clone $query)->get();
    $morningCount = $kpiData->where('shift_name', 'Morning')->count();
    $afternoonCount = $kpiData->where('shift_name', 'Afternoon')->count();
    $eveningCount = $kpiData->where('shift_name', 'Evening')->count();
    $nightCount = $kpiData->where('shift_name', 'Night')->count();
    $totalProcessed = $kpiData->count();

    // 4. Fetch Paginated List
    $reportsList = $query->latest('created_at')->paginate(15);
@endphp

<div class="min-h-screen bg-[#0B1120] p-6 md:p-10 text-slate-200 font-sans">
    <div class="max-w-[1600px] mx-auto space-y-8">
        
        {{-- ══════════════════════════════════════════════════════
             HEADER SECTION
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end pb-6 border-b border-slate-800 gap-4">
            <div>
                <p class="text-[10px] text-blue-500 font-black uppercase tracking-[0.3em] mb-2 flex items-center gap-2">
                    <i class="fas fa-satellite-dish animate-pulse"></i> Fleet Telemetry
                </p>
                <h1 class="text-4xl font-black text-white uppercase tracking-widest">Shift Intelligence</h1>
                <p class="text-slate-400 text-[10px] mt-2 uppercase tracking-[0.2em] font-bold">Automated Performance Reports Archive</p>
            </div>
            <div class="bg-[#1E293B]/80 border border-slate-700 px-6 py-4 rounded-2xl shadow-lg backdrop-blur text-right">
                <p class="text-[9px] font-black uppercase text-blue-400 mb-1 tracking-widest">Total Reports Processed</p>
                <p class="text-3xl font-black text-white">{{ $totalProcessed }} <span class="text-xs text-slate-500 font-normal">Units</span></p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             KPI CARDS (SHIFT BREAKDOWN)
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-[#0F172A] p-6 rounded-3xl border border-emerald-900/30 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-emerald-500 group-hover:scale-110 transition-transform"><i class="fas fa-sun"></i></div>
                <p class="text-[9px] text-emerald-400 uppercase tracking-[0.2em] font-black mb-2 relative z-10">Morning Cycle</p>
                <h3 class="text-3xl font-black text-white relative z-10">{{ $morningCount }}</h3>
            </div>
            
            <div class="bg-[#0F172A] p-6 rounded-3xl border border-amber-900/30 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-amber-500 group-hover:scale-110 transition-transform"><i class="fas fa-cloud-sun"></i></div>
                <p class="text-[9px] text-amber-400 uppercase tracking-[0.2em] font-black mb-2 relative z-10">Afternoon Cycle</p>
                <h3 class="text-3xl font-black text-white relative z-10">{{ $afternoonCount }}</h3>
            </div>

            <div class="bg-[#0F172A] p-6 rounded-3xl border border-purple-900/30 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-purple-500 group-hover:scale-110 transition-transform"><i class="fas fa-moon"></i></div>
                <p class="text-[9px] text-purple-400 uppercase tracking-[0.2em] font-black mb-2 relative z-10">Evening Cycle</p>
                <h3 class="text-3xl font-black text-white relative z-10">{{ $eveningCount }}</h3>
            </div>

            <div class="bg-[#0F172A] p-6 rounded-3xl border border-blue-900/30 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-blue-500 group-hover:scale-110 transition-transform"><i class="fas fa-star-and-crescent"></i></div>
                <p class="text-[9px] text-blue-400 uppercase tracking-[0.2em] font-black mb-2 relative z-10">Night Cycle</p>
                <h3 class="text-3xl font-black text-white relative z-10">{{ $nightCount }}</h3>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEARCH & FILTER BAR
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] p-4 md:p-6 rounded-[2rem] border border-slate-800 shadow-xl">
            <form action="{{ route('admin.reports.shifts') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                
                {{-- Quick Timeline Pills --}}
                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    @foreach(['today' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $key => $label)
                        <button type="submit" name="period" value="{{ $key }}"
                           class="px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all flex-1 md:flex-none text-center
                           {{ $period == $key ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)]' : 'bg-[#1E293B] text-slate-400 border border-slate-700 hover:text-white' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="flex w-full md:w-auto gap-4">
                    {{-- Hidden input to persist period when changing operator --}}
                    <input type="hidden" name="period" value="{{ $period }}">
                    
                    {{-- Operator Dropdown --}}
                    <div class="relative w-full md:w-64">
                        <i class="fas fa-user-astronaut absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <select name="user_id" onchange="this.form.submit()" class="w-full bg-[#1E293B] border border-slate-700 rounded-xl pl-10 pr-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-300 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none transition-all cursor-pointer">
                            <option value="" class="bg-slate-900">All Operators</option>
                            @foreach(\App\Models\User::where('role', 'employee')->orderBy('name')->get() as $emp)
                                <option value="{{ $emp->id }}" {{ $operator == $emp->id ? 'selected' : '' }} class="bg-slate-900">
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════
             REPORTS TABLE
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] rounded-[2rem] border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#1E293B]/50 border-b border-slate-800 text-[9px] uppercase font-black tracking-[0.2em] text-slate-400">
                            <th class="px-8 py-5">Operator Node</th>
                            <th class="px-6 py-5">Cycle</th>
                            <th class="px-6 py-5">Work Duration</th>
                            <th class="px-6 py-5 text-indigo-400">AI Load</th>
                            <th class="px-6 py-5">Visual Pulses</th>
                            <th class="px-6 py-5">Temporal Log</th>
                            <th class="px-8 py-5 text-right">Intel Summary</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($reportsList as $report)
                        <tr class="hover:bg-[#1E293B]/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-900 border border-slate-700 overflow-hidden shadow-sm flex items-center justify-center text-blue-300 font-black">
                                        @if($report->user->profile_image)
                                            <img src="{{ asset('storage/'.$report->user->profile_image) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($report->user->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black uppercase tracking-wider text-xs text-slate-200 group-hover:text-blue-400 transition-colors">{{ $report->user->name }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">ID: #{{ $report->user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $shift = strtoupper($report->shift_name);
                                    $class = match($shift) {
                                        'MORNING' => 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10',
                                        'AFTERNOON' => 'border-amber-500/30 text-amber-400 bg-amber-500/10',
                                        'EVENING' => 'border-purple-500/30 text-purple-400 bg-purple-500/10',
                                        default => 'border-blue-500/30 text-blue-400 bg-blue-500/10',
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-[8px] font-black border {{ $class }} uppercase tracking-[0.2em]">
                                    {{ $shift }}
                                </span>
                            </td>
                            <td class="px-6 py-5 font-black text-xs text-white">
                                 {{ floor($report->total_seconds / 3600) }}h {{ floor(($report->total_seconds % 3600) / 60) }}m
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1.5 w-24">
                                    <div class="flex justify-between text-[9px] font-black uppercase tracking-widest">
                                        <span class="text-indigo-400">AI.USE</span>
                                        <span class="text-white">{{ $report->ai_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-600 to-blue-400 h-full rounded-full" style="width: {{ $report->ai_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
                                    {{ $report->pulse_count }} <span class="text-slate-600">CAPS</span>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M, Y') }}
                                </p>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button onclick="viewShiftIntel('{{ addslashes($report->user->name) }}', '{{ addslashes($report->task_summary) }}')" 
                                        class="p-2.5 bg-[#1E293B] hover:bg-blue-600 text-blue-400 hover:text-white rounded-xl border border-slate-700 transition-all shadow-md">
                                    <i class="fas fa-file-contract text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-24 text-center">
                                <i class="fas fa-database text-5xl mb-4 text-slate-700 block"></i>
                                <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-500">No Intelligence Matching Filter Found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🛰️ Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $reportsList->appends(request()->input())->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewShiftIntel(name, text) {
        Swal.fire({
            title: `REPORT: ${name.toUpperCase()}`,
            html: `<div style="text-align: left; padding: 15px; border-radius: 12px; border: 1px solid #334155; background: #1E293B; font-size: 13px; line-height: 1.6; color: #cbd5e1;">${text || 'No briefing provided.'}</div>`,
            background: '#0F172A',
            color: '#fff',
            confirmButtonColor: '#2563EB',
            confirmButtonText: 'ACKNOWLEDGE',
            customClass: {
                title: 'text-sm font-black uppercase tracking-[0.2em] text-white',
                confirmButton: 'text-[10px] font-black uppercase tracking-[0.2em] px-6 py-3 rounded-xl'
            }
        });
    }
</script>
@endsection