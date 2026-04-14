@extends('layouts.admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ✅ IMAGE PREVIEW MODAL --}}
<div id="imageModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/95 backdrop-blur-xl flex items-center justify-center p-4 cursor-zoom-out" onclick="this.classList.add('hidden')">
    <div class="relative max-w-5xl w-full">
        <img id="modalImg" src="" class="w-full rounded-[2rem] border border-indigo-500/30 shadow-2xl">
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
    {{-- Animated Background Decor --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
    </div>

    <div class="relative z-10 p-6 md:p-8">
        
        {{-- 📊 1. TOP STATS BAR --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4 hover:scale-105 transition-all group">
                <div class="bg-blue-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-users"></i></div>
                <div>
                    <p class="text-[10px] font-medium text-white/60 uppercase tracking-widest">Total Fleet</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ $totalEmployees }}</h3>
                </div>
            </div>
            
            <div class="glass-effect p-6 rounded-2xl border border-emerald-500/20 flex items-center gap-4 bg-emerald-500/5">
                <div class="bg-emerald-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg animate-pulse"><i class="fas fa-signal"></i></div>
                <div>
                    <p class="text-[10px] font-medium text-emerald-400 uppercase tracking-widest">Active Now</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ $employeePerformance->count() }}</h3>
                </div>
            </div>

            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4">
                <div class="bg-rose-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-check-double"></i></div>
                <div>
                    <p class="text-[10px] font-medium text-white/60 uppercase tracking-widest">Tasks</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ $totalTasks }}</h3>
                </div>
            </div>

            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4">
                <div class="bg-amber-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-brain"></i></div>
                <div>
                    <p class="text-[10px] font-medium text-white/60 uppercase tracking-widest">AI Load</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ round($globalAiStats['ai_tool'] ?? 0, 1) }}%</h3>
                </div>
            </div>

            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4">
                <div class="bg-indigo-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-sitemap"></i></div>
                <div>
                    <p class="text-[10px] font-medium text-white/60 uppercase tracking-widest">Nodes</p>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ $totalDepartments }}</h3>
                </div>
            </div>
        </div>

        {{-- ⭐ 2. LIVE ACTIVE PERSONNEL --}}
        <div class="glass-effect rounded-[2rem] p-6 mb-8 border border-emerald-500/20 bg-emerald-500/5">
            <h4 class="text-xs font-black text-white uppercase italic tracking-widest mb-6 flex items-center gap-3">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                Operators Authenticated in Current Cycle
            </h4>
            <div class="flex flex-wrap gap-4">
                @forelse($employeePerformance as $perf)
                    <div class="bg-white/5 border border-white/10 pl-2 pr-5 py-2 rounded-2xl flex items-center gap-3 hover:bg-indigo-600 transition-all cursor-default group shadow-xl">
                        <img src="{{ $perf->user->profile_image ? asset('storage/' . $perf->user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($perf->name).'&background=6366f1&color=fff' }}" class="w-10 h-10 rounded-xl border border-white/20 object-cover">
                        <div>
                            <p class="text-[11px] font-black text-white uppercase italic">{{ $perf->name }}</p>
                            <p class="text-[8px] text-emerald-400 font-bold uppercase tracking-tighter group-hover:text-white">NODE: {{ $perf->watch_time }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] text-white/20 italic uppercase tracking-widest py-4 px-2">Scanning signals...</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- 🛰️ LEFT: Telemetry Stream --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-effect rounded-[2.5rem] p-8 border border-white/10 shadow-2xl">
                    <h4 class="font-black text-white uppercase italic tracking-tighter text-xl mb-8 flex items-center gap-3">
                        <i class="fas fa-broadcast-tower text-emerald-400"></i> Telemetry Stream
                    </h4>

                    <div class="space-y-4">
                        @forelse($employeePerformance as $perf)
                        <div class="flex flex-col md:flex-row items-center justify-between p-6 rounded-3xl border border-white/5 bg-white/5 hover:bg-white/10 transition-all group">
                            <div class="flex items-center gap-5 w-full md:w-1/4">
                                <div class="relative">
                                    <img src="{{ $perf->user->profile_image ? asset('storage/' . $perf->user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($perf->name).'&background=6366f1&color=fff' }}" class="w-12 h-12 rounded-2xl border border-white/10 shadow-lg">
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-900 shadow-sm animate-pulse"></div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-white uppercase italic">{{ $perf->name }}</p>
                                    <span class="text-[8px] text-indigo-400 font-bold uppercase">{{ $perf->watch_time }} Session</span>
                                </div>
                            </div>

                            <div class="w-full md:w-1/3 flex items-center gap-3 my-4 md:my-0">
                                <div class="p-1 bg-black/40 rounded-xl border border-white/10 overflow-hidden shadow-inner group-hover:border-indigo-500/50 transition-colors">
                                    @if($perf->last_screenshot)
                                        <img src="{{ asset('storage/' . $perf->last_screenshot) }}" class="w-24 h-14 object-cover rounded-lg cursor-zoom-in hover:scale-110 transition-transform" onclick="openFullSC(this.src)">
                                    @else
                                        <div class="w-24 h-14 flex items-center justify-center text-[8px] text-white/20 italic bg-slate-800 rounded-lg">SIGNAL LOST</div>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[8px] text-white/40 uppercase font-black tracking-tighter">Active Window</span>
                                    <p class="text-[10px] text-white font-bold truncate max-w-[150px] italic">{{ $perf->last_window ?? 'Calibrating...' }}</p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.employees.pulses', $perf->user_id) }}" class="p-3 bg-indigo-500/20 text-indigo-400 rounded-xl hover:bg-indigo-600 transition-all border border-indigo-500/20 shadow-lg">
                                    <i class="fas fa-history text-xs"></i>
                                </a>
                                <button onclick="triggerManualCapture({{ $perf->user_id }})" class="p-3 bg-emerald-500/20 text-emerald-400 rounded-xl hover:bg-emerald-600 border border-emerald-500/20 shadow-lg">
                                    <i class="fas fa-camera text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-20 bg-white/5 rounded-[2rem] border border-dashed border-white/10">
                            <i class="fas fa-satellite-dish text-4xl text-white/10 mb-4 animate-pulse"></i>
                            <p class="text-[10px] text-white/20 italic uppercase tracking-[0.5em]">No active signals in current cycle...</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- 🔥 MISSION HEATMAP --}}
                <div class="glass-effect rounded-[2.5rem] p-8 border border-white/10 shadow-2xl">
                    <h4 class="font-black text-white uppercase tracking-tighter italic text-lg mb-6 flex items-center gap-3">
                        <i class="fas fa-fire-alt text-orange-500"></i> Mission Heatmap
                    </h4>
                    <div id="taskHeatmap" class="min-h-[250px] w-full"></div>
                </div>
            </div>

            {{-- 🕹️ RIGHT: Alerts & AI --}}
            <div class="space-y-6">
                <div class="glass-effect rounded-[2rem] p-8 border border-rose-500/20 shadow-2xl bg-rose-500/5">
                    <h4 class="font-black text-white uppercase tracking-tighter mb-6 italic flex items-center gap-2 text-xl">
                        <i class="fas fa-exclamation-triangle text-rose-500 animate-pulse"></i> Security Alerts
                    </h4>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar-style">
                        @forelse($securityAlerts ?? [] as $alert)
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-between">
                            <div class="overflow-hidden">
                                <p class="text-[9px] font-black text-rose-400 uppercase">{{ $alert->operator }}</p>
                                <p class="text-[10px] text-white font-bold truncate italic">{{ $alert->violation }}</p>
                            </div>
                            <span class="text-[8px] bg-rose-600 text-white px-2 py-1 rounded font-black">{{ $alert->time }}</span>
                        </div>
                        @empty
                        <div class="text-center py-6 border border-dashed border-white/5 rounded-xl">
                            <p class="text-[9px] text-emerald-400 font-black italic">ALL NODES SECURE</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- AI Penetration --}}
                <div class="glass-effect rounded-[2rem] p-8 border border-white/10 shadow-2xl">
                    <h4 class="font-black text-white uppercase tracking-tighter mb-6 italic flex items-center gap-2 text-xl">
                        <i class="fas fa-brain text-rose-500"></i> AI Penetration
                    </h4>
                    <div class="space-y-5">
                        @foreach($aiToolBreakdown as $ai)
                        <div>
                            <div class="flex justify-between text-[10px] uppercase font-black italic mb-2">
                                <span class="text-slate-300">{{ $ai->ai_tool }}</span>
                                <span class="text-rose-400">{{ round($ai->total_time / 60) }} M</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-1.5 border border-white/5 shadow-inner">
                                <div class="bg-gradient-to-r from-rose-600 to-indigo-600 h-full rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 📊 Mission Heatmap Logic
    document.addEventListener('DOMContentLoaded', function () {
        var heatmapData = {!! json_encode($heatmapCounts ?? [0,0,0,0,0,0,0]) !!};
        var heatmapDays = {!! json_encode($heatmapDays ?? ['M','T','W','T','F','S','S']) !!};

        var options = {
            series: [{ name: 'Completed', data: heatmapData }],
            chart: { height: 250, type: 'bar', toolbar: { show: false }, background: 'transparent' },
            plotOptions: { bar: { borderRadius: 10, columnWidth: '50%', distributed: true } },
            colors: ['#10b981', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b'],
            xaxis: { categories: heatmapDays, labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 900 } } },
            yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '10px' } } },
            grid: { borderColor: 'rgba(255,255,255,0.05)' },
            tooltip: { theme: 'dark' }
        };

        var chart = new ApexCharts(document.querySelector("#taskHeatmap"), options);
        chart.render();
    });

    function openFullSC(src) { 
        document.getElementById('modalImg').src = src; 
        document.getElementById('imageModal').classList.remove('hidden'); 
    }

    function triggerManualCapture(userId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/admin/employees/${userId}/trigger-screenshot`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                Swal.fire({ title: '📸 SIGNAL SENT', icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#1e1b4b', color: '#fff' });
            }
        });
    }

    // Auto-refresh Dashboard every 30 seconds
    setTimeout(() => { 
        if(document.getElementById('imageModal').classList.contains('hidden')) {
            location.reload(); 
        }
    }, 30000); 
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;700;900&display=swap');
    body { font-family: 'Space Grotesk', sans-serif; background: #020617; }
    .glass-effect { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
</style>
@endsection