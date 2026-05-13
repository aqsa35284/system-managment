@extends('layouts.admin')

@section('content')

{{-- ⭐ DATA CALCULATION BLOCK (No Controller Change Needed) --}}
@php
    $period = request('period', 'today');
    $type = request('type', 'all');

    // Calculate Date Range
    $startDate = \Carbon\Carbon::today('Asia/Karachi');
    if ($period == 'weekly') $startDate = \Carbon\Carbon::today('Asia/Karachi')->subDays(7);
    elseif ($period == 'monthly') $startDate = \Carbon\Carbon::today('Asia/Karachi')->subDays(30);
    elseif ($period == 'yearly') $startDate = \Carbon\Carbon::today('Asia/Karachi')->subDays(365);

    // 1. Get Screen Capture Counts
    $statsQuery = \App\Models\Screenshot::where('user_id', $employee->id)->whereDate('created_at', '>=', $startDate);
    $totalAuto = (clone $statsQuery)->where('triggered_by_admin', false)->count();
    $totalAdmin = (clone $statsQuery)->where('triggered_by_admin', true)->count();

    // 2. Get AI & Social Media Lists
    $logsQuery = \App\Models\AiUsageLog::where('user_id', $employee->id)
        ->whereDate('log_date', '>=', $startDate)
        ->where('ai_tool', '!=', 'IDLE')
        ->get();

    $socialKeywords = ['FACEBOOK', 'YOUTUBE', 'NETFLIX', 'TIKTOK', 'INSTAGRAM', 'WHATSAPP', 'TWITTER'];
    $aiUsage = [];
    $socialUsage = [];

    foreach($logsQuery as $log) {
        $tool = $log->ai_tool;
        if (in_array($tool, $socialKeywords)) {
            $socialUsage[$tool] = ($socialUsage[$tool] ?? 0) + $log->sessions ?? 1; // Assuming each log is a session
        } else {
            $aiUsage[$tool] = ($aiUsage[$tool] ?? 0) + $log->sessions ?? 1;
        }
    }
@endphp

<div class="p-6 md:p-10 bg-[#0B1120] min-h-screen font-sans text-slate-200">
    <div class="max-w-[1600px] mx-auto space-y-8">
        
        {{-- ══════════════════════════════════════════════════════
             1. HEADER & TIMELINE FILTER (HIGH-END DARK THEME)
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] rounded-[2rem] shadow-[0_0_40px_rgba(0,0,0,0.5)] p-8 flex flex-col xl:flex-row justify-between items-center xl:items-end border border-blue-900/50 relative overflow-hidden gap-8">
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl translate-x-10 -translate-y-20 pointer-events-none"></div>
            
            <div class="flex items-center gap-6 relative z-10 w-full xl:w-auto">
                <div class="w-20 h-20 bg-[#1E293B] rounded-2xl flex items-center justify-center text-3xl text-blue-300 font-light border border-blue-500/30 shadow-inner overflow-hidden flex-shrink-0">
                    @if($employee->profile_image)
                        <img src="{{ asset('storage/'.$employee->profile_image) }}" class="w-full h-full object-cover">
                    @else
                        {{ substr($employee->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-[0.3em] mb-1 flex items-center gap-2">
                        <i class="fas fa-satellite-dish animate-pulse"></i> Visual Telemetry Archive
                    </p>
                    <h1 class="text-3xl md:text-4xl font-black text-white uppercase tracking-widest">{{ $employee->name }}</h1>
                </div>
            </div>

            {{-- Period Filter --}}
            <div class="flex gap-2 p-1.5 bg-[#1E293B]/80 backdrop-blur border border-slate-700 rounded-2xl relative z-10 w-full xl:w-auto justify-center overflow-x-auto">
                @foreach(['today' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $key => $label)
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $key, 'type' => $type]) }}"
                       class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all whitespace-nowrap
                       {{ $period == $key ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)]' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             2. METRICS & ANALYSIS DASHBOARD
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            
            {{-- Auto Captures --}}
            <div class="bg-[#0F172A] p-6 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden group hover:border-blue-500/30 transition-colors">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-blue-500 group-hover:scale-110 transition-transform"><i class="fas fa-camera"></i></div>
                <p class="text-[10px] text-blue-400 uppercase tracking-[0.2em] font-black mb-2 relative z-10">Auto Protocols</p>
                <h3 class="text-4xl font-black text-white relative z-10">{{ $totalAuto }} <span class="text-sm text-slate-500 font-normal">Scans</span></h3>
            </div>

            {{-- Admin Triggers --}}
            <div class="bg-[#0F172A] p-6 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden group hover:border-rose-500/30 transition-colors">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-rose-500 group-hover:scale-110 transition-transform"><i class="fas fa-crosshairs"></i></div>
                <p class="text-[10px] text-rose-400 uppercase tracking-[0.2em] font-black mb-2 relative z-10">Manual Overrides</p>
                <h3 class="text-4xl font-black text-white relative z-10">{{ $totalAdmin }} <span class="text-sm text-slate-500 font-normal">Pulses</span></h3>
            </div>

            {{-- AI Tools List --}}
            <div class="bg-[#0F172A] p-6 rounded-3xl border border-slate-800 shadow-xl overflow-hidden flex flex-col justify-between">
                <p class="text-[10px] text-indigo-400 uppercase tracking-[0.2em] font-black mb-4 flex items-center gap-2"><i class="fas fa-robot"></i> Neural / AI Usage</p>
                @if(count($aiUsage) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($aiUsage as $tool => $count)
                            <div class="bg-[#1E293B] border border-indigo-500/30 px-3 py-1.5 rounded-lg flex items-center justify-between gap-3 flex-1 min-w-[45%]">
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">{{ $tool }}</span>
                                <span class="text-[9px] bg-indigo-500/20 text-indigo-400 px-1.5 py-0.5 rounded font-bold">{{ $count }}x</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-2"><p class="text-[10px] text-slate-500 italic uppercase tracking-widest">No AI Activity</p></div>
                @endif
            </div>

            {{-- Social Media / Distractions --}}
            <div class="bg-[#0F172A] p-6 rounded-3xl border border-slate-800 shadow-xl overflow-hidden flex flex-col justify-between relative">
                @if(count($socialUsage) > 0)<div class="absolute inset-0 bg-rose-500/5 animate-pulse pointer-events-none"></div>@endif
                <p class="text-[10px] text-amber-500 uppercase tracking-[0.2em] font-black mb-4 flex items-center gap-2 relative z-10"><i class="fas fa-exclamation-triangle"></i> Distractions Detected</p>
                @if(count($socialUsage) > 0)
                    <div class="flex flex-wrap gap-2 relative z-10">
                        @foreach($socialUsage as $tool => $count)
                            <div class="bg-[#1E293B] border border-amber-500/30 px-3 py-1.5 rounded-lg flex items-center justify-between gap-3 flex-1 min-w-[45%]">
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">{{ $tool }}</span>
                                <span class="text-[9px] bg-amber-500/20 text-amber-500 px-1.5 py-0.5 rounded font-bold">{{ $count }}x</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-2 relative z-10"><p class="text-[10px] text-emerald-500/70 font-black uppercase tracking-widest flex items-center justify-center gap-2"><i class="fas fa-check-circle"></i> Clean Session</p></div>
                @endif
            </div>
            
        </div>

        {{-- ══════════════════════════════════════════════════════
             3. FILTER TABS & MAIN GALLERY GRID
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] rounded-3xl shadow-xl p-8 border border-slate-800 relative z-10">
            
            {{-- Pulse Type Filter --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-800">
                <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-images text-blue-500"></i> Signal Gallery
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $period, 'type' => 'all']) }}" class="px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all {{ $type == 'all' ? 'bg-[#1E293B] text-white border border-slate-600' : 'text-slate-500 hover:text-white' }}">All Signals</a>
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $period, 'type' => 'employee']) }}" class="px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all {{ $type == 'employee' ? 'bg-[#1E293B] text-blue-400 border border-blue-900' : 'text-slate-500 hover:text-white' }}">Auto Scans</a>
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $period, 'type' => 'admin']) }}" class="px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all {{ $type == 'admin' ? 'bg-[#1E293B] text-rose-400 border border-rose-900' : 'text-slate-500 hover:text-white' }}">Admin Triggers</a>
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5">
                @forelse($screenshots as $snap)
                <div id="pulse-card-{{ $snap->id }}" class="group relative bg-[#1E293B] border border-slate-700/50 rounded-2xl overflow-hidden hover:shadow-[0_0_20px_rgba(59,130,246,0.15)] hover:border-blue-500/50 transition-all duration-300">
                    
                    {{-- Badges --}}
                    @if($snap->triggered_by_admin)
                    <div class="absolute top-2 right-2 z-10">
                        <span class="bg-rose-500/90 backdrop-blur text-white text-[7px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-md border border-rose-400/50">Manual</span>
                    </div>
                    @else
                    <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="bg-blue-600/90 backdrop-blur text-white text-[7px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-md border border-blue-400/50">Auto</span>
                    </div>
                    @endif

                    {{-- Delete Button --}}
                    <div class="absolute top-2 left-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="deletePulse({{ $snap->id }})" class="w-7 h-7 rounded-lg bg-[#0F172A]/80 backdrop-blur border border-rose-500/30 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center shadow-lg">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </div>

                    {{-- Image --}}
                    <div class="relative h-32 overflow-hidden cursor-pointer bg-black"
                         onclick="viewFullImage('{{ asset('storage/' . $snap->image_path) }}', '{{ addslashes($snap->window_title ?? '') }}', {{ $snap->triggered_by_admin ? 'true' : 'false' }})">
                        <img src="{{ asset('storage/' . $snap->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-70 group-hover:opacity-100 mix-blend-screen">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-[#0F172A]/40">
                            <i class="fas fa-expand text-white text-lg"></i>
                        </div>
                    </div>

                    {{-- Info Footer --}}
                    <div class="p-3 bg-[#0F172A] border-t border-slate-800 relative z-20">
                        <p class="text-[9px] font-black text-blue-100 uppercase tracking-widest truncate mb-1" title="{{ $snap->window_title }}">
                            {{ $snap->window_title ?? 'Unknown Interface' }}
                        </p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest">
                            <i class="far fa-clock"></i> {{ $snap->created_at->format('H:i | d M') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center border-2 border-dashed border-slate-700 rounded-3xl bg-[#1E293B]/20">
                    <i class="fas fa-satellite-dish text-5xl text-slate-600 mb-4 block"></i>
                    <p class="text-slate-400 uppercase tracking-[0.3em] text-xs font-black">No Telemetry Signals Found for this Period.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-10 flex justify-center">
                {{ $screenshots->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     LIGHTBOX MODAL (CYBER DARK)
══════════════════════════════════════════════════════ --}}
<div id="lightbox" class="fixed inset-0 z-[9999] hidden bg-[#0B1120]/95 backdrop-blur-md flex flex-col items-center justify-center p-6 cursor-pointer" onclick="this.classList.add('hidden')">
    <div class="w-full max-w-6xl mb-4 flex justify-between items-center border-b border-slate-700 pb-4" onclick="event.stopPropagation()">
        <div>
            <p class="text-slate-400 font-bold text-[9px] uppercase tracking-[0.3em] mb-1">Signal Decrypted</p>
            <span id="lightbox-title" class="text-blue-300 font-black text-sm md:text-lg uppercase tracking-widest"></span>
        </div>
        <div class="flex items-center gap-4">
            <span id="lightbox-badge" class="font-black text-[9px] uppercase tracking-widest border px-3 py-1.5 rounded-lg"></span>
            <button onclick="document.getElementById('lightbox').classList.add('hidden')" class="w-10 h-10 rounded-full bg-slate-800 text-slate-400 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center border border-slate-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="relative p-2 bg-black rounded-2xl border border-slate-700 shadow-[0_0_50px_rgba(0,0,0,0.5)]" onclick="event.stopPropagation()">
        <img id="fullImg" src="" class="max-w-full max-h-[75vh] rounded-xl object-contain">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewFullImage(src, title, isAdmin) {
        document.getElementById('fullImg').src = src;
        document.getElementById('lightbox-title').innerText = title || 'NO TITLE DETECTED';
        
        const badge = document.getElementById('lightbox-badge');
        if(isAdmin) {
            badge.innerText = 'Manual Override';
            badge.className = 'font-black text-[9px] uppercase tracking-widest border px-3 py-1.5 rounded-lg bg-rose-500/10 text-rose-400 border-rose-500/30';
        } else {
            badge.innerText = 'Auto Protocol';
            badge.className = 'font-black text-[9px] uppercase tracking-widest border px-3 py-1.5 rounded-lg bg-blue-500/10 text-blue-400 border-blue-500/30';
        }
        
        document.getElementById('lightbox').classList.remove('hidden');
    }

    function deletePulse(id) {
        Swal.fire({
            title: 'PURGE DATA?',
            text: "Irreversible deletion of visual telemetry.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E11D48', // rose-600
            cancelButtonColor: '#475569',  // slate-600
            background: '#0F172A',
            color: '#F8FAFC',
            confirmButtonText: 'Execute Purge'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/screenshots/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                }).then(res => res.json()).then(data => {
                    if(data.success) {
                        const card = document.getElementById(`pulse-card-${id}`);
                        card.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => card.remove(), 300);
                        
                        Swal.fire({
                            title: 'Purged',
                            text: 'Telemetry deleted securely.',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#0F172A',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection