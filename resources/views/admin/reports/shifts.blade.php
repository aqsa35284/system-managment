@extends('layouts.admin')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

{{-- ⭐ Extract Active Filters from Controller Scope --}}
@php
    // FIX: Default is now 'all' so data doesn't disappear the next day
    $period = request('period', 'all');
    $operator = request('user_id', '');
@endphp

<div class="min-h-screen p-6 md:p-10 font-sans" style="background-color: #010717; color: #eaeef2;">
    <div class="max-w-[1600px] mx-auto space-y-8">
        
        {{-- ══════════════════════════════════════════════════════
             HEADER SECTION
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end pb-6 gap-4" style="border-bottom: 1px solid #d2a63d22;">
            <div>
                {{-- Back Button --}}
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 rounded-lg font-medium text-xs transition-all border mb-4" style="border-color: #d2a63d; color: #d2a63d; background-color: transparent;" onmouseover="this.style.backgroundColor='#d2a63d'; this.style.color='#010717';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#d2a63d';">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-2 flex items-center gap-2" style="color: #d2a63d;">
                    <i class="fas fa-satellite-dish animate-pulse"></i> Fleet Telemetry
                </p>
                <h1 class="text-4xl font-black uppercase tracking-widest" style="color: #eaeef2;">Shift Intelligence</h1>
                <p class="text-[10px] mt-2 uppercase tracking-[0.2em] font-bold" style="color: #eaeef2; opacity: 0.4;">Automated Performance Reports Archive</p>
            </div>
            <div class="border px-6 py-4 rounded-2xl shadow-lg text-right" style="background-color: #0d1b2e; border-color: #d2a63d30;">
                <p class="text-[9px] font-black uppercase mb-1 tracking-widest" style="color: #d2a63d;">Total Reports Processed</p>
                <p class="text-3xl font-black" style="color: #eaeef2;">{{ $stats['total_processed'] ?? 0 }} <span class="text-xs font-normal" style="color: #eaeef2; opacity: 0.4;">Units</span></p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             KPI CARDS (SHIFT BREAKDOWN)
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="p-6 rounded-3xl shadow-xl relative overflow-hidden group" style="background-color: #0d1b2e; border: 1px solid #d2a63d20;">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"><i class="fas fa-sun"></i></div>
                <p class="text-[9px] uppercase tracking-[0.2em] font-black mb-2 relative z-10" style="color: #d2a63d;">Morning Cycle</p>
                <h3 class="text-3xl font-black relative z-10" style="color: #eaeef2;">{{ $stats['morning_count'] ?? 0 }}</h3>
            </div>
            
            <div class="p-6 rounded-3xl shadow-xl relative overflow-hidden group" style="background-color: #0d1b2e; border: 1px solid #d2a63d20;">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"><i class="fas fa-cloud-sun"></i></div>
                <p class="text-[9px] uppercase tracking-[0.2em] font-black mb-2 relative z-10" style="color: #d2a63d; opacity: 0.8;">Afternoon Cycle</p>
                <h3 class="text-3xl font-black relative z-10" style="color: #eaeef2;">{{ $stats['afternoon_count'] ?? 0 }}</h3>
            </div>

            <div class="p-6 rounded-3xl shadow-xl relative overflow-hidden group" style="background-color: #0d1b2e; border: 1px solid #d2a63d20;">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"><i class="fas fa-moon"></i></div>
                <p class="text-[9px] uppercase tracking-[0.2em] font-black mb-2 relative z-10" style="color: #d2a63d; opacity: 0.7;">Evening Cycle</p>
                <h3 class="text-3xl font-black relative z-10" style="color: #eaeef2;">{{ $stats['evening_count'] ?? 0 }}</h3>
            </div>

            <div class="p-6 rounded-3xl shadow-xl relative overflow-hidden group" style="background-color: #0d1b2e; border: 1px solid #d2a63d20;">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"><i class="fas fa-star-and-crescent"></i></div>
                <p class="text-[9px] uppercase tracking-[0.2em] font-black mb-2 relative z-10" style="color: #d2a63d; opacity: 0.6;">Night Cycle</p>
                <h3 class="text-3xl font-black relative z-10" style="color: #eaeef2;">{{ $stats['night_count'] ?? 0 }}</h3>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             SEARCH & FILTER BAR
        ══════════════════════════════════════════════════════ --}}
        <div class="p-4 md:p-6 rounded-[2rem] border shadow-xl" style="background-color: #0d1b2e; border-color: #d2a63d22;">
            <form action="{{ route('admin.reports.shifts') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                
                {{-- Quick Timeline Pills --}}
                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    @foreach(['all' => 'All Time', 'today' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $key => $label)
                        <button type="submit" name="period" value="{{ $key }}"
                           class="px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all flex-1 md:flex-none text-center"
                           style="{{ $period == $key ? 'background-color: #d2a63d; color: #010717; box-shadow: 0 0 15px rgba(210,166,61,0.35);' : 'background-color: #010717; color: #eaeef2; border: 1px solid #d2a63d30;' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="flex w-full md:w-auto gap-4">
                    {{-- Hidden input to persist period when changing operator --}}
                    <input type="hidden" name="period" value="{{ $period }}">
                    
                    {{-- Operator Dropdown --}}
                    <div class="relative w-full md:w-64">
                        <i class="fas fa-user-astronaut absolute left-4 top-1/2 -translate-y-1/2 text-xs" style="color: #d2a63d; opacity: 0.6;"></i>
                        <select name="user_id" onchange="this.form.submit()" class="w-full rounded-xl pl-10 pr-4 py-3 text-[10px] font-black uppercase tracking-widest outline-none appearance-none transition-all cursor-pointer" style="background-color: #010717; border: 1px solid #d2a63d30; color: #eaeef2;">
                            <option value="">All Operators</option>
                            @foreach(\App\Models\User::where('role', 'employee')->orderBy('name')->get() as $emp)
                                <option value="{{ $emp->id }}" {{ $operator == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] pointer-events-none" style="color: #d2a63d; opacity: 0.5;"></i>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════
             REPORTS TABLE WITH SAAS FEATURE LOCKS
        ══════════════════════════════════════════════════════ --}}
        <div class="rounded-[2rem] border overflow-hidden shadow-xl" style="background-color: #0d1b2e; border-color: #d2a63d22;">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-[9px] uppercase font-black tracking-[0.2em]" style="background-color: #010717; border-color: #d2a63d22; color: #d2a63d;">
                            <th class="px-8 py-5">Operator Node</th>
                            <th class="px-6 py-5">Cycle</th>
                            <th class="px-6 py-5">Work Duration</th>
                            
                            {{-- Locked Header: AI Load (Advanced) --}}
                            <th class="px-6 py-5">
                                @if(auth()->user()->company->hasFeature('ai_tool_tracking'))
                                    <span style="color: #d2a63d; opacity: 0.75;">AI Load</span>
                                @else
                                    <i class="fas fa-lock text-rose-500 mr-1"></i> <span style="opacity: 0.5;">AI Load</span>
                                @endif
                            </th>
                            
                            {{-- Locked Header: Visual Pulses (Basic) --}}
                            <th class="px-6 py-5">
                                @if(auth()->user()->company->hasFeature('screenshot_monitoring'))
                                    Visual Pulses
                                @else
                                    <i class="fas fa-lock text-blue-400 mr-1"></i> <span style="opacity: 0.5;">Visual Pulses</span>
                                @endif
                            </th>
                            
                            <th class="px-6 py-5">Temporal Log</th>
                            
                            {{-- Locked Header: Intel Summary (Pro) --}}
                            <th class="px-8 py-5 text-right">
                                @if(auth()->user()->company->hasFeature('advanced_reporting'))
                                    Intel Summary
                                @else
                                    <i class="fas fa-lock text-gold mr-1"></i> <span style="opacity: 0.5;">Intel Summary</span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm" style="border-color: #d2a63d11;">
                        @forelse($reports as $report)
                        <tr class="transition-colors group" style="border-bottom: 1px solid #d2a63d11;" onmouseover="this.style.backgroundColor='#010717';" onmouseout="this.style.backgroundColor='transparent';">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl border overflow-hidden shadow-sm flex items-center justify-center font-black" style="background-color: #d2a63d20; border-color: #d2a63d40; color: #d2a63d;">
                                        @if($report->user && $report->user->profile_image)
                                            <img src="{{ asset('storage/'.$report->user->profile_image) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($report->user->name ?? 'U', 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black uppercase tracking-wider text-xs transition-colors" style="color: #eaeef2;">{{ $report->user->name ?? 'Unknown' }}</p>
                                        <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5" style="color: #eaeef2; opacity: 0.35;">ID: #{{ $report->user_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $shift = strtoupper($report->shift_name ?? 'MORNING');
                                    $shiftStyle = match($shift) {
                                        'MORNING'   => 'border-color: #d2a63d50; color: #d2a63d; background-color: #d2a63d15;',
                                        'AFTERNOON' => 'border-color: #d2a63d40; color: #c49530; background-color: #d2a63d10;',
                                        'EVENING'   => 'border-color: #d2a63d30; color: #b8892e; background-color: #d2a63d08;',
                                        default     => 'border-color: #eaeef220; color: #eaeef2; background-color: #eaeef205;',
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-[8px] font-black border uppercase tracking-[0.2em]" style="{{ $shiftStyle }}">
                                    {{ $shift }}
                                </span>
                            </td>
                            <td class="px-6 py-5 font-black text-xs" style="color: #eaeef2;">
                                 {{ floor($report->total_seconds / 3600) }}h {{ floor(($report->total_seconds % 3600) / 60) }}m
                            </td>
                            
                            {{-- Locked Data: AI Load --}}
                            <td class="px-6 py-5">
                                @if(auth()->user()->company->hasFeature('ai_tool_tracking'))
                                    <div class="flex flex-col gap-1.5 w-24">
                                        <div class="flex justify-between text-[9px] font-black uppercase tracking-widest">
                                            <span style="color: #d2a63d;">AI.USE</span>
                                            <span style="color: #eaeef2;">{{ $report->ai_percentage ?? 0 }}%</span>
                                        </div>
                                        <div class="w-full rounded-full h-1.5 overflow-hidden" style="background-color: #010717;">
                                            <div class="h-full rounded-full" style="width: {{ $report->ai_percentage ?? 0 }}%; background: linear-gradient(to right, #d2a63d, #f0c060);"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[8px] font-bold uppercase tracking-widest px-2.5 py-1 rounded border cursor-pointer transition-colors hover:bg-rose-500 hover:text-white" 
                                          style="background-color: rgba(244, 63, 94, 0.1); border-color: rgba(244, 63, 94, 0.3); color: #fb7185;" 
                                          onclick="upgradePrompt('Advanced', 'AI Tool Tracking & Load')">
                                        <i class="fas fa-lock mr-1"></i> Adv
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Locked Data: Visual Pulses --}}
                            <td class="px-6 py-5">
                                @if(auth()->user()->company->hasFeature('screenshot_monitoring'))
                                    <span class="text-[10px] font-black uppercase tracking-widest" style="color: #eaeef2;">
                                        {{ $report->pulse_count ?? 0 }} <span style="color: #eaeef2; opacity: 0.3;">CAPS</span>
                                    </span>
                                @else
                                    <span class="text-[8px] font-bold uppercase tracking-widest px-2.5 py-1 rounded border cursor-pointer transition-colors hover:bg-blue-500 hover:text-white" 
                                          style="background-color: rgba(96, 165, 250, 0.1); border-color: rgba(96, 165, 250, 0.3); color: #60a5fa;" 
                                          onclick="upgradePrompt('Basic', 'Visual Pulses (Screenshots)')">
                                        <i class="fas fa-lock mr-1"></i> Bsc
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-5">
                                <p class="text-[10px] font-black uppercase tracking-widest" style="color: #eaeef2; opacity: 0.5;">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M, Y') }}
                                </p>
                            </td>
                            
                            {{-- Locked Data: Intel Summary --}}
                            <td class="px-8 py-5 text-right">
                                @if(auth()->user()->company->hasFeature('advanced_reporting'))
                                    <button onclick="viewShiftIntel('{{ addslashes($report->user->name ?? 'Operator') }}', '{{ addslashes($report->task_summary) }}')" 
                                            class="p-2.5 rounded-xl border transition-all shadow-md"
                                            style="background-color: #010717; border-color: #d2a63d30; color: #d2a63d;"
                                            onmouseover="this.style.backgroundColor='#d2a63d'; this.style.color='#010717'; this.style.borderColor='#d2a63d';"
                                            onmouseout="this.style.backgroundColor='#010717'; this.style.color='#d2a63d'; this.style.borderColor='#d2a63d30';">
                                        <i class="fas fa-file-contract text-xs"></i>
                                    </button>
                                @else
                                    <button onclick="upgradePrompt('Pro', 'Advanced Intel Summary & Rating')" 
                                            class="p-2.5 rounded-xl border shadow-md cursor-pointer transition-colors hover:bg-gold hover:text-navy"
                                            style="background-color: #010717; border-color: rgba(210,166,61,0.2); color: rgba(210,166,61,0.5);">
                                        <i class="fas fa-lock text-xs"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-24 text-center">
                                <i class="fas fa-database text-5xl mb-4 block" style="color: #d2a63d; opacity: 0.2;"></i>
                                <p class="text-xs font-black uppercase tracking-[0.3em]" style="color: #eaeef2; opacity: 0.35;">No Intelligence Matching Filter Found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🛰️ Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $reports->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewShiftIntel(name, text) {
        Swal.fire({
            title: `REPORT: ${name.toUpperCase()}`,
            html: `<div style="text-align: left; padding: 15px; border-radius: 12px; border: 1px solid #d2a63d30; background: #0d1b2e; font-size: 13px; line-height: 1.6; color: #eaeef2;">${text || 'No briefing provided.'}</div>`,
            background: '#010717',
            color: '#eaeef2',
            confirmButtonColor: '#d2a63d',
            confirmButtonText: 'ACKNOWLEDGE',
            customClass: {
                title: 'text-sm font-black uppercase tracking-[0.2em]',
                confirmButton: 'text-[10px] font-black uppercase tracking-[0.2em] px-6 py-3 rounded-xl'
            }
        });
    }

    function upgradePrompt(planName, featureName) {
        Swal.fire({
            title: 'Security Clearance Required',
            html: `<div style="margin-top:10px;">
                    <p style="font-size:12px; color:#eaeef2; margin-bottom:15px;">The <strong>${featureName}</strong> telemetry requires elevated access.</p>
                    <p style="font-size:10px; color:#8a9bb0; text-transform:uppercase; letter-spacing:1px;">Target Plan: <span style="color:#d2a63d; font-weight:bold;">${planName} Plan</span></p>
                   </div>`,
            icon: 'info',
            iconColor: '#d2a63d',
            background: '#05101f',
            color: '#eaeef2',
            showCancelButton: true,
            confirmButtonColor: '#d2a63d',
            cancelButtonColor: '#010717',
            confirmButtonText: 'UPGRADE NODE',
            cancelButtonText: 'DISMISS',
            customClass: {
                title: 'text-sm font-black uppercase tracking-widest text-gold',
                confirmButton: 'text-[10px] font-black uppercase tracking-[0.2em] px-5 py-2.5 rounded-lg text-navy shadow-lg',
                cancelButton: 'text-[10px] font-black uppercase tracking-[0.2em] px-5 py-2.5 rounded-lg border border-slate-700 text-slate-400 hover:text-white'
            }
        });
    }
</script>
@endsection