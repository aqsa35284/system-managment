@extends('layouts.admin')

@section('content')

{{-- ⭐ DATA PROCESSING FOR GRAPHS & LIVE ALERTS (For 100+ Employees) --}}
@php
    // Categorize data for 100+ employees
    $aiUsersList = [];
    $socialUsersList = [];
    $deptChartLabels = [];
    $deptActiveData = [];
    $deptIdleData = [];
    
    $tempDepts = [];

    foreach($employeeStatus ?? [] as $es) {
        $emp = $es['employee'];
        $deptName = $emp->department->name ?? 'General';
        
        // Initialize Department if not exists
        if(!isset($tempDepts[$deptName])) {
            $tempDepts[$deptName] = ['active' => 0, 'idle' => 0];
        }

        // Count Active vs Idle
        if($es['is_online']) $tempDepts[$deptName]['active']++;
        else $tempDepts[$deptName]['idle']++;

        // Catch Live AI & Social Users
        if(!empty($es['social_alert'])) {
            $socialUsersList[] = [
                'name' => $emp->name, 
                'dept' => $deptName, 
                'tools' => implode(', ', $es['ai_tools'] ?? ['SOCIAL MEDIA'])
            ];
        } elseif (!empty($es['ai_tools']) && count($es['ai_tools']) > 0) {
            $aiUsersList[] = [
                'name' => $emp->name, 
                'dept' => $deptName, 
                'tools' => implode(', ', $es['ai_tools'])
            ];
        }
    }

    // Format Data for Chart.js
    foreach($tempDepts as $dName => $counts) {
        $deptChartLabels[] = $dName;
        $deptActiveData[] = $counts['active'];
        $deptIdleData[] = $counts['idle'];
    }
@endphp

<div class="p-4 md:p-8 bg-[#0B1120] min-h-screen font-sans text-slate-200">
    <div class="max-w-[1800px] mx-auto space-y-6">

        {{-- ══════════════════════════════════════════════════════
             1. COMMAND HEADER & GLOBAL FILTERS
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end pb-4 border-b border-slate-800 gap-6">
            <div>
                <p class="text-[10px] text-blue-500 font-black tracking-[0.3em] uppercase mb-1 flex items-center gap-2">
                    <i class="fas fa-globe animate-pulse"></i> Global Fleet Overview
                </p>
                <h1 class="text-3xl md:text-4xl font-black text-white uppercase tracking-widest">Command Center</h1>
            </div>
            
            <div class="flex gap-2 bg-[#1E293B]/80 backdrop-blur p-1.5 rounded-2xl border border-slate-700 shadow-xl overflow-x-auto w-full xl:w-auto">
                @foreach(['today' => 'Today', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $pk => $pl)
                <a href="{{ route('admin.dashboard', ['period' => $pk]) }}"
                   class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
                   {{ (request('period', 'today') == $pk) ? 'bg-blue-600 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)]' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' }}">
                    {{ $pl }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             2. TOP LEVEL METRICS (100+ Scale)
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#0F172A] border border-blue-900/30 rounded-2xl p-5 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 text-blue-500 group-hover:scale-110 transition-transform"><i class="fas fa-users"></i></div>
                <p class="text-[9px] text-blue-400 font-black uppercase tracking-widest mb-1">Total Workforce</p>
                <h3 class="text-3xl font-black text-white">{{ $totalEmployees ?? 0 }}</h3>
            </div>
            <div class="bg-[#0F172A] border border-emerald-900/30 rounded-2xl p-5 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 text-emerald-500 group-hover:scale-110 transition-transform"><i class="fas fa-network-wired"></i></div>
                <p class="text-[9px] text-emerald-400 font-black uppercase tracking-widest mb-1">Active Nodes</p>
                <h3 class="text-3xl font-black text-white">{{ $onlineCount ?? 0 }}</h3>
            </div>
            <div class="bg-[#0F172A] border border-amber-900/30 rounded-2xl p-5 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 text-amber-500 group-hover:scale-110 transition-transform"><i class="fas fa-exclamation-triangle"></i></div>
                <p class="text-[9px] text-amber-400 font-black uppercase tracking-widest mb-1">Distraction Alerts</p>
                <h3 class="text-3xl font-black {{ count($socialUsersList) > 0 ? 'text-amber-400 animate-pulse' : 'text-white' }}">{{ count($socialUsersList) }}</h3>
            </div>
            <div class="bg-[#0F172A] border border-indigo-900/30 rounded-2xl p-5 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 text-indigo-500 group-hover:scale-110 transition-transform"><i class="fas fa-robot"></i></div>
                <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest mb-1">AI Co-Pilots Active</p>
                <h3 class="text-3xl font-black text-white">{{ count($aiUsersList) }}</h3>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             3. VISUAL GRAPHS & LIVE INTERCEPTS (NEW)
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Department Active Status Graph --}}
            <div class="lg:col-span-2 bg-[#0F172A] border border-slate-800 rounded-3xl p-6 shadow-xl relative">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-500"></i> Department Live Status
                </h3>
                <div class="h-64 w-full">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>

            {{-- Live AI & Social Feed --}}
            <div class="bg-[#0F172A] border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col h-full max-h-[340px]">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <i class="fas fa-radar text-rose-500 animate-pulse"></i> Live Detections
                </h3>
                <div class="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                    
                    {{-- Social Media Violations First --}}
                    @forelse($socialUsersList as $alert)
                    <div class="bg-rose-500/10 border border-rose-500/20 p-3 rounded-xl flex justify-between items-center group">
                        <div>
                            <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest">{{ $alert['name'] }}</p>
                            <p class="text-[8px] font-bold text-slate-400 uppercase">{{ $alert['dept'] }}</p>
                        </div>
                        <span class="bg-rose-500 text-white text-[8px] font-black uppercase px-2 py-1 rounded shadow-md group-hover:scale-105 transition-transform">
                            {{ $alert['tools'] }}
                        </span>
                    </div>
                    @empty
                    @endforelse

                    {{-- AI Tool Usage Second --}}
                    @forelse($aiUsersList as $ai)
                    <div class="bg-indigo-500/5 border border-indigo-500/20 p-3 rounded-xl flex justify-between items-center group">
                        <div>
                            <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest">{{ $ai['name'] }}</p>
                            <p class="text-[8px] font-bold text-slate-500 uppercase">{{ $ai['dept'] }}</p>
                        </div>
                        <span class="bg-indigo-600/30 text-indigo-300 border border-indigo-500/50 text-[8px] font-black uppercase px-2 py-1 rounded shadow-md group-hover:scale-105 transition-transform">
                            {{ $ai['tools'] }}
                        </span>
                    </div>
                    @empty
                    @endforelse

                    @if(count($socialUsersList) == 0 && count($aiUsersList) == 0)
                    <div class="h-full flex flex-col items-center justify-center text-center opacity-50 pt-10">
                        <i class="fas fa-shield-check text-4xl text-emerald-500 mb-3"></i>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Clear Radar</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             4. HIGH-DENSITY OPERATOR STATUS MATRIX
        ══════════════════════════════════════════════════════ --}}
        <div class="bg-[#0F172A] rounded-3xl shadow-xl border border-slate-800 overflow-hidden flex flex-col h-full max-h-[800px]">
            
            {{-- Matrix Header & Search --}}
            <div class="px-6 py-5 border-b border-slate-800 bg-[#1E293B]/30 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-users-cog text-blue-500"></i> Operator Status Matrix
                    </h2>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Real-time telemetry for {{ $totalEmployees ?? 0 }} Nodes</p>
                </div>
                
                {{-- Smart Search for 100+ users --}}
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                    <input type="text" id="matrixSearch" placeholder="Search Name, Dept or Tool..."
                           class="w-full bg-[#1E293B] border border-blue-900/50 rounded-xl pl-10 pr-4 py-2.5 text-[10px] font-black text-white uppercase tracking-widest outline-none focus:border-blue-500 transition-all placeholder-slate-500">
                </div>
            </div>

            {{-- Scrollable Matrix Body --}}
            <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1">
                <table class="w-full min-w-[1200px] text-left border-collapse">
                    <thead class="bg-[#1E293B]/80 backdrop-blur sticky top-0 z-20">
                        <tr class="border-b border-slate-800">
                            <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Operator Identity</th>
                            <th class="px-4 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Status</th>
                            <th class="px-4 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Active Tools</th>
                            <th class="px-4 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Work Time</th>
                            <th class="px-4 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Live Preview</th>
                            <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Command</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50" id="matrixBody">
                        @foreach($employeeStatus ?? [] as $es)
                        @php
                            $emp = $es['employee'];
                            $dept = $emp->department->name ?? 'General';
                            $wh  = floor($es['work_seconds'] / 3600);
                            $wm  = floor(($es['work_seconds'] % 3600) / 60);
                            $toolsStr = implode(' ', $es['ai_tools'] ?? []);
                            $searchString = strtolower($emp->name . ' ' . $dept . ' ' . $toolsStr);
                        @endphp
                        <tr class="hover:bg-[#1E293B]/40 transition-colors group matrix-row" data-search="{{ $searchString }}">
                            
                            {{-- Identity & Department --}}
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-9 h-9 bg-blue-900/50 rounded-xl flex items-center justify-center text-blue-300 text-xs font-black border border-blue-800">
                                            @if($emp->profile_image)
                                                <img src="{{ asset('storage/'.$emp->profile_image) }}" class="w-full h-full object-cover rounded-xl">
                                            @else
                                                {{ substr($emp->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full border-2 border-[#0F172A] {{ $es['is_online'] ? 'bg-emerald-500' : 'bg-slate-500' }}"></div>
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-white font-black uppercase tracking-wider group-hover:text-blue-400 transition-colors">{{ $emp->name }}</p>
                                        <p class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $dept }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Connection Status --}}
                            <td class="px-4 py-3 text-center">
                                @if($es['is_online'])
                                    <span class="inline-flex items-center gap-1.5 text-[8px] font-black text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded bg-emerald-500/10 uppercase tracking-[0.2em] shadow-sm">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Sync
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[8px] font-black text-slate-400 border border-slate-600/30 px-2.5 py-1 rounded bg-slate-800 uppercase tracking-[0.2em]">
                                        <span class="w-1.5 h-1.5 bg-slate-500 rounded-full"></span> Idle
                                    </span>
                                @endif
                            </td>

                            {{-- Active Tools (Tags) --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-1 max-w-[150px] mx-auto">
                                    @if($es['social_alert'])
                                        <span class="text-[7px] text-white bg-rose-600 px-1.5 py-0.5 rounded shadow border border-rose-500 uppercase font-black tracking-widest">
                                            <i class="fas fa-exclamation-circle"></i> Distracted
                                        </span>
                                    @elseif(count($es['ai_tools'] ?? []) > 0)
                                        @foreach(array_slice($es['ai_tools'], 0, 2) as $tool)
                                            <span class="text-[7px] font-black text-indigo-300 bg-indigo-500/20 border border-indigo-500/30 px-1.5 py-0.5 rounded shadow uppercase tracking-widest">{{ $tool }}</span>
                                        @endforeach
                                        @if(count($es['ai_tools']) > 2)
                                            <span class="text-[7px] font-black text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded shadow">...</span>
                                        @endif
                                    @else
                                        <span class="text-[8px] font-bold text-slate-600 uppercase tracking-widest">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Work Time Bar --}}
                            <td class="px-4 py-3 text-center">
                                <p class="text-[10px] font-black {{ $wh >= 8 ? 'text-emerald-400' : ($wh > 0 ? 'text-white' : 'text-slate-600') }}">{{ $wh }}h {{ $wm }}m</p>
                                <div class="w-16 h-1 bg-slate-800 rounded-full mx-auto mt-1 overflow-hidden">
                                    <div class="h-full {{ $wh >= 8 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ min(($wh / 9) * 100, 100) }}%"></div>
                                </div>
                            </td>

                            {{-- Thumbnail Preview --}}
                            <td class="px-4 py-3 text-center">
                                @if($es['latest_snap'])
                                    <div class="relative w-20 h-10 mx-auto rounded border border-slate-700 overflow-hidden cursor-pointer group/img"
                                         onclick="previewShot('{{ asset('storage/'.$es['latest_snap']->image_path) }}', '{{ addslashes($es['latest_snap']->window_title ?? '') }}')">
                                        <img src="{{ asset('storage/'.$es['latest_snap']->image_path) }}" class="w-full h-full object-cover opacity-60 group-hover/img:opacity-100 group-hover/img:scale-110 transition-all">
                                        @if($es['latest_snap']->triggered_by_admin)
                                            <div class="absolute top-0 right-0 bg-rose-500 text-white text-[5px] font-black uppercase px-1 py-0.5">ADM</div>
                                        @endif
                                    </div>
                                @else
                                    <div class="w-20 h-10 mx-auto rounded border border-slate-700 bg-slate-800/50 flex items-center justify-center opacity-50">
                                        <i class="fas fa-video-slash text-xs text-slate-500"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- Action Buttons --}}
                            <td class="px-6 py-3 text-right">
                                <div class="flex gap-1.5 justify-end">
                                    <button onclick="triggerPulse({{ $emp->id }}, '{{ addslashes($emp->name) }}')" class="w-7 h-7 rounded border border-rose-500/30 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center shadow" title="Trigger Pulse">
                                        <i class="fas fa-crosshairs text-[10px]"></i>
                                    </button>
                                    <a href="{{ route('admin.employees.pulses', $emp->id) }}" class="w-7 h-7 rounded border border-blue-500/30 bg-blue-500/10 text-blue-400 hover:bg-blue-600 hover:text-white transition-colors flex items-center justify-center shadow" title="Archive">
                                        <i class="fas fa-images text-[10px]"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.show', $emp->id) }}" class="w-7 h-7 rounded border border-slate-600 bg-slate-800 text-slate-300 hover:bg-slate-600 hover:text-white transition-colors flex items-center justify-center shadow" title="Dossier">
                                        <i class="fas fa-user-shield text-[10px]"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SCRIPTS & CHART.JS
══════════════════════════════════════════════════════ --}}
<style>
    /* Custom Scrollbar for dense lists */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #0F172A; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Operator Matrix Smart Search
    document.getElementById('matrixSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.matrix-row').forEach(row => {
            if (row.dataset.search.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // 2. Trigger Pulse Logic (Kept Intact)
    function triggerPulse(userId, name) {
        Swal.fire({
            title: 'INITIATING SCAN',
            html: `<p style="font-size:10px;color:#64748b;font-weight:bold;text-transform:uppercase;letter-spacing:1px">Target: ${name}</p>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#0F172A',
            color: '#fff',
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/admin/employees/${userId}/trigger-screenshot`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).then(r => r.json()).then(data => {
            Swal.fire({
                title: 'SIGNAL SENT',
                text: 'Awaiting telemetry response. Check archive in 5 secs.',
                icon: 'success',
                background: '#0F172A',
                color: '#fff',
                confirmButtonColor: '#3B82F6',
            });
        }).catch(() => { Swal.fire({ title: 'Error', icon: 'error' }); });
    }

    // 3. Simple Lightbox (To Avoid Clutter)
    function previewShot(src, title) {
        Swal.fire({
            html: `<img src="${src}" style="width:100%; border-radius:8px; margin-bottom:10px;"><p style="font-size:10px; font-weight:bold; color:#60a5fa; text-transform:uppercase; letter-spacing:1px;">${title}</p>`,
            background: '#0F172A',
            showConfirmButton: false,
            width: '800px',
            showCloseButton: true
        });
    }

    // 4. Initialize Department Chart.js
    const ctx = document.getElementById('deptChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($deptChartLabels) !!},
            datasets: [
                {
                    label: 'Active Operators',
                    data: {!! json_encode($deptActiveData) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.8)', // Emerald
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Idle / Offline',
                    data: {!! json_encode($deptIdleData) !!},
                    backgroundColor: 'rgba(51, 65, 85, 0.8)', // Slate
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 9, weight: 'bold' } } },
                y: { stacked: true, grid: { color: '#1e293b', drawBorder: false }, ticks: { color: '#94a3b8', stepSize: 5 } }
            },
            plugins: {
                legend: { position: 'top', align: 'end', labels: { color: '#cbd5e1', usePointStyle: true, boxWidth: 6, font: { size: 10, weight: 'bold' } } },
                tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', titleColor: '#fff', bodyColor: '#cbd5e1', borderColor: '#334155', borderWidth: 1 }
            }
        }
    });
</script>
@endsection