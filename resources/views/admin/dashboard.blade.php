@extends('layouts.admin')

@section('content')


 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

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

<div class="p-3 md:p-6 lg:p-8 min-h-screen font-sans" style="background-color: #010717; color: #eaeef2;">
    <div class="max-w-[1800px] mx-auto space-y-6">

        {{-- ══════════════════════════════════════════════════════
             1. COMMAND HEADER, LOGO & GLOBAL FILTERS (Responsive)
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center pb-4 gap-6" style="border-bottom: 1px solid rgba(210, 166, 61, 0.2);">
            
            {{-- Logo & Title Area --}}
            <div class="flex items-center gap-4 w-full lg:w-auto">
                <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg" style="background-color: rgba(210, 166, 61, 0.15); border: 1px solid rgba(210, 166, 61, 0.35);">
                    <img src="{{ asset('storage/logo/Favicon.png') }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" class="w-full h-full object-contain p-2 rounded-xl" alt="Logo">
                    <i class="fas fa-shield-alt text-2xl md:text-3xl" style="display: none; color: #d2a63d;"></i>
                </div>
                
                <div>
                    <p class="text-[9px] md:text-[10px] font-black tracking-[0.3em] uppercase mb-1 flex items-center gap-2" style="color: #d2a63d;">
                        <i class="fas fa-globe animate-pulse"></i> Global Employee Overview
                    </p>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black uppercase tracking-widest" style="color: #eaeef2;">Admin Center</h1>
                </div>
            </div>
            
            <div class="flex gap-2 backdrop-blur px-6 py-3 rounded-2xl shadow-xl w-full lg:w-auto items-center justify-center" style="background-color: rgba(210, 166, 61, 0.1); border: 1px solid rgba(210, 166, 61, 0.25);">
                <span class="text-[10px] md:text-xs font-black uppercase tracking-widest flex items-center gap-2" style="color: #d2a63d;">
                    <i class="fas fa-clock"></i> Daily Employee Active
                </span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             2. TOP LEVEL METRICS (Grid Auto-adjusting)
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total Workforce --}}
            <div class="rounded-2xl p-5 shadow-lg relative overflow-hidden group" style="background-color: #05101f; border: 1px solid rgba(210, 166, 61, 0.2);">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"><i class="fas fa-users"></i></div>
                <p class="text-[9px] font-black uppercase tracking-widest mb-1" style="color: #d2a63d;">Total Workforce</p>
                <h3 class="text-3xl font-black" style="color: #eaeef2;">{{ $totalEmployees ?? 0 }}</h3>
            </div>

            {{-- Active Nodes --}}
            <div class="rounded-2xl p-5 shadow-lg relative overflow-hidden group" style="background-color: #05101f; border: 1px solid rgba(29, 209, 130, 0.2);">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #1dd182;"><i class="fas fa-network-wired"></i></div>
                <p class="text-[9px] font-black uppercase tracking-widest mb-1" style="color: #1dd182;">Active Nodes</p>
                <h3 class="text-3xl font-black" style="color: #eaeef2;">{{ $onlineCount ?? 0 }}</h3>
            </div>

            {{-- Distraction Alerts --}}
            <div class="rounded-2xl p-5 shadow-lg relative overflow-hidden group" style="background-color: #05101f; border: 1px solid rgba(239, 68, 68, 0.2);">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i></div>
                <p class="text-[9px] font-black uppercase tracking-widest mb-1" style="color: #ef4444;">Distraction Alerts</p>
                <h3 class="text-3xl font-black {{ count($socialUsersList) > 0 ? 'animate-pulse' : '' }}" style="color: {{ count($socialUsersList) > 0 ? '#ef4444' : '#eaeef2' }};">{{ count($socialUsersList) }}</h3>
            </div>

            {{-- AI Co-Pilots Active --}}
            <div class="rounded-2xl p-5 shadow-lg relative overflow-hidden group" style="background-color: #05101f; border: 1px solid rgba(210, 166, 61, 0.2);">
                <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 group-hover:scale-110 transition-transform" style="color: #d2a63d;"><i class="fas fa-robot"></i></div>
                <p class="text-[9px] font-black uppercase tracking-widest mb-1" style="color: #d2a63d;">AI Co-Pilots Active</p>
                <h3 class="text-3xl font-black" style="color: #eaeef2;">{{ count($aiUsersList) }}</h3>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             3. VISUAL GRAPHS & LIVE INTERCEPTS (Responsive Stack)
        ══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Department Active Status Graph --}}
            <div class="lg:col-span-2 rounded-3xl p-4 md:p-6 shadow-xl relative min-h-[300px]" style="background-color: #05101f; border: 1px solid rgba(210, 166, 61, 0.15);">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 flex items-center gap-2" style="color: #eaeef2;">
                    <i class="fas fa-chart-bar" style="color: #d2a63d;"></i> Department Live Status
                </h3>
                <div class="h-[250px] md:h-64 w-full">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>

            {{-- Live AI & Social Feed --}}
            <div class="rounded-3xl p-4 md:p-6 shadow-xl flex flex-col h-[350px] lg:h-full max-h-[400px] lg:max-h-[340px]" style="background-color: #05101f; border: 1px solid rgba(210, 166, 61, 0.15);">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 flex items-center gap-2" style="color: #eaeef2;">
                    <i class="fas fa-radar animate-pulse" style="color: #ef4444;"></i> Live Detections
                </h3>
                <div class="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                    
                    @forelse($socialUsersList as $alert)
                    <div class="p-3 rounded-xl flex justify-between items-center group" style="background-color: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2);">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] font-black uppercase tracking-widest truncate" style="color: #ef4444;">{{ $alert['name'] }}</p>
                            <p class="text-[8px] font-bold uppercase truncate" style="color: #8a9bb0;">{{ $alert['dept'] }}</p>
                        </div>
                        <span class="text-white text-[7px] md:text-[8px] font-black uppercase px-2 py-1 rounded shadow-md group-hover:scale-105 transition-transform whitespace-nowrap" style="background-color: #ef4444;">
                            {{ $alert['tools'] }}
                        </span>
                    </div>
                    @empty
                    @endforelse

                    @forelse($aiUsersList as $ai)
                    <div class="p-3 rounded-xl flex justify-between items-center group" style="background-color: rgba(210, 166, 61, 0.06); border: 1px solid rgba(210, 166, 61, 0.2);">
                        <div class="min-w-0 pr-2">
                            <p class="text-[10px] font-black uppercase tracking-widest truncate" style="color: #d2a63d;">{{ $ai['name'] }}</p>
                            <p class="text-[8px] font-bold uppercase truncate" style="color: #8a9bb0;">{{ $ai['dept'] }}</p>
                        </div>
                        <span class="text-[7px] md:text-[8px] font-black uppercase px-2 py-1 rounded shadow-md group-hover:scale-105 transition-transform whitespace-nowrap" style="background-color: rgba(210, 166, 61, 0.15); color: #d2a63d; border: 1px solid rgba(210, 166, 61, 0.35);">
                            {{ $ai['tools'] }}
                        </span>
                    </div>
                    @empty
                    @endforelse

                    @if(count($socialUsersList) == 0 && count($aiUsersList) == 0)
                    <div class="h-full flex flex-col items-center justify-center text-center opacity-50 pt-6">
                        <i class="fas fa-shield-check text-3xl md:text-4xl mb-3" style="color: #1dd182;"></i>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em]" style="color: #8a9bb0;">Clear Radar</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             4. HIGH-DENSITY OPERATOR STATUS MATRIX (Scrollable)
        ══════════════════════════════════════════════════════ --}}
        <div class="rounded-3xl shadow-xl overflow-hidden flex flex-col h-full max-h-[800px]" style="background-color: #05101f; border: 1px solid rgba(210, 166, 61, 0.15);">
            
            <div class="px-4 md:px-6 py-4 md:py-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4" style="border-bottom: 1px solid rgba(210, 166, 61, 0.15); background-color: rgba(210, 166, 61, 0.05);">
                <div class="w-full md:w-auto">
                    <h2 class="text-xs md:text-sm font-black uppercase tracking-widest flex items-center gap-2" style="color: #eaeef2;">
                        <i class="fas fa-users-cog" style="color: #d2a63d;"></i> Operator Matrix
                    </h2>
                    <p class="text-[8px] md:text-[9px] font-bold uppercase tracking-widest mt-1" style="color: #8a9bb0;">Real-time telemetry for {{ $totalEmployees ?? 0 }} Nodes</p>
                </div>
                
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-xs" style="color: #d2a63d;"></i>
                    <input type="text" id="matrixSearch" placeholder="Search Name, Dept or Tool..."
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-[10px] font-black uppercase tracking-widest outline-none transition-all placeholder-slate-500"
                           style="background-color: #010717; border: 1px solid rgba(210, 166, 61, 0.25); color: #eaeef2;"
                           onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.25)'">
                </div>
            </div>

            <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 -webkit-overflow-scrolling-touch">
                <table class="w-full min-w-[900px] md:min-w-[1200px] text-left border-collapse">
                    <thead class="sticky top-0 z-20 shadow-sm" style="background-color: rgba(1, 7, 23, 0.95);">
                        <tr style="border-bottom: 1px solid rgba(210, 166, 61, 0.15);">
                            <th class="px-4 md:px-6 py-4 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] whitespace-nowrap" style="color: #8a9bb0;">Operator Identity</th>
                            <th class="px-4 py-4 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] text-center whitespace-nowrap" style="color: #8a9bb0;">Status</th>
                            <th class="px-4 py-4 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] text-center whitespace-nowrap" style="color: #8a9bb0;">Active Tools</th>
                            <th class="px-4 py-4 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] text-center whitespace-nowrap" style="color: #8a9bb0;">Work Time</th>
                            <th class="px-4 py-4 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] text-center whitespace-nowrap" style="color: #8a9bb0;">Live Preview</th>
                            <th class="px-4 md:px-6 py-4 text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] text-right whitespace-nowrap" style="color: #8a9bb0;">Command</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" id="matrixBody" style="divide-color: rgba(210, 166, 61, 0.08);">
                        @foreach($employeeStatus ?? [] as $es)
                        @php
                            $emp = $es['employee'];
                            $dept = $emp->department->name ?? 'General';
                            $wh  = floor($es['work_seconds'] / 3600);
                            $wm  = floor(($es['work_seconds'] % 3600) / 60);
                            $toolsStr = implode(' ', $es['ai_tools'] ?? []);
                            $searchString = strtolower($emp->name . ' ' . $dept . ' ' . $toolsStr);
                        @endphp

                        <tr class="group matrix-row transition-colors" 
                            data-search="{{ $searchString }}"
                            style="{{ $es['social_alert'] ? 'background-color: rgba(239,68,68,0.04);' : '' }}"
                            onmouseover="this.style.backgroundColor='rgba(210,166,61,0.04)'"
                            onmouseout="this.style.backgroundColor='{{ $es['social_alert'] ? 'rgba(239,68,68,0.04)' : 'transparent' }}'">
                            
                            <td class="px-4 md:px-6 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <div class="relative flex-shrink-0">
                                            <div class="w-8 h-8 md:w-9 md:h-9 rounded-xl flex items-center justify-center text-xs font-black overflow-hidden"
                                                 style="background-color: rgba(210, 166, 61, 0.15); border: 1px solid {{ $es['social_alert'] ? '#ef4444' : 'rgba(210,166,61,0.3)' }}; color: #d2a63d; {{ $es['social_alert'] ? 'animation: pulse 2s infinite;' : '' }}">
                                                @if($emp->profile_image)
                                                    <img src="{{ asset('storage/'.$emp->profile_image) }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ substr($emp->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full border-2" style="border-color: #05101f; background-color: {{ $es['is_online'] ? '#1dd182' : '#4a5568' }};"></div>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] md:text-[11px] font-black uppercase tracking-wider transition-colors truncate" style="color: {{ $es['social_alert'] ? '#ef4444' : '#eaeef2' }};">{{ $emp->name }}</p>
                                            <p class="text-[7px] md:text-[8px] font-bold uppercase tracking-widest mt-0.5 truncate" style="color: #8a9bb0;">{{ $dept }}</p>
                                        </div>
                                    </div>

                                    @if($es['social_alert'] && $es['latest_snap'])
                                        <div onclick="previewShot('{{ asset('storage/'.$es['latest_snap']->image_path) }}', '{{ addslashes($es['latest_snap']->window_title ?? '') }}')" 
                                             class="flex items-center gap-2 flex-shrink-0 p-1.5 rounded-xl shadow-md cursor-pointer transition-colors" 
                                             style="background-color: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25);"
                                             onmouseover="this.style.backgroundColor='rgba(239,68,68,0.18)'"
                                             onmouseout="this.style.backgroundColor='rgba(239,68,68,0.08)'"
                                             title="Click to view Alert Image">
                                            <div class="relative w-12 h-6 md:w-14 md:h-7 rounded border overflow-hidden shadow-inner opacity-50" style="border-color: rgba(210,166,61,0.2);">
                                                <img src="{{ asset('storage/'.$es['latest_snap']->image_path) }}" class="w-full h-full object-cover" style="filter: blur(0.5px);">
                                            </div>
                                            <div class="relative">
                                                <i class="fas fa-bell text-sm md:text-base" style="color: #ef4444;"></i>
                                                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #ef4444;"></span>
                                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 border-2" style="background-color: #ef4444; border-color: #05101f;"></span>
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if($es['is_online'])
                                    <span class="inline-flex items-center gap-1.5 text-[7px] md:text-[8px] font-black uppercase tracking-[0.2em] px-2 py-1 rounded shadow-sm whitespace-nowrap" style="color: #1dd182; border: 1px solid rgba(29,209,130,0.25); background-color: rgba(29,209,130,0.08);">
                                        <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color: #1dd182;"></span> Sync
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[7px] md:text-[8px] font-black uppercase tracking-[0.2em] px-2 py-1 rounded whitespace-nowrap" style="color: #8a9bb0; border: 1px solid rgba(138,155,176,0.2); background-color: rgba(138,155,176,0.05);">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: #4a5568;"></span> Idle
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-1 max-w-[120px] md:max-w-[150px] mx-auto">
                                    @if($es['social_alert'])
                                        <span class="text-[7px] text-white px-1.5 py-0.5 rounded shadow uppercase font-black tracking-widest whitespace-nowrap" style="background-color: #ef4444; border: 1px solid #dc2626;">
                                            <i class="fas fa-exclamation-circle"></i> Distracted
                                        </span>
                                    @elseif(count($es['ai_tools'] ?? []) > 0)
                                        @foreach(array_slice($es['ai_tools'], 0, 2) as $tool)
                                            <span class="text-[7px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded shadow whitespace-nowrap" style="color: #d2a63d; background-color: rgba(210,166,61,0.12); border: 1px solid rgba(210,166,61,0.25);">{{ $tool }}</span>
                                        @endforeach
                                        @if(count($es['ai_tools']) > 2)
                                            <span class="text-[7px] font-black px-1.5 py-0.5 rounded shadow" style="color: #8a9bb0; background-color: rgba(138,155,176,0.08);">...</span>
                                        @endif
                                    @else
                                        <span class="text-[8px] font-bold uppercase tracking-widest" style="color: #4a5568;">—</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <p class="text-[9px] md:text-[10px] font-black whitespace-nowrap" style="color: {{ $wh >= 8 ? '#1dd182' : ($wh > 0 ? '#eaeef2' : '#4a5568') }};">{{ $wh }}h {{ $wm }}m</p>
                                <div class="w-12 md:w-16 h-1 rounded-full mx-auto mt-1 overflow-hidden" style="background-color: rgba(138,155,176,0.15);">
                                    <div class="h-full rounded-full" style="width: {{ min(($wh / 9) * 100, 100) }}%; background-color: {{ $wh >= 8 ? '#1dd182' : '#d2a63d' }};"></div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if($es['latest_snap'])
                                    <div class="relative w-16 h-8 md:w-20 md:h-10 mx-auto rounded border overflow-hidden cursor-pointer shadow-md group/img"
                                         style="border-color: rgba(210,166,61,0.2);"
                                         onclick="previewShot('{{ asset('storage/'.$es['latest_snap']->image_path) }}', '{{ addslashes($es['latest_snap']->window_title ?? '') }}')">
                                        <img src="{{ asset('storage/'.$es['latest_snap']->image_path) }}" class="w-full h-full object-cover opacity-60 group-hover/img:opacity-100 group-hover/img:scale-110 transition-all" style="filter: blur(0.3px);">
                                        @if($es['latest_snap']->triggered_by_admin)
                                            <div class="absolute top-0 right-0 text-white text-[5px] font-black uppercase px-1 py-0.5 z-10" style="background-color: #ef4444;">ADM</div>
                                        @endif
                                    </div>
                                @else
                                    <div class="w-16 h-8 md:w-20 md:h-10 mx-auto rounded border flex items-center justify-center opacity-50" style="border-color: rgba(210,166,61,0.15); background-color: rgba(138,155,176,0.05);">
                                        <i class="fas fa-video-slash text-xs" style="color: #4a5568;"></i>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 md:px-6 py-3 text-right">
                                <div class="flex gap-1 md:gap-1.5 justify-end">
                                    <button onclick="triggerPulse({{ $emp->id }}, '{{ addslashes($emp->name) }}')" 
                                            class="w-6 h-6 md:w-7 md:h-7 rounded flex items-center justify-center shadow transition-colors"
                                            style="border: 1px solid rgba(239,68,68,0.3); background-color: rgba(239,68,68,0.08); color: #ef4444;"
                                            onmouseover="this.style.backgroundColor='#ef4444'; this.style.color='#fff';"
                                            onmouseout="this.style.backgroundColor='rgba(239,68,68,0.08)'; this.style.color='#ef4444';"
                                            title="Trigger Pulse">
                                        <i class="fas fa-crosshairs text-[9px] md:text-[10px]"></i>
                                    </button>
                                    <a href="{{ route('admin.employees.pulses', $emp->id) }}" 
                                       class="w-6 h-6 md:w-7 md:h-7 rounded flex items-center justify-center shadow transition-colors"
                                       style="border: 1px solid rgba(210,166,61,0.3); background-color: rgba(210,166,61,0.08); color: #d2a63d;"
                                       onmouseover="this.style.backgroundColor='#d2a63d'; this.style.color='#010717';"
                                       onmouseout="this.style.backgroundColor='rgba(210,166,61,0.08)'; this.style.color='#d2a63d';"
                                       title="Archive">
                                        <i class="fas fa-images text-[9px] md:text-[10px]"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.show', $emp->id) }}" 
                                       class="w-6 h-6 md:w-7 md:h-7 rounded flex items-center justify-center shadow transition-colors"
                                       style="border: 1px solid rgba(234,238,242,0.15); background-color: rgba(234,238,242,0.05); color: #eaeef2;"
                                       onmouseover="this.style.backgroundColor='rgba(234,238,242,0.15)'; this.style.color='#fff';"
                                       onmouseout="this.style.backgroundColor='rgba(234,238,242,0.05)'; this.style.color='#eaeef2';"
                                       title="Dossier">
                                        <i class="fas fa-user-shield text-[9px] md:text-[10px]"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>

                        {{-- Work Time (⭐ Pure Active Time Logic) --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $pwh = floor($es['pure_work_seconds'] / 3600);
                                    $pwm = floor(($es['pure_work_seconds'] % 3600) / 60);
                                    
                                    $dh  = floor($es['distraction_seconds'] / 3600);
                                    $dm  = floor(($es['distraction_seconds'] % 3600) / 60);
                                @endphp

                                {{-- Pure Exact Work Time --}}
                                <p class="text-[10px] sm:text-[11px] font-black whitespace-nowrap" style="color: {{ $pwh >= 8 ? '#1dd182' : ($pwh > 0 ? '#d2a63d' : '#4a5568') }};">
                                    {{ $pwh }}h {{ $pwm }}m <span class="text-[7px] uppercase tracking-widest ml-1" style="color: #1dd182;">Pure</span>
                                </p>
                                
                                {{-- Minus Wasted Time --}}
                                @if($es['distraction_seconds'] > 0)
                                <p class="text-[8px] font-bold mt-0.5 whitespace-nowrap pt-0.5 inline-block" style="color: #ef4444; border-top: 1px solid rgba(239,68,68,0.2);">
                                    - {{ $dh }}h {{ $dm }}m <span class="text-[6px] uppercase" style="color: #dc2626;">Wasted</span>
                                </p>
                                @endif

                                <div class="w-12 sm:w-16 h-1 rounded-full mx-auto mt-1 overflow-hidden" style="background-color: rgba(138,155,176,0.15);">
                                    <div class="h-full rounded-full" style="width: {{ min(($pwh / 8) * 100, 100) }}%; background-color: {{ $pwh >= 8 ? '#1dd182' : '#d2a63d' }};"></div>
                                </div>
                            </td>
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
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #010717; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(210, 166, 61, 0.3); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(210, 166, 61, 0.5); }
    .-webkit-overflow-scrolling-touch { -webkit-overflow-scrolling: touch; }
    tbody tr { border-bottom: 1px solid rgba(210, 166, 61, 0.08); }
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

    // 2. Trigger Pulse Logic
    function triggerPulse(userId, name) {
        Swal.fire({
            title: 'INITIATING SCAN',
            html: `<p style="font-size:10px;color:#8a9bb0;font-weight:bold;text-transform:uppercase;letter-spacing:1px">Target: ${name}</p>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#05101f',
            color: '#eaeef2',
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
                background: '#05101f',
                color: '#eaeef2',
                confirmButtonColor: '#d2a63d',
            });
        }).catch(() => { Swal.fire({ title: 'Error', icon: 'error', background: '#05101f', color: '#eaeef2' }); });
    }

    // 3. Simple Lightbox for Preview (Mobile Optimized)
    function previewShot(src, title) {
        Swal.fire({
            html: `<img src="${src}" style="width:100%; border-radius:8px; margin-bottom:10px; border: 1px solid rgba(210,166,61,0.2);"><p style="font-size:10px; font-weight:bold; color:#d2a63d; text-transform:uppercase; letter-spacing:1px; word-wrap:break-word;">${title}</p>`,
            background: '#05101f',
            showConfirmButton: false,
            width: window.innerWidth < 768 ? '95%' : '800px',
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
                    backgroundColor: 'rgba(210, 166, 61, 0.85)',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Idle / Offline',
                    data: {!! json_encode($deptIdleData) !!},
                    backgroundColor: 'rgba(138, 155, 176, 0.25)',
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
                x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: '#8a9bb0', font: { size: window.innerWidth < 768 ? 7 : 9, weight: 'bold' } } },
                y: { stacked: true, grid: { color: 'rgba(210, 166, 61, 0.07)', drawBorder: false }, ticks: { color: '#8a9bb0', stepSize: 5 } }
            },
            plugins: {
                legend: { position: 'top', align: 'end', labels: { color: '#eaeef2', usePointStyle: true, boxWidth: 6, font: { size: 10, weight: 'bold' } } },
                tooltip: { backgroundColor: 'rgba(5, 16, 31, 0.95)', titleColor: '#eaeef2', bodyColor: '#8a9bb0', borderColor: 'rgba(210,166,61,0.3)', borderWidth: 1 }
            }
        }
    });

    // 5. ⭐ WEBSOCKETS LIVE LISTENER (Pusher Native Channels Backend Integration)
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof pusherInstance !== 'undefined') {
            console.log('Radar Active: Listening for stealth telemetry signals via Pusher Channel Setup...');
            
            var channel = pusherInstance.subscribe('admin-alerts');
            channel.bind('new-alert', function(e) {
                
                // Audio chime feedback processing
                var audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                audio.play().catch(function(err) {
                    console.log('Audio playback context needs manual user interaction protocol first.');
                });
                
                // Live SweetAlert notification popup display
                Swal.fire({
                    title: '🚨 LIVE DISTRACTION CAUGHT!',
                    html: `
                        <div style="text-align: left; margin-top: 10px;">
                            <p style="color:#8a9bb0; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">Target Operator:</p>
                            <p style="color:#ef4444; font-size:16px; font-weight:900; margin-bottom:10px;">${e.employeeName}</p>
                            
                            <p style="color:#8a9bb0; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">Platform Detected:</p>
                            <span style="background-color:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.4); color:#fca5a5; padding:4px 8px; border-radius:4px; font-size:11px; font-weight:black; display:inline-block; margin-top:4px;">
                                ${e.message}
                            </span>
                        </div>
                    `,
                    background: '#05101f',
                    color: '#eaeef2',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 15000,
                    timerProgressBar: true,
                    icon: 'warning',
                    iconColor: '#d2a63d'
                });
            });
        } else {
            console.log('Pusher client SDK instance object undetected on layout layer fallback configuration.');
        }
    });
</script>
@endsection