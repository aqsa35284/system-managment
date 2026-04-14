@extends('layouts.admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ✅ IMAGE PREVIEW MODAL --}}
<div id="imageModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/95 backdrop-blur-xl flex items-center justify-center p-4 cursor-zoom-out" onclick="this.classList.add('hidden')">
    <div class="relative max-w-5xl w-full">
        <img id="modalImg" src="" class="w-full rounded-[2rem] border border-indigo-500/30 shadow-2xl">
    </div>
</div>

<div class="min-h-screen bg-[#020617] relative overflow-hidden">
    {{-- Animated Background Decor --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-pulse"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-pulse"></div>
    </div>

    <div class="relative z-10 p-6 md:p-8">
        
        {{-- 📊 1. TOP STATS BAR --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4 hover:bg-white/5 transition-all">
                <div class="bg-blue-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-blue-400 shadow-lg"><i class="fas fa-users"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Total Fleet</p>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ $totalEmployees }}</h3>
                </div>
            </div>
            
            <div class="glass-effect p-6 rounded-2xl border border-emerald-500/20 flex items-center gap-4 bg-emerald-500/5">
                <div class="bg-emerald-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg animate-pulse"><i class="fas fa-signal"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Active Now</p>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ $employeePerformance->count() }}</h3>
                </div>
            </div>

            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4">
                <div class="bg-rose-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-rose-400"><i class="fas fa-tasks"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Total Tasks</p>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ $totalTasks }}</h3>
                </div>
            </div>

            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4">
                <div class="bg-amber-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-amber-400"><i class="fas fa-brain"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">AI Penetration</p>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ round($globalAiStats['ai_tool'] ?? 0, 1) }}%</h3>
                </div>
            </div>

            <div class="glass-effect p-6 rounded-2xl border border-white/10 flex items-center gap-4">
                <div class="bg-indigo-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-indigo-400"><i class="fas fa-microchip"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Nodes Active</p>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ $totalDepartments }}</h3>
                </div>
            </div>
        </div>

        {{-- ⭐ 2. LIVE ACTIVE PERSONNEL (Fix for missing names) --}}
        <div class="glass-effect rounded-[2rem] p-6 mb-8 border border-emerald-500/20 bg-emerald-500/5">
            <h4 class="text-xs font-black text-white uppercase italic tracking-widest mb-6 flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                Authenticated Fleet Operators
            </h4>
            <div class="flex flex-wrap gap-4">
                @forelse($employeePerformance as $perf)
                    <div class="bg-white/5 border border-white/10 pl-2 pr-5 py-2 rounded-2xl flex items-center gap-3 hover:bg-indigo-600 transition-all cursor-default group">
                        <img src="{{ $perf->user->profile_image ? asset('storage/' . $perf->user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($perf->name).'&background=6366f1&color=fff' }}" class="w-10 h-10 rounded-xl border border-white/20 object-cover shadow-lg">
                        <div>
                            <p class="text-[11px] font-black text-white uppercase italic tracking-tighter">{{ $perf->name }}</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $perf->is_online ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
                                <p class="text-[8px] text-white/60 font-bold uppercase tracking-tighter group-hover:text-white">{{ $perf->watch_time }} Active</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] text-white/20 italic uppercase tracking-widest py-2">Waiting for authenticated signals...</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- 🛰️ LEFT: Telemetry Stream --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-effect rounded-[2.5rem] p-8 border border-white/10 shadow-2xl">
                    <h4 class="font-black text-white uppercase italic tracking-tighter text-xl mb-8 flex items-center gap-3">
                        <i class="fas fa-broadcast-tower text-emerald-400 animate-pulse"></i> Telemetry Pulse
                    </h4>

                    <div class="space-y-4">
                        @forelse($employeePerformance as $perf)
                        <div class="flex flex-col md:flex-row items-center justify-between p-6 rounded-3xl border border-white/5 bg-white/5 hover:bg-white/10 transition-all group">
                            <div class="flex items-center gap-5 w-full md:w-1/4">
                                <div class="relative">
                                    <img src="{{ $perf->user->profile_image ? asset('storage/' . $perf->user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($perf->name).'&background=6366f1&color=fff' }}" class="w-14 h-14 rounded-2xl border border-white/10 shadow-lg object-cover">
                                    <div class="absolute -top-1 -right-1 w-4 h-4 {{ $perf->is_online ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full border-2 border-[#020617] animate-pulse"></div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-white uppercase italic tracking-tighter">{{ $perf->name }}</p>
                                    <span class="text-[8px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded font-black uppercase">{{ $perf->watch_time }}</span>
                                </div>
                            </div>

                            <div class="w-full md:w-1/2 flex items-center gap-4 my-4 md:my-0">
                                <div class="p-1 bg-black/40 rounded-xl border border-white/10 overflow-hidden shadow-inner group-hover:border-indigo-500/50 transition-all">
                                    @if($perf->last_screenshot)
                                        <img src="{{ asset('storage/' . $perf->last_screenshot) }}" class="w-28 h-16 object-cover rounded-lg cursor-zoom-in hover:scale-105 transition-transform" onclick="openFullSC(this.src)">
                                    @else
                                        <div class="w-28 h-16 flex items-center justify-center text-[8px] text-white/20 italic bg-slate-900/50 rounded-lg uppercase tracking-tighter">Scanning Node...</div>
                                    @endif
                                </div>
                                <div class="flex flex-col overflow-hidden">
                                    <span class="text-[8px] text-white/40 uppercase font-black tracking-tighter italic">Last Active Process</span>
                                    <p class="text-[10px] text-white font-bold truncate max-w-[180px] italic">{{ $perf->last_window ?? 'System Standby' }}</p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.employees.pulses', $perf->user_id) }}" class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all border border-indigo-500/20">
                                    <i class="fas fa-history text-xs"></i>
                                </a>
                                <button onclick="triggerManualCapture({{ $perf->user_id }})" class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl hover:bg-emerald-600 hover:text-white transition-all border border-emerald-500/20 shadow-lg">
                                    <i class="fas fa-camera text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-20 bg-white/2 rounded-[2rem] border border-dashed border-white/10">
                            <i class="fas fa-satellite-dish text-4xl text-white/10 mb-4 animate-bounce"></i>
                            <p class="text-[10px] text-white/20 italic uppercase tracking-[0.5em]">No nodes detected in current cycle</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- 🔥 MISSION HEATMAP --}}
                <div class="glass-effect rounded-[2.5rem] p-8 border border-white/10">
                    <h4 class="font-black text-white uppercase tracking-tighter italic text-lg mb-6 flex items-center gap-3">
                        <i class="fas fa-fire-alt text-orange-500"></i> Objective Completion Heatmap
                    </h4>
                    <div id="taskHeatmap" class="w-full"></div>
                </div>
            </div>

            {{-- 🕹️ RIGHT: Alerts & AI --}}
            <div class="space-y-6">
                <div class="glass-effect rounded-[2rem] p-8 border border-rose-500/20 shadow-2xl bg-rose-500/5">
                    <h4 class="font-black text-white uppercase tracking-tighter mb-6 italic flex items-center gap-2 text-xl">
                        <i class="fas fa-exclamation-triangle text-rose-500 animate-pulse"></i> Compliance Alerts
                    </h4>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                        @forelse($securityAlerts ?? [] as $alert)
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-between hover:bg-rose-500/20 transition-all">
                            <div class="overflow-hidden">
                                <p class="text-[9px] font-black text-rose-400 uppercase">{{ $alert->operator }}</p>
                                <p class="text-[10px] text-white font-bold truncate italic tracking-tight">{{ $alert->violation }}</p>
                            </div>
                            <span class="text-[8px] bg-rose-600 text-white px-2 py-1 rounded font-black shadow-lg">{{ $alert->time }}</span>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <i class="fas fa-user-shield text-emerald-500/20 text-4xl mb-3"></i>
                            <p class="text-[9px] text-emerald-400 font-black italic uppercase tracking-widest">All Nodes Compliant</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- AI LOAD ANALYSIS --}}
                <div class="glass-effect rounded-[2rem] p-8 border border-white/10 shadow-2xl bg-indigo-500/5">
                    <h4 class="font-black text-white uppercase tracking-tighter mb-6 italic flex items-center gap-2 text-xl">
                        <i class="fas fa-brain text-purple-500"></i> AI Penetration Load
                    </h4>
                    <div class="space-y-6">
                        @forelse($aiToolBreakdown as $ai)
                        <div>
                            <div class="flex justify-between text-[10px] uppercase font-black italic mb-2">
                                <span class="text-slate-300">{{ $ai->ai_tool }}</span>
                                <span class="text-indigo-400">{{ round($ai->total_time / 60) }} Mins</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2 border border-white/5 shadow-inner">
                                @php $percent = min(($ai->total_time / 3600) * 100, 100); @endphp
                                <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-rose-600 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-white/20 italic text-[9px] uppercase tracking-widest">AI usage scanning...</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 📊 Mission Heatmap Logic (Synced with real data)
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{ name: 'Objectives Completed', data: {!! json_encode($heatmapCounts) !!} }],
            chart: { height: 280, type: 'bar', toolbar: { show: false }, background: 'transparent' },
            plotOptions: { bar: { borderRadius: 8, columnWidth: '40%', distributed: true, dataLabels: { position: 'top' } } },
            colors: ['#10b981', '#6366f1', '#f59e0b', '#f43f5e', '#8b5cf6', '#3b82f6', '#ec4899'],
            xaxis: { categories: {!! json_encode($heatmapDays) !!}, labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 900 } } },
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            tooltip: { theme: 'dark', y: { formatter: (v) => v + " Tasks" } }
        };
        new ApexCharts(document.querySelector("#taskHeatmap"), options).render();
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
                Swal.fire({ title: '📸 SIGNAL TRANSMITTED', text: 'Surveillance pulse broadcasted.', icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#1e1b4b', color: '#fff' });
            }
        });
    }

    // Auto-refresh Dashboard every 45 seconds (Smart Polling)
    setInterval(() => { 
        if(document.getElementById('imageModal').classList.contains('hidden')) {
            window.location.reload(); 
        }
    }, 45000); 
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;700;900&display=swap');
    body { font-family: 'Space Grotesk', sans-serif; background: #020617; }
    .glass-effect { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.2); border-radius: 10px; }
</style>
@endsection