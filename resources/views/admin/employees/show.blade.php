@extends('layouts.admin')

@section('content')
<div class="p-6 md:p-10 bg-[#0B1120] min-h-screen font-sans text-slate-200">
<div class="max-w-[1600px] mx-auto space-y-8">

{{-- ══════════════════════════════════════════════════════
     1. HIGH-END OPERATOR DOSSIER HEADER
══════════════════════════════════════════════════════ --}}
<div class="relative rounded-[2rem] p-1 bg-gradient-to-b from-blue-600/30 to-transparent shadow-2xl">
    <div class="absolute inset-0 bg-blue-900/10 blur-3xl rounded-full"></div>
    <div class="relative bg-[#0F172A] rounded-[1.8rem] p-8 lg:p-10 flex flex-col lg:flex-row gap-10 items-center lg:items-start border border-blue-900/50 overflow-hidden">
        
        <div class="absolute right-0 top-0 w-1/2 h-full opacity-10 pointer-events-none" 
             style="background-image: radial-gradient(#3B82F6 1px, transparent 1px); background-size: 20px 20px;"></div>

        <div class="relative flex-shrink-0 z-10 group">
            <div class="w-40 h-40 bg-[#1E293B] flex items-center justify-center text-5xl text-blue-300 font-light rounded-[2rem] border-2 border-blue-500/20 shadow-[0_0_40px_rgba(59,130,246,0.15)] overflow-hidden transition-transform duration-500 group-hover:scale-105">
                @if($employee->profile_image)
                    <img src="{{ asset('storage/'.$employee->profile_image) }}" class="w-full h-full object-cover">
                @else
                    {{ substr($employee->name, 0, 1) }}
                @endif
            </div>
            <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 px-5 py-2 rounded-full shadow-2xl border backdrop-blur-md flex items-center gap-2 whitespace-nowrap
                        {{ $isOnline ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border-rose-500/30 text-rose-400' }}">
                <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-emerald-400 animate-pulse' : 'bg-rose-500' }}"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">{{ $isOnline ? 'System Online' : 'Offline / Idle' }}</span>
            </div>
        </div>

        <div class="flex-1 w-full relative z-10 flex flex-col justify-between h-full">
            <div class="flex flex-col xl:flex-row justify-between items-center xl:items-start gap-6 border-b border-slate-700/50 pb-6 mb-6">
                <div class="text-center xl:text-left">
                    <div class="flex items-center justify-center xl:justify-start gap-3 mb-2">
                        <span class="px-2.5 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded text-[9px] font-black uppercase tracking-widest">ID: #{{ $employee->id }}</span>
                        <span class="px-2.5 py-1 bg-slate-800 text-slate-300 rounded text-[9px] font-black uppercase tracking-widest">{{ $employee->department->name ?? 'Fleet Operations' }}</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-white uppercase tracking-wider mb-1">{{ $employee->name }}</h2>
                    <p class="text-sm text-blue-300 font-bold uppercase tracking-[0.2em]">{{ $employee->designation }}</p>
                </div>
                
                <div class="flex flex-wrap justify-center gap-3">
                    <button onclick="triggerPulse({{ $employee->id }})" class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-500 hover:-translate-y-0.5 transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)]">
                        <i class="fas fa-crosshairs"></i> Stealth Pulse
                    </button>
                    <a href="{{ route('admin.employees.pulses', $employee->id) }}" class="flex items-center gap-2 bg-[#1E293B] text-slate-300 border border-slate-700 px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest hover:text-white hover:border-slate-500 hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-photo-video"></i> Archives
                    </a>
                    <a href="{{ route('admin.tasks.forward-history', ['user_id' => $employee->id]) }}" class="flex items-center gap-2 bg-[#1E293B] text-slate-300 border border-slate-700 px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest hover:text-white hover:border-slate-500 hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-route"></i> Logs
                    </a>
                    <button onclick="openFeedbackModal()" class="flex items-center gap-2 bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-emerald-500 hover:text-white hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-star"></i> Evaluate
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-[#0B1120]/50 p-4 rounded-2xl border border-slate-800 backdrop-blur-sm">
                    <p class="text-[9px] text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-1.5"><i class="far fa-clock text-blue-500"></i> Work Time ({{ ucfirst($period) }})</p>
                    <p class="text-2xl font-black text-white">{{ $totalWorkDisplay }}</p>
                </div>
                <div class="bg-[#0B1120]/50 p-4 rounded-2xl border border-slate-800 backdrop-blur-sm">
                    <p class="text-[9px] text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-1.5"><i class="fas fa-robot text-indigo-500"></i> AI Logic ({{ ucfirst($period) }})</p>
                    <p class="text-2xl font-black text-white">{{ $aiPercentage }}%</p>
                </div>
                <div class="bg-[#0B1120]/50 p-4 rounded-2xl border border-slate-800 backdrop-blur-sm">
                    <p class="text-[9px] text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-1.5"><i class="fas fa-camera text-emerald-500"></i> Scans ({{ ucfirst($period) }})</p>
                    <p class="text-2xl font-black text-white">{{ $todayPulseCount }}</p>
                </div>
                <div class="bg-[#0B1120]/50 p-4 rounded-2xl border border-slate-800 backdrop-blur-sm relative overflow-hidden group">
                    @if($socialMediaActivity->sum('count') > 0)
                        <div class="absolute inset-0 bg-rose-500/10 animate-pulse"></div>
                    @endif
                    <p class="text-[9px] text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-1.5 relative z-10"><i class="fas fa-exclamation-triangle text-amber-500"></i> Distractions ({{ ucfirst($period) }})</p>
                    <p class="text-2xl font-black mt-1 relative z-10 {{ $socialMediaActivity->sum('count') > 0 ? 'text-amber-400 drop-shadow-[0_0_10px_rgba(251,191,36,0.5)]' : 'text-slate-300' }}">
                        {{ $socialMediaActivity->sum('count') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     2. GLOBAL TIMELINE FILTER (GLASSMORPHISM)
══════════════════════════════════════════════════════ --}}
<div class="flex justify-center md:justify-start">
    <div class="inline-flex bg-[#1E293B] p-1.5 rounded-2xl shadow-lg border border-slate-700/50 backdrop-blur-md">
        @foreach(['today' => 'Today', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $key => $label)
            <a href="{{ route('admin.employees.show', [$employee->id, 'period' => $key]) }}"
               class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 {{ $period == $key ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)]' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     3. INTELLIGENCE GRID
══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

    {{-- ── LEFT PANE (TASKS, AI, SHIFTS) ──────────────── --}}
    <div class="xl:col-span-2 space-y-8">
        
        {{-- AI UTILIZATION MATRIX --}}
        <div class="bg-[#0F172A] rounded-3xl p-8 border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <i class="fas fa-brain text-lg"></i> Neural & AI Analysis — {{ ucfirst($period) }}
            </h4>
            
            @if($aiToolBreakdown->count() > 0)
            <div class="space-y-6 relative z-10">
                @foreach($aiToolBreakdown as $tool)
                @php
                    $toolMins = round($tool->total_seconds / 60);
                    $maxSecs  = $aiToolBreakdown->max('total_seconds') ?: 1;
                    $barPct   = round(($tool->total_seconds / $maxSecs) * 100);
                @endphp
                <div class="group">
                    <div class="flex justify-between items-end mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            <span class="text-xs font-black text-slate-200 uppercase tracking-widest">{{ $tool->ai_tool }}</span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $toolMins }} mins <span class="mx-1 text-slate-600">|</span> {{ $tool->sessions }} sessions</span>
                    </div>
                    <div class="w-full bg-[#1E293B] h-2 rounded-full overflow-hidden border border-slate-800">
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-400 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $barPct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-10 relative z-10 bg-[#1E293B]/30 rounded-2xl border border-slate-800/50 border-dashed">
                <div class="w-16 h-16 rounded-full bg-slate-800/50 flex items-center justify-center mx-auto mb-4 border border-slate-700">
                    <i class="fas fa-robot text-2xl text-slate-500"></i>
                </div>
                <p class="text-slate-300 font-bold uppercase tracking-widest text-xs">No Neural Activity</p>
                <p class="text-slate-500 text-[10px] mt-2 uppercase tracking-widest">No AI tools recorded for {{ ucfirst($period) }}.</p>
            </div>
            @endif
        </div>

        {{-- ACTIVE OBJECTIVES --}}
        <div class="bg-[#0F172A] rounded-3xl p-8 border border-slate-800 shadow-xl">
            <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                <i class="fas fa-layer-group text-lg"></i> Mission Objectives — {{ ucfirst($period) }}
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $assignedTasks = \App\Models\Task::where('user_id', $employee->id)->latest()->take(6)->get();
                @endphp
                @forelse($assignedTasks as $task)
                <div class="bg-[#1E293B] p-5 rounded-2xl border border-slate-700/50 hover:border-blue-500/50 transition-colors group">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[8px] font-black text-slate-400 bg-slate-800 px-2 py-1 rounded uppercase tracking-[0.2em]">Task #{{ $task->id }}</span>
                        <span class="px-2 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded text-[8px] font-black uppercase tracking-widest">{{ str_replace('_', ' ', $task->status) }}</span>
                    </div>
                    <h5 class="text-sm font-bold text-white mb-4 group-hover:text-blue-300 transition-colors">{{ $task->title }}</h5>
                    <div class="flex items-center gap-2 text-[9px] font-bold text-slate-500 uppercase tracking-widest border-t border-slate-700/50 pt-3">
                        <i class="far fa-calendar-alt text-slate-600"></i> Deployed: {{ $task->created_at->format('d M Y') }}
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-10 bg-[#1E293B]/30 rounded-2xl border border-slate-800/50 border-dashed">
                    <p class="text-slate-400 italic text-[10px] tracking-widest uppercase">No Active Assignments</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- SHIFT REPORTS --}}
        <div class="bg-[#0F172A] rounded-3xl p-8 border border-slate-800 shadow-xl">
            <h4 class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                <i class="fas fa-clipboard-check text-lg"></i> Session Telemetry — {{ ucfirst($period) }}
            </h4>
            <div class="space-y-4">
                @php
                    $shiftReports = \App\Models\ShiftReport::where('user_id', $employee->id)->latest('report_date')->take(5)->get();
                @endphp
                @forelse($shiftReports as $report)
                <div class="flex flex-col md:flex-row gap-4 p-5 bg-[#1E293B] rounded-2xl border border-slate-700/50 items-center justify-between">
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-500">
                            <i class="fas fa-power-off"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-black text-white uppercase tracking-wider">{{ \Carbon\Carbon::parse($report->report_date)->format('d M, Y') }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">{{ $report->shift_name }} Cycle</span>
                            </div>
                            <p class="text-[11px] text-slate-400 line-clamp-1">"{{ $report->task_summary ?? 'System shutdown nominal.' }}"</p>
                        </div>
                    </div>
                    <div class="flex gap-6 border-t md:border-t-0 md:border-l border-slate-700 pt-4 md:pt-0 md:pl-6 w-full md:w-auto justify-around md:justify-end text-center">
                        <div>
                            <p class="text-[8px] text-slate-500 uppercase tracking-widest mb-1">Uptime</p>
                            <p class="text-xs font-black text-white">{{ floor($report->total_seconds / 3600) }}h {{ floor(($report->total_seconds % 3600) / 60) }}m</p>
                        </div>
                        <div>
                            <p class="text-[8px] text-slate-500 uppercase tracking-widest mb-1">Scans</p>
                            <p class="text-xs font-black text-blue-400">{{ $report->pulse_count }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 bg-[#1E293B]/30 rounded-2xl border border-slate-800/50 border-dashed">
                    <p class="text-slate-400 italic text-[10px] tracking-widest uppercase">No Telemetry Recorded for {{ ucfirst($period) }}</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── RIGHT PANE (SURVEILLANCE, REVIEWS, LOGS) ──────────── --}}
    <div class="space-y-8">

        {{-- LATEST SCREENSHOT (LIVE MONITOR) --}}
        <div class="bg-black rounded-3xl border border-blue-900 shadow-[0_0_30px_rgba(0,0,0,0.5)] overflow-hidden relative" data-latest-shot-id="{{ $latestScreenshot->id ?? 0 }}">
            <div class="absolute inset-0 border border-blue-500/20 rounded-3xl pointer-events-none z-20"></div>
            
            <div class="p-4 flex justify-between items-center bg-gradient-to-b from-[#0F172A] to-transparent relative z-10">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                    </span>
                    <h5 class="text-[9px] font-black uppercase tracking-[0.2em] text-white">Live Surveillance</h5>
                </div>
                @if($latestScreenshot && $latestScreenshot->triggered_by_admin)
                    <span class="text-[7px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/50 px-2 py-0.5 rounded uppercase tracking-[0.2em]">Manual Override</span>
                @else
                    <span class="text-[7px] font-black bg-blue-500/20 text-blue-400 border border-blue-500/50 px-2 py-0.5 rounded uppercase tracking-[0.2em]">Auto Protocol</span>
                @endif
            </div>

            @if($latestScreenshot)
            <div class="relative group cursor-pointer" onclick="previewShot('{{ asset('storage/' . $latestScreenshot->image_path) }}', '{{ addslashes($latestScreenshot->window_title ?? '') }}', '{{ addslashes($employee->name) }}')">
                <img id="last-pulse-img" src="{{ asset('storage/' . $latestScreenshot->image_path) }}" class="w-full object-cover max-h-56 opacity-80 group-hover:opacity-100 transition-all duration-500 mix-blend-screen">
                
                <div class="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,0.1)_1px,transparent_1px)] bg-[size:20px_20px] pointer-events-none opacity-50"></div>
                <div class="absolute top-1/2 left-0 w-full h-[2px] bg-blue-400/50 shadow-[0_0_10px_#3B82F6] opacity-30 pointer-events-none animate-scan"></div>
                
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10 bg-black/40">
                    <div class="bg-blue-600/80 backdrop-blur text-white w-10 h-10 rounded-full flex items-center justify-center">
                        <i class="fas fa-expand"></i>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-gradient-to-t from-[#0F172A] to-[#0F172A]/90 border-t border-blue-900/50">
                <p id="last-pulse-title" class="text-[10px] font-black text-white uppercase tracking-widest truncate flex items-center gap-2">
                    <i class="fas fa-terminal text-blue-500"></i> {{ $latestScreenshot->window_title ?? 'Unknown Interface' }}
                </p>
                <p id="last-pulse-time" class="text-[8px] text-slate-500 mt-2 uppercase tracking-[0.2em] font-bold">Signal received: {{ $latestScreenshot->created_at->diffForHumans() }}</p>
            </div>
            @else
            <div class="h-48 flex flex-col items-center justify-center bg-[#1E293B]">
                <i class="fas fa-satellite-dish text-4xl text-slate-600 mb-3"></i>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">No Signal Detected</p>
            </div>
            @endif
        </div>

        {{-- PERFORMANCE EVALUATIONS --}}
        <div class="bg-[#0F172A] p-8 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 text-8xl text-slate-800/30 font-black italic">★</div>
            <div class="flex justify-between items-center mb-6 relative z-10">
                <h4 class="text-[10px] font-black text-amber-400 uppercase tracking-[0.2em] flex items-center gap-3">
                    <i class="fas fa-star text-lg"></i> Command Reviews — {{ ucfirst($period) }}
                </h4>
            </div>
            
            <div class="space-y-4 relative z-10">
                @php
                    $adminFeedbacks = \App\Models\AdminFeedback::where('employee_id', $employee->id)->latest()->take(4)->get();
                @endphp
                @forelse($adminFeedbacks as $review)
                <div class="p-5 rounded-2xl bg-[#1E293B] border border-slate-700/50">
                    <div class="flex justify-between items-start mb-3">
                        <div class="bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded flex items-center gap-1.5 text-amber-400 text-[10px] font-black tracking-widest">
                            <i class="fas fa-bolt"></i> {{ $review->rating_score }}/10
                        </div>
                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">{{ $review->created_at->format('d M Y') }}</span>
                    </div>
                    <p class="text-[11px] text-slate-300 font-bold leading-relaxed mb-3">"{{ $review->review_note }}"</p>
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-500 bg-slate-800 px-2 py-1 rounded inline-block">{{ str_replace('_', ' ', $review->status) }}</span>
                </div>
                @empty
                <div class="text-center py-8 bg-[#1E293B]/30 rounded-2xl border border-slate-800/50 border-dashed">
                    <p class="text-slate-400 italic text-[10px] uppercase tracking-widest">No Evaluations Found</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- OPERATOR TERMINAL LOGS --}}
        <div class="bg-[#0F172A] rounded-3xl p-8 border border-slate-800 shadow-xl font-mono">
            <h4 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                <i class="fas fa-terminal text-lg text-slate-500"></i> System Terminal — {{ ucfirst($period) }}
            </h4>
            <div class="space-y-5">
                @php
                    $empLogs = \App\Models\EmployeeFeedback::where('user_id', $employee->id)->latest()->take(5)->get();
                @endphp
                @forelse($empLogs as $log)
                <div class="relative pl-4 border-l-2 border-slate-700">
                    <div class="absolute w-2 h-2 bg-blue-500 rounded-full -left-[5px] top-1 shadow-[0_0_8px_#3B82F6]"></div>
                    <div class="flex gap-2 items-center mb-1">
                        <span class="text-[9px] text-blue-400">[{{ $log->created_at->format('H:i:s') }}]</span>
                        <span class="text-[9px] text-emerald-400">Progress: {{ $log->completion_percentage }}%</span>
                    </div>
                    <p class="text-[11px] text-slate-300">~ $ {{ $log->update_note }}</p>
                    @if($log->blockers)
                    <p class="text-[10px] text-rose-400 mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle text-[8px]"></i> ERR: {{ $log->blockers }}</p>
                    @endif
                </div>
                @empty
                <div class="pl-4">
                    <p class="text-[10px] text-slate-500 animate-pulse">_ terminal awaiting input...</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

</div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODALS & SCRIPTS (UNTOUCHED LOGIC)
══════════════════════════════════════════════════════ --}}
<style>
    @keyframes scan {
        0% { top: 0; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
    .animate-scan { animation: scan 3s cubic-bezier(0.4, 0, 0.2, 1) infinite; }
</style>

<div id="feedbackModal" class="fixed inset-0 z-[10000] hidden bg-[#0B1120]/95 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-[#0F172A] max-w-md w-full rounded-[2rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-slate-700 p-8 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-emerald-500"></div>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Deploy Evaluation</h3>
            <button type="button" onclick="document.getElementById('feedbackModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:bg-rose-500 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.feedback.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="task_id" value="0">
            
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Performance Rating (1-10)</label>
                <input type="number" name="rating_score" min="1" max="10" value="8"
                       class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-3.5 text-sm font-bold text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Efficiency Status</label>
                <select name="status" class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-3.5 text-sm font-bold text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all appearance-none">
                    <option value="excellent">Excellent Execution</option>
                    <option value="good">Standard Met</option>
                    <option value="needs_improvement">Needs Optimization</option>
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Commander Notes</label>
                <textarea name="review_note" rows="4" required
                          class="w-full bg-[#1E293B] border border-slate-700 rounded-xl p-3.5 text-sm font-bold text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all resize-none placeholder-slate-600"
                          placeholder="Input log details..."></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-blue-500 active:scale-95 transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] mt-2">
                Commit Protocol
            </button>
        </form>
    </div>
</div>

<div id="shotModal" class="fixed inset-0 z-[9999] hidden bg-[#0F172A]/98 backdrop-blur-lg flex items-center justify-center p-6 cursor-pointer" onclick="this.classList.add('hidden')">
    <div class="max-w-6xl w-full bg-[#1E293B] rounded-2xl overflow-hidden shadow-2xl border border-slate-700" onclick="event.stopPropagation()">
        <div class="px-6 py-4 flex justify-between items-center border-b border-slate-700 bg-[#0F172A]">
            <div>
                <p id="shotEmployee" class="text-[10px] font-black text-slate-300 uppercase tracking-widest"></p>
                <p id="shotTitle" class="text-xs text-blue-400 font-bold uppercase tracking-widest mt-1 truncate"></p>
            </div>
            <button onclick="document.getElementById('shotModal').classList.add('hidden')" class="w-10 h-10 rounded-full bg-slate-800 text-slate-400 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center border border-slate-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="bg-black p-4 flex items-center justify-center min-h-[50vh]">
            <img id="shotImg" src="" class="w-full max-h-[75vh] object-contain rounded-lg">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Trigger Pulse JS Logic
    function triggerPulse(userId) {
        const lastScreenshotId = document.querySelector('[data-latest-shot-id]')?.dataset.latestShotId ?? 0;

        Swal.fire({
            title: 'INITIATING SCAN...',
            html: '<p style="color:#64748b;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:2px">Establishing secure link</p>',
            showConfirmButton: false,
            allowOutsideClick: false,
            background: '#0F172A',
            color: '#ffffff',
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/admin/employees/${userId}/trigger-screenshot`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                Swal.fire({ title: 'Error', text: 'Link failed.', icon: 'error', background: '#0F172A', color: '#fff' });
                return;
            }

            let attempts = 0;
            const pollInterval = setInterval(() => {
                attempts++;
                fetch(`/admin/employees/${userId}/latest-trigger-shot`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(shot => {
                    if (shot && shot.id && shot.id != lastScreenshotId && shot.triggered_by_admin) {
                        clearInterval(pollInterval);
                        
                        Swal.fire({
                            title: 'PULSE SECURED',
                            html: `
                                <img src="${shot.image_url}" style="width:100%;border-radius:12px;border:1px solid #1E293B;max-height:240px;object-fit:cover;margin-bottom:15px">
                                <p style="font-size:10px;text-transform:uppercase;font-weight:900;color:#3B82F6;letter-spacing:1px">${shot.window_title || 'Unknown'}</p>
                            `,
                            background: '#0F172A',
                            color: '#ffffff',
                            confirmButtonColor: '#2563EB',
                            confirmButtonText: 'View Archive',
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("admin.employees.pulses", ["id" => $employee->id, "type" => "admin"]) }}';
                            }
                        });

                        // Update Live Card
                        const lastPulseImg = document.getElementById('last-pulse-img');
                        const lastPulseTitle = document.getElementById('last-pulse-title');
                        const lastPulseTime = document.getElementById('last-pulse-time');
                        if (lastPulseImg) { lastPulseImg.src = shot.image_url; }
                        if (lastPulseTitle) lastPulseTitle.innerHTML = `<i class="fas fa-terminal text-blue-500 mr-2"></i>${shot.window_title || 'No title'}`;
                        if (lastPulseTime) lastPulseTime.innerText = 'Signal received: Just now';
                    }
                }).catch(() => {});

                if (attempts >= 7) {
                    clearInterval(pollInterval);
                    if (Swal.isVisible()) {
                        Swal.fire({
                            title: 'SIGNAL DISPATCHED',
                            html: '<p style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:1px">Node is offline. Signal queued for next sync.</p>',
                            icon: 'info',
                            background: '#0F172A',
                            color: '#ffffff',
                            confirmButtonColor: '#1E3A8A'
                        });
                    }
                }
            }, 2000);
        })
        .catch(() => {
            Swal.fire({ title: 'Network Error', icon: 'error', background: '#0F172A', color: '#fff' });
        });
    }

    function openFeedbackModal() {
        document.getElementById('feedbackModal').classList.remove('hidden');
    }

    function previewShot(src, title, employee) {
        document.getElementById('shotImg').src = src;
        document.getElementById('shotTitle').innerText = title || 'No Title Recorded';
        document.getElementById('shotEmployee').innerText = employee || 'Unknown Operator';
        document.getElementById('shotModal').classList.remove('hidden');
    }
</script>
@endsection