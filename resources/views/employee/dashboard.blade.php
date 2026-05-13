@extends('layouts.employee')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ═══════════════════════════════════════════════════════════
     NODE LINK OVERLAY (NAVY BLUE THEME)
═══════════════════════════════════════════════════════════ --}}
<div id="lock-overlay" class="fixed inset-0 z-[9999] bg-[#0F172A]/95 backdrop-blur-xl flex items-center justify-center transition-all duration-700">
    <div class="text-center p-12 bg-[#1E293B]/80 border border-blue-500/30 rounded-[3rem] shadow-2xl max-w-md w-full relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-2xl"></div>
        <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-8 border border-blue-400/50 shadow-[0_0_30px_rgba(37,99,235,0.3)] relative overflow-hidden z-10">
            <div class="absolute inset-0 bg-white opacity-20 animate-ping"></div>
            <i class="fas fa-satellite-dish text-4xl text-white relative z-10"></i>
        </div>
        <h2 class="text-3xl font-black text-white uppercase tracking-widest mb-4 relative z-10">Node Link Required</h2>
        <p class="text-blue-300 text-[10px] mb-8 uppercase font-bold tracking-[0.2em] relative z-10">Establish secure connection to resume session</p>
        <button onclick="initiateTracking()" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-lg active:scale-95 relative z-10">
            <i class="fas fa-link mr-2"></i> Establish Signal
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     INACTIVITY WARNING OVERLAY
═══════════════════════════════════════════════════════════ --}}
<div id="inactivity-overlay" class="fixed inset-0 z-[9998] hidden bg-[#0F172A]/95 backdrop-blur-xl flex items-center justify-center">
    <div class="text-center p-12 bg-[#1E293B]/90 border border-amber-500/40 rounded-[3rem] shadow-2xl max-w-md w-full">
        <div class="w-20 h-20 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-amber-500/30">
            <i class="fas fa-exclamation-triangle text-3xl text-amber-500 animate-pulse"></i>
        </div>
        <h2 class="text-2xl font-black text-white uppercase tracking-widest mb-3">Idle Detected</h2>
        <p class="text-amber-500 text-[10px] mb-2 font-black tracking-[0.2em] uppercase">20 Minutes of Inactivity</p>
        <p class="text-slate-400 text-xs mb-8 font-medium">Timer paused. Are you still working?</p>
        <div class="flex gap-3">
            <button onclick="resumeFromInactivity()" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-lg active:scale-95">
                <i class="fas fa-play mr-2"></i> Resume
            </button>
            <button onclick="stopSession()" class="flex-1 bg-rose-500/10 text-rose-500 border border-rose-500/30 py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all hover:bg-rose-600 hover:text-white">
                <i class="fas fa-power-off mr-2"></i> End Shift
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MAIN DASHBOARD CONTENT
═══════════════════════════════════════════════════════════ --}}
<div id="dashboard-content" class="opacity-0 blur-xl pointer-events-none transition-all duration-1000">
<div class="min-h-screen bg-[#0B1120] relative z-10 p-6 md:p-10 text-white">

    {{-- TOP BAR --}}
    <div class="mb-8 bg-[#0F172A] rounded-3xl p-6 md:p-8 shadow-2xl border border-blue-900/30">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <img src="{{ auth()->user()->profile_image ? asset('storage/'.auth()->user()->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=2563eb&color=fff' }}"
                         class="w-20 h-20 rounded-2xl object-cover border border-blue-500/50 shadow-xl">
                    <div class="absolute -bottom-2 -right-2 w-5 h-5 bg-emerald-500 rounded-full border-2 border-[#0F172A] shadow-lg flex items-center justify-center">
                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-widest">
                        Operator <span class="text-blue-500">Node</span>
                    </h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">
                        {{ auth()->user()->name }} | <span class="text-blue-400">SHIFT: <span id="shift-display" class="font-black text-white">DETECTING...</span></span>
                    </p>
                    <div id="activity-status" class="mt-2 hidden">
                        <span class="inline-flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/30 text-amber-500 px-3 py-1 rounded-md text-[8px] font-black uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                            <span id="activity-status-text">Active</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- ⭐ ADVANCED FEATURE: Live Clock --}}
                <div class="bg-[#1E293B] border border-slate-700/50 px-6 py-3 rounded-2xl flex flex-col items-center min-w-[120px]">
                    <p class="text-[8px] font-bold uppercase text-slate-400 tracking-widest mb-1">Current Time</p>
                    <p id="live-clock" class="text-lg font-black text-white tracking-widest">00:00:00</p>
                </div>

                <div class="bg-[#1E293B] border border-slate-700/50 px-6 py-3 rounded-2xl flex flex-col items-center min-w-[120px]">
                    <p class="text-[8px] font-bold uppercase text-slate-400 tracking-widest mb-1">Logins Today</p>
                    <p class="text-lg font-black text-white">
                        {{ \App\Models\Attendance::where('user_id', Auth::id())->whereDate('date', now())->count() }}
                    </p>
                </div>

                <div class="bg-blue-600 text-white px-6 py-3 rounded-2xl shadow-[0_0_20px_rgba(37,99,235,0.2)] flex flex-col items-center min-w-[140px] border border-blue-500">
                    <p class="text-[8px] font-bold uppercase tracking-widest text-blue-200 mb-1">Active Session</p>
                    <p id="session-timer" class="text-xl font-mono font-black tracking-widest">00:00:00</p>
                </div>

                <button onclick="stopSession()" class="w-14 h-14 flex items-center justify-center bg-rose-500/10 text-rose-500 rounded-2xl hover:bg-rose-600 hover:text-white transition-all border border-rose-500/20 shadow-lg" title="End Shift">
                    <i class="fas fa-power-off text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- PROGRESS & STATS ROW --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-[#0F172A] rounded-3xl p-6 border border-blue-900/30 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <p class="text-[9px] font-black uppercase text-slate-400 tracking-[0.2em]">Overall Work Progress</p>
                @php $avgProgress = $tasks->count() > 0 ? round($tasks->avg('completion_percentage')) : 0; @endphp
                <span class="text-blue-500 font-black text-sm">{{ $avgProgress }}%</span>
            </div>
            <div class="w-full bg-[#1E293B] h-2 rounded-full overflow-hidden border border-slate-700/50">
                <div class="bg-blue-600 h-full transition-all duration-1000" style="width: {{ $avgProgress }}%"></div>
            </div>
        </div>

        <div class="bg-[#0F172A] rounded-3xl p-6 border border-blue-900/30 shadow-xl flex justify-around items-center">
            <div class="text-center">
                <p class="text-[8px] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Active Tasks</p>
                <p class="text-2xl font-black text-white">{{ $tasks->count() }}</p>
            </div>
            <div class="h-10 w-[1px] bg-slate-700/50"></div>
            <div class="text-center">
                <p class="text-[8px] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Hours Worked</p>
                @php $wh = floor($totalSeconds/3600); $wm = floor(($totalSeconds%3600)/60); @endphp
                <p id="stat-work-hours" class="text-2xl font-black text-emerald-500">{{ $wh }}h {{ $wm }}m</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-[#0F172A] rounded-[2.5rem] p-8 border border-emerald-900/50 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-900/20 blur-2xl rounded-full"></div>
                <h4 class="text-xs font-black text-emerald-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2 relative z-10">
                    <i class="fas fa-broadcast-tower"></i> Commander's Briefing
                </h4>
                <div class="space-y-4 max-h-[250px] overflow-y-auto custom-scrollbar pr-2 relative z-10">
                    @forelse($adminFeedbacks as $feedback)
                    <div class="bg-[#1E293B] rounded-xl p-4 border border-slate-700/50 border-l-2 border-l-emerald-500">
                        <div class="flex justify-between mb-2 items-center">
                            <span class="text-[8px] font-black bg-emerald-500/10 text-emerald-500 px-2 py-0.5 rounded tracking-widest">SCORE: {{ $feedback->rating_score }}/10</span>
                            <span class="text-[7px] text-slate-500 uppercase tracking-widest font-bold">{{ $feedback->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[10px] text-slate-300 italic font-medium leading-relaxed">"{{ $feedback->review_note }}"</p>
                    </div>
                    @empty
                    <p class="text-center text-slate-500 italic text-[10px] py-10 uppercase tracking-widest font-bold">No new commands...</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-[#0F172A] rounded-[2.5rem] p-8 border border-blue-900/30 shadow-2xl">
                <h4 class="text-xs font-black text-blue-500 uppercase tracking-[0.2em] mb-6">Weekly Activity</h4>
                <div id="weeklyChart"></div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[#0F172A] rounded-[3rem] p-8 shadow-2xl border border-blue-900/30">
                <h4 class="text-sm font-black text-white uppercase tracking-widest mb-8 flex items-center gap-3">
                    <i class="fas fa-crosshairs text-blue-500"></i> Active Objectives
                </h4>
                <div class="space-y-4">
                    @forelse($tasks as $task)
                    <div class="bg-[#1E293B] rounded-2xl p-5 border border-slate-700/50 hover:border-blue-500/50 transition-all">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex-1 w-full">
                                <span class="text-[7px] font-black px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded tracking-widest uppercase border border-blue-500/20">{{ str_replace('_', ' ', $task->status) }}</span>
                                <h5 class="text-sm font-bold text-white mt-2">{{ $task->title }}</h5>
                                <div class="mt-3 flex items-center gap-3">
                                    <div class="flex-1 bg-[#0F172A] h-1.5 rounded-full overflow-hidden border border-slate-700/50">
                                        <div class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-[8px] font-black text-slate-400 tracking-widest">{{ $task->completion_percentage ?? 0 }}%</span>
                                </div>
                            </div>
                            
                            {{-- UPDATE & FORWARD BUTTONS --}}
                            <div class="flex gap-2 w-full md:w-auto mt-4 md:mt-0">
                                <button onclick="openForwardModal({{ $task->id }})" class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 hover:bg-indigo-600 hover:text-white px-4 py-2.5 rounded-xl text-[10px] font-black transition-all shadow-lg" title="Forward Task">
                                    <i class="fas fa-share-square"></i>
                                </button>
                                <button onclick="openOperationalModal({{ $task->id }})" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all shadow-lg">Update</button>
                            </div>

                        </div>
                    </div>
                    @empty
                    <p class="text-center py-10 text-slate-500 uppercase tracking-widest font-bold text-[10px]">No active objectives</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<canvas id="screenshotCanvas" style="display:none;"></canvas>

{{-- ═══════════════════════════════════════════════════════════
     OPERATIONAL LOG MODAL (UPDATE TASK)
═══════════════════════════════════════════════════════════ --}}
<div id="opModal" class="fixed inset-0 z-[10000] hidden bg-[#0F172A]/90 backdrop-blur-sm flex items-center justify-center p-4">
    <div id="opModalContent" class="bg-white max-w-lg w-full rounded-[2rem] shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <div class="p-6 bg-[#0F172A] flex justify-between items-center border-b border-blue-900/50">
            <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Operational Log Entry</h3>
            <button onclick="closeOperationalModal()" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        {{-- ⭐ ADVANCED FEATURE: Button Loader to prevent double click --}}
        <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-8 space-y-5" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Transmitting...';">
            @csrf
            <input type="hidden" name="task_id" id="modal_task_id">
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="completion_percentage" min="0" max="100" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 outline-none focus:border-blue-500 transition-colors" placeholder="Progress %">
                <input type="text" name="blockers" placeholder="Any blockers?" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 outline-none focus:border-blue-500 transition-colors">
            </div>
            <textarea name="update_note" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 resize-none outline-none focus:border-blue-500 transition-colors" placeholder="Update details..."></textarea>
            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black uppercase tracking-[0.2em] text-[10px] shadow-lg active:scale-95 transition-all hover:bg-blue-700">Transmit Report</button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     FORWARD TASK MODAL
═══════════════════════════════════════════════════════════ --}}
<div id="forwardModal" class="fixed inset-0 z-[10000] hidden bg-[#0F172A]/90 backdrop-blur-sm flex items-center justify-center p-4">
    <div id="forwardModalContent" class="bg-white max-w-lg w-full rounded-[2rem] shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <div class="p-6 bg-[#0F172A] flex justify-between items-center border-b border-indigo-900/50">
            <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]"><i class="fas fa-share mr-2"></i> Forward Objective</h3>
            <button onclick="closeForwardModal()" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="forward-form" method="POST" class="p-8 space-y-5" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Forwarding...';">
            @csrf
            <input type="hidden" name="task_id" id="forward_task_id">
            
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Select Team Member</label>
                <select name="forwarded_to" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 outline-none focus:border-indigo-500 transition-colors">
                    <option value="">-- Choose Colleague --</option>
                    @foreach($users ?? [] as $teamMember)
                        @if($teamMember->id !== auth()->id())
                            <option value="{{ $teamMember->id }}">{{ $teamMember->name }} ({{ $teamMember->role ?? 'Employee' }})</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <textarea name="forward_reason" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 resize-none outline-none focus:border-indigo-500 transition-colors" placeholder="Why are you forwarding this task?"></textarea>
            
            <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-black uppercase tracking-[0.2em] text-[10px] shadow-lg active:scale-95 transition-all hover:bg-indigo-700">Forward Task</button>
        </form>
    </div>
</div>

{{-- Scripts required for advanced features --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
{{-- Confetti for 100% completion celebration --}}
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
// ⭐ ADVANCED FEATURE: SweetAlert2 Auto-Toasts for session messages
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            toast: true, position: 'top-end', showConfirmButton: false,
            timer: 4000, timerProgressBar: true, icon: 'success',
            title: "{{ session('success') }}",
            background: '#0F172A', color: '#fff',
            iconColor: '#10B981'
        });
        
        // Agar task completely forward hua ya finish hua ho to Confetti (Celebration) fire karo
        confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 }, colors: ['#2563eb', '#10b981', '#ffffff'] });
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            toast: true, position: 'top-end', showConfirmButton: false,
            timer: 4000, timerProgressBar: true, icon: 'error',
            title: "{{ session('error') }}",
            background: '#0F172A', color: '#fff'
        });
    });
@endif

// ⭐ ADVANCED FEATURE: Live Digital Clock
function updateLiveClock() {
    const clockEl = document.getElementById('live-clock');
    if (clockEl) {
        const now = new Date();
        clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}
setInterval(updateLiveClock, 1000);
updateLiveClock();

// ═══════════════════════════════════════════════════════════
//  CORE STATE (Unchanged)
// ═══════════════════════════════════════════════════════════
let videoTrack        = null;
let workSeconds       = {{ $totalSeconds ?? 0 }};
let isTimerRunning    = false;
let isInactiveOverlay = false;
let lastActivityTime  = Date.now();
let timerInterval     = null;
let triggerInterval   = null;
let aiInterval        = null;

const captureTargets = [30, 60, 120, 180, 300, 420, 540, 660, 780, 900, 1080, 1260];
let currentCaptureIndex = 0;
let cycleStartTime = 0; 

const INACTIVITY_LIMIT = 20 * 60 * 1000; // 20 minutes in ms
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ═══════════════════════════════════════════════════════════
//  Automated Surface/Window Title Detection
// ═══════════════════════════════════════════════════════════
function getSurfaceLabel() {
    if (videoTrack && videoTrack.label && videoTrack.label.trim().length > 0) {
        return videoTrack.label;
    }
    return document.title;
}

// ═══════════════════════════════════════════════════════════
//  ACTIVITY TRACKING
// ═══════════════════════════════════════════════════════════
function resetActivityTimer() {
    lastActivityTime = Date.now();
    if (isInactiveOverlay) {
        resumeFromInactivity();
    }
}

['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click', 'focus'].forEach(event => {
    document.addEventListener(event, resetActivityTimer, { passive: true });
});

document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        resetActivityTimer();
    }
});

function checkInactivity() {
    if (!isTimerRunning || isInactiveOverlay) return;

    const idleMs = Date.now() - lastActivityTime;
    if (idleMs >= INACTIVITY_LIMIT) {
        triggerInactivityAlert();
    }
}

function triggerInactivityAlert() {
    isInactiveOverlay = true;
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }

    document.getElementById('inactivity-overlay').classList.remove('hidden');
    document.getElementById('activity-status').classList.remove('hidden');
    document.getElementById('activity-status-text').innerText = 'IDLE — Timer Paused';

    fetch("{{ route('employee.attendance.report-idle') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ seconds: workSeconds })
    }).catch(() => {});
}

function resumeFromInactivity() {
    isInactiveOverlay = false;
    lastActivityTime = Date.now();
    document.getElementById('inactivity-overlay').classList.add('hidden');
    document.getElementById('activity-status-text').innerText = 'ACTIVE';

    if (!timerInterval && isTimerRunning) {
        startTimerInterval();
    }
}

// ═══════════════════════════════════════════════════════════
//  Timer with RANDOM SCREENSHOT CYCLE
// ═══════════════════════════════════════════════════════════
function startTimerInterval() {
    cycleStartTime = workSeconds;
    
    timerInterval = setInterval(() => {
        workSeconds++;
        updateTimerDisplay();
        
        if (workSeconds % 20 === 0) syncTimer();
        
        let secondsInCurrentCycle = workSeconds - cycleStartTime;
        
        if (currentCaptureIndex < captureTargets.length && secondsInCurrentCycle >= captureTargets[currentCaptureIndex]) {
            autoCapture(false);
            currentCaptureIndex++;
        }
        
        if (secondsInCurrentCycle > 1260) {
            cycleStartTime = workSeconds;
            currentCaptureIndex = 0;
        }
    }, 1000);
}

function updateTimerDisplay() {
    const h = Math.floor(workSeconds / 3600).toString().padStart(2, '0');
    const m = Math.floor((workSeconds % 3600) / 60).toString().padStart(2, '0');
    const s = (workSeconds % 60).toString().padStart(2, '0');

    const timerEl = document.getElementById('session-timer');
    if (timerEl) timerEl.innerText = `${h}:${m}:${s}`;

    const statEl = document.getElementById('stat-work-hours');
    if (statEl) statEl.innerText = `${Math.floor(workSeconds / 3600)}h ${Math.floor((workSeconds % 3600) / 60)}m`;
}

function updateShiftDisplay() {
    const h = new Date().getHours();
    const el = document.getElementById('shift-display');
    let shiftName;
    if (h >= 5 && h < 12) shiftName = 'MORNING';
    else if (h >= 12 && h < 17) shiftName = 'AFTERNOON';
    else if (h >= 17 && h < 21) shiftName = 'EVENING';
    else shiftName = 'NIGHT';
    if (el) el.innerText = shiftName;
}

// ═══════════════════════════════════════════════════════════
//  START ALL OPERATIONAL SYSTEMS
// ═══════════════════════════════════════════════════════════
function startOperationalSystems() {
    isTimerRunning = true;
    lastActivityTime = Date.now();
    updateShiftDisplay();

    startTimerInterval();

    setInterval(checkInactivity, 30000);

    triggerInterval = setInterval(async () => {
        try {
            const res = await fetch("{{ route('employee.check.trigger') }}");
            const data = await res.json();
            if (data.trigger && videoTrack) autoCapture(true);
        } catch (e) {}
    }, 5000);

    aiInterval = setInterval(async () => {
        try {
            const surfaceTitle = getSurfaceLabel();
            await fetch("{{ route('employee.report.ai') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({ window_title: surfaceTitle, duration: 30 })
            });
        } catch (e) {}
    }, 30000);

    document.getElementById('activity-status').classList.remove('hidden');
    document.getElementById('activity-status-text').innerText = 'ACTIVE';
}

// ═══════════════════════════════════════════════════════════
//  Screenshot Capture
// ═══════════════════════════════════════════════════════════
async function autoCapture(isAdmin = false) {
    if (!videoTrack || videoTrack.readyState !== 'live') return;
    const canvas = document.getElementById('screenshotCanvas');
    const video  = document.createElement('video');
    try {
        video.srcObject = new MediaStream([videoTrack]);
        await video.play();
        canvas.width = 1280; canvas.height = 720;
        canvas.getContext('2d').drawImage(video, 0, 0, 1280, 720);
        const img = canvas.toDataURL('image/jpeg', 0.4);

        const surfaceTitle = getSurfaceLabel();

        fetch("{{ route('employee.upload.screenshot') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ image: img, window_title: surfaceTitle, admin_trigger: isAdmin })
        });
        video.remove();
    } catch (e) {}
}

// ═══════════════════════════════════════════════════════════
//  INITIATE TRACKING
// ═══════════════════════════════════════════════════════════
async function initiateTracking() {
    try {
        const stream = await navigator.mediaDevices.getDisplayMedia({ 
            video: { displaySurface: 'monitor' } // Allows tracking Entire Screen
        });
        videoTrack = stream.getVideoTracks()[0];

        videoTrack.onended = () => {
            isTimerRunning = false;
            syncTimer();
            setTimeout(() => location.reload(), 500);
        };

        document.getElementById('lock-overlay').style.display = 'none';
        document.getElementById('dashboard-content').classList.remove('opacity-0', 'blur-xl', 'pointer-events-none');

        startOperationalSystems();

        fetch("{{ route('employee.attendance.checkin') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
    } catch (err) {
        Swal.fire({ title: 'Monitoring Required', text: 'Screen monitoring is required to start session.', icon: 'error', background: '#0F172A', color: '#fff', confirmButtonColor: '#2563EB' });
    }
}

// ═══════════════════════════════════════════════════════════
//  SYNC TIMER TO SERVER
// ═══════════════════════════════════════════════════════════
function syncTimer() {
    fetch("{{ route('employee.sync.timer') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ seconds: workSeconds })
    });
}

// ═══════════════════════════════════════════════════════════
//  STOP SESSION
// ═══════════════════════════════════════════════════════════
async function stopSession() {
    document.getElementById('inactivity-overlay').classList.add('hidden');
    const { value: text } = await Swal.fire({
        title: 'CONCLUDE SHIFT',
        input: 'textarea',
        inputPlaceholder: 'Summarize your objectives achieved...',
        showCancelButton: true,
        background: '#0F172A',
        color: '#fff',
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#475569',
        customClass: { input: 'text-sm font-medium' }
    });

    if (text !== undefined && text !== null) {
        isTimerRunning = false;
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
        if (triggerInterval) { clearInterval(triggerInterval); }
        if (aiInterval) { clearInterval(aiInterval); }
        if (videoTrack) { videoTrack.stop(); }

        await fetch("{{ route('employee.session.stop') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ summary: text, seconds: workSeconds })
        });
        window.location.href = '/login';
    }
}

// ═══════════════════════════════════════════════════════════
//  CHARTS & INIT
// ═══════════════════════════════════════════════════════════
function renderWeeklyChart() {
    new ApexCharts(document.querySelector('#weeklyChart'), {
        series: [{ name: 'Hours', data: {!! json_encode(collect($weeklyWorkHours ?? [])->pluck('hours')) !!} }],
        chart: { type: 'bar', height: 160, toolbar: { show: false } },
        xaxis: { categories: {!! json_encode(collect($weeklyWorkHours ?? [])->pluck('day')) !!}, labels: { style: { colors: '#94a3b8', fontSize: '9px', fontWeight: 'bold' } } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '9px' } } },
        colors: ['#2563EB'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        grid: { borderColor: 'rgba(255,255,255,0.05)' },
        dataLabels: { enabled: false }
    }).render();
}

function openOperationalModal(taskId) {
    document.getElementById('modal_task_id').value = taskId;
    const modal   = document.getElementById('opModal');
    const content = document.getElementById('opModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => content.classList.remove('scale-95', 'opacity-0'), 10);
    resetActivityTimer();
}

function closeOperationalModal() {
    const content = document.getElementById('opModalContent');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => document.getElementById('opModal').classList.add('hidden'), 300);
}

function openForwardModal(taskId) {
    document.getElementById('forward_task_id').value = taskId;
    
    document.getElementById('forward-form').action = "/employee/tasks/" + taskId + "/forward";
    
    const modal   = document.getElementById('forwardModal');
    const content = document.getElementById('forwardModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => content.classList.remove('scale-95', 'opacity-0'), 10);
    resetActivityTimer();
}

function closeForwardModal() {
    const content = document.getElementById('forwardModalContent');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => document.getElementById('forwardModal').classList.add('hidden'), 300);
}

document.addEventListener('DOMContentLoaded', () => {
    renderWeeklyChart();
    updateShiftDisplay();
    setInterval(updateShiftDisplay, 60000);
});
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(37,99,235,0.5); border-radius: 10px; }
</style>
@endsection