@extends('layouts.admin')

@section('content')

<link rel="icon" type="image/png" href="{{ asset('storage/logo/Favicon.png') }}">

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

<div class="p-6 md:p-10 bg-[#010717] min-h-screen font-sans text-[#eaeef2]">
    <div class="max-w-[1600px] mx-auto space-y-8">
        
        {{-- ⭐ BACK BUTTON --}}
        <div class="flex justify-start">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] text-[10px] font-black uppercase tracking-widest transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>

        {{-- ══════════════════════════════════════════════════════
             1. HEADER & TIMELINE FILTER (GOLD & DARK THEME)
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#010717] rounded-[2rem] shadow-[0_0_40px_rgba(0,0,0,0.5)] p-8 flex flex-col xl:flex-row justify-between items-center xl:items-end border border-[#d2a63d]/20 relative overflow-hidden gap-8">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#d2a63d]/5 rounded-full blur-3xl translate-x-10 -translate-y-20 pointer-events-none"></div>
            
            <div class="flex items-center gap-6 relative z-10 w-full xl:w-auto">
                <div class="w-20 h-20 bg-[#010717] rounded-2xl flex items-center justify-center text-3xl text-[#d2a63d] font-light border border-[#d2a63d]/30 shadow-inner overflow-hidden flex-shrink-0">
                    @if($employee->profile_image)
                        <img src="{{ asset('storage/'.$employee->profile_image) }}" class="w-full h-full object-cover">
                    @else
                        {{ substr($employee->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <p class="text-[10px] text-[#d2a63d] font-bold uppercase tracking-[0.3em] mb-1 flex items-center gap-2">
                        <i class="fas fa-satellite-dish animate-pulse"></i> Visual Telemetry Archive
                    </p>
                    <h1 class="text-3xl md:text-4xl font-black text-[#eaeef2] uppercase tracking-widest">{{ $employee->name }}</h1>
                </div>
            </div>

            {{-- Period Filter --}}
            <div class="flex gap-2 p-1.5 bg-[#010717]/80 backdrop-blur border border-[#d2a63d]/20 rounded-2xl relative z-10 w-full xl:w-auto justify-center overflow-x-auto">
                @foreach(['today' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $key => $label)
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $key, 'type' => $type]) }}"
                       class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all whitespace-nowrap
                       {{ $period == $key ? 'bg-[#d2a63d] text-[#010717] shadow-[0_0_15px_rgba(210,166,61,0.4)]' : 'text-[#eaeef2]/50 hover:text-[#eaeef2] hover:bg-[#d2a63d]/10' }}">
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
            <div class="bg-[#010717] p-6 rounded-3xl border border-[#d2a63d]/20 shadow-xl relative overflow-hidden group hover:border-[#d2a63d]/50 transition-colors">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-[#d2a63d] group-hover:scale-110 transition-transform"><i class="fas fa-camera"></i></div>
                <p class="text-[10px] text-[#d2a63d] uppercase tracking-[0.2em] font-black mb-2 relative z-10">Auto Protocols</p>
                <h3 class="text-4xl font-black text-[#eaeef2] relative z-10">{{ $totalAuto }} <span class="text-sm text-[#eaeef2]/50 font-normal">Scans</span></h3>
            </div>

            {{-- Admin Triggers --}}
            <div class="bg-[#010717] p-6 rounded-3xl border border-[#d2a63d]/20 shadow-xl relative overflow-hidden group hover:border-[#d2a63d]/50 transition-colors">
                <div class="absolute -right-6 -top-6 text-7xl opacity-5 text-[#d2a63d] group-hover:scale-110 transition-transform"><i class="fas fa-crosshairs"></i></div>
                <p class="text-[10px] text-[#d2a63d] uppercase tracking-[0.2em] font-black mb-2 relative z-10">Manual Overrides</p>
                <h3 class="text-4xl font-black text-[#eaeef2] relative z-10">{{ $totalAdmin }} <span class="text-sm text-[#eaeef2]/50 font-normal">Pulses</span></h3>
            </div>

            {{-- AI Tools List --}}
            <div class="bg-[#010717] p-6 rounded-3xl border border-[#d2a63d]/20 shadow-xl overflow-hidden flex flex-col justify-between">
                <p class="text-[10px] text-[#d2a63d] uppercase tracking-[0.2em] font-black mb-4 flex items-center gap-2"><i class="fas fa-robot"></i> Neural / AI Usage</p>
                @if(count($aiUsage) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($aiUsage as $tool => $count)
                            <div class="bg-[#d2a63d]/5 border border-[#d2a63d]/30 px-3 py-1.5 rounded-lg flex items-center justify-between gap-3 flex-1 min-w-[45%]">
                                <span class="text-[9px] font-black text-[#eaeef2] uppercase tracking-widest">{{ $tool }}</span>
                                <span class="text-[9px] bg-[#d2a63d]/20 text-[#d2a63d] px-1.5 py-0.5 rounded font-bold">{{ $count }}x</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-2"><p class="text-[10px] text-[#eaeef2]/50 italic uppercase tracking-widest">No AI Activity</p></div>
                @endif
            </div>

            {{-- Social Media / Distractions --}}
            <div class="bg-[#010717] p-6 rounded-3xl border border-[#d2a63d]/20 shadow-xl overflow-hidden flex flex-col justify-between relative">
                @if(count($socialUsage) > 0)<div class="absolute inset-0 bg-[#d2a63d]/5 animate-pulse pointer-events-none"></div>@endif
                <p class="text-[10px] text-[#d2a63d] uppercase tracking-[0.2em] font-black mb-4 flex items-center gap-2 relative z-10"><i class="fas fa-exclamation-triangle"></i> Distractions Detected</p>
                @if(count($socialUsage) > 0)
                    <div class="flex flex-wrap gap-2 relative z-10">
                        @foreach($socialUsage as $tool => $count)
                            <div class="bg-[#d2a63d]/5 border border-[#d2a63d]/30 px-3 py-1.5 rounded-lg flex items-center justify-between gap-3 flex-1 min-w-[45%]">
                                <span class="text-[9px] font-black text-[#eaeef2] uppercase tracking-widest">{{ $tool }}</span>
                                <span class="text-[9px] bg-[#d2a63d]/20 text-[#d2a63d] px-1.5 py-0.5 rounded font-bold">{{ $count }}x</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-2 relative z-10"><p class="text-[10px] text-[#d2a63d]/70 font-black uppercase tracking-widest flex items-center justify-center gap-2"><i class="fas fa-check-circle"></i> Clean Session</p></div>
                @endif
            </div>
            
        </div>

        {{-- ══════════════════════════════════════════════════════
             3. FILTER TABS & MAIN GALLERY GRID
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#010717] rounded-3xl shadow-xl p-8 border border-[#d2a63d]/20 relative z-10">
            
            {{-- Pulse Type Filter --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-6 border-b border-[#d2a63d]/10">
                <h3 class="text-sm font-black text-[#eaeef2] uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-images text-[#d2a63d]"></i> Signal Gallery
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $period, 'type' => 'all']) }}" class="px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all {{ $type == 'all' ? 'bg-[#d2a63d] text-[#010717] border border-[#d2a63d]' : 'text-[#eaeef2]/50 hover:text-[#eaeef2] hover:bg-[#d2a63d]/10' }}">All Signals</a>
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $period, 'type' => 'employee']) }}" class="px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all {{ $type == 'employee' ? 'bg-[#d2a63d] text-[#010717] border border-[#d2a63d]' : 'text-[#eaeef2]/50 hover:text-[#eaeef2] hover:bg-[#d2a63d]/10' }}">Auto Scans</a>
                    <a href="{{ route('admin.employees.pulses', [$employee->id, 'period' => $period, 'type' => 'admin']) }}" class="px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all {{ $type == 'admin' ? 'bg-[#d2a63d] text-[#010717] border border-[#d2a63d]' : 'text-[#eaeef2]/50 hover:text-[#eaeef2] hover:bg-[#d2a63d]/10' }}">Admin Triggers</a>
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5">
                @forelse($screenshots as $snap)
                <div id="pulse-card-{{ $snap->id }}" class="group relative bg-[#010717] border border-[#d2a63d]/20 rounded-2xl overflow-hidden hover:shadow-[0_0_20px_rgba(210,166,61,0.15)] hover:border-[#d2a63d]/50 transition-all duration-300">
                    
                    {{-- Badges --}}
                    @if($snap->triggered_by_admin)
                    <div class="absolute top-2 right-2 z-10">
                        <span class="bg-[#d2a63d]/90 backdrop-blur text-[#010717] text-[7px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-md border border-[#d2a63d]/50">Manual</span>
                    </div>
                    @else
                    <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="bg-[#010717]/90 backdrop-blur text-[#eaeef2] text-[7px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-md border border-[#eaeef2]/50">Auto</span>
                    </div>
                    @endif

                    {{-- Delete Button (Kept red for destructive action UX) --}}
                    <div class="absolute top-2 left-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="deletePulse({{ $snap->id }})" class="w-7 h-7 rounded-lg bg-[#010717]/80 backdrop-blur border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center shadow-lg">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </div>

                    {{-- Image --}}
                    <div class="relative h-32 overflow-hidden cursor-pointer bg-black"
                         onclick="viewFullImage('{{ asset('storage/' . $snap->image_path) }}', '{{ addslashes($snap->window_title ?? '') }}', {{ $snap->triggered_by_admin ? 'true' : 'false' }})">
                        <img src="{{ asset('storage/' . $snap->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-70 group-hover:opacity-100 mix-blend-screen">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-[#010717]/40">
                            <i class="fas fa-expand text-[#d2a63d] text-lg"></i>
                        </div>
                    </div>

                    {{-- Info Footer --}}
                    <div class="p-3 bg-[#010717] border-t border-[#d2a63d]/20 relative z-20">
                        <p class="text-[9px] font-black text-[#eaeef2] uppercase tracking-widest truncate mb-1" title="{{ $snap->window_title }}">
                            {{ $snap->window_title ?? 'Unknown Interface' }}
                        </p>
                        <p class="text-[8px] text-[#eaeef2]/50 font-bold uppercase tracking-widest">
                            <i class="far fa-clock text-[#d2a63d]"></i> {{ $snap->created_at->format('H:i | d M') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center border-2 border-dashed border-[#d2a63d]/20 rounded-3xl bg-[#d2a63d]/5">
                    <i class="fas fa-satellite-dish text-5xl text-[#d2a63d]/40 mb-4 block"></i>
                    <p class="text-[#d2a63d]/60 uppercase tracking-[0.3em] text-xs font-black">No Telemetry Signals Found for this Period.</p>
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
     LIGHTBOX MODAL (THEMED)
══════════════════════════════════════════════════════ --}}
<div id="lightbox" class="fixed inset-0 z-[9999] hidden bg-[#010717]/95 backdrop-blur-md flex flex-col items-center justify-center p-6 cursor-pointer" onclick="this.classList.add('hidden')">
    <div class="w-full max-w-6xl mb-4 flex justify-between items-center border-b border-[#d2a63d]/20 pb-4" onclick="event.stopPropagation()">
        <div>
            <p class="text-[#eaeef2]/50 font-bold text-[9px] uppercase tracking-[0.3em] mb-1">Signal Decrypted</p>
            <span id="lightbox-title" class="text-[#d2a63d] font-black text-sm md:text-lg uppercase tracking-widest"></span>
        </div>
        <div class="flex items-center gap-4">
            <span id="lightbox-badge" class="font-black text-[9px] uppercase tracking-widest border px-3 py-1.5 rounded-lg"></span>
            <button onclick="document.getElementById('lightbox').classList.add('hidden')" class="w-10 h-10 rounded-full bg-[#010717] text-[#eaeef2]/50 hover:bg-red-500 hover:text-[#eaeef2] transition-colors flex items-center justify-center border border-[#d2a63d]/30">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="relative p-2 bg-black rounded-2xl border border-[#d2a63d]/30 shadow-[0_0_50px_rgba(0,0,0,0.5)]" onclick="event.stopPropagation()">
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
            badge.className = 'font-black text-[9px] uppercase tracking-widest border px-3 py-1.5 rounded-lg bg-[#d2a63d] text-[#010717] border-[#d2a63d]';
        } else {
            badge.innerText = 'Auto Protocol';
            badge.className = 'font-black text-[9px] uppercase tracking-widest border px-3 py-1.5 rounded-lg bg-[#010717] text-[#eaeef2] border-[#eaeef2]/30';
        }
        
        document.getElementById('lightbox').classList.remove('hidden');
    }

    function deletePulse(id) {
        Swal.fire({
            title: 'PURGE DATA?',
            text: "Irreversible deletion of visual telemetry.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d2a63d', // Golden accent
            cancelButtonColor: '#0a1225',  // Dark accent
            background: '#010717',
            color: '#eaeef2',
            confirmButtonText: '<span style="color: #010717; font-weight: bold;">Execute Purge</span>'
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
                            background: '#010717',
                            color: '#d2a63d'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection