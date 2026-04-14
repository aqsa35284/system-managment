@extends('layouts.employee')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- 🛡️ 1. LOCK OVERLAY --}}
<div id="lock-overlay" class="fixed inset-0 z-[9999] bg-[#020617]/95 backdrop-blur-xl flex items-center justify-center transition-all duration-700">
    <div class="text-center p-12 bg-slate-900/50 border border-indigo-500/20 rounded-[4rem] shadow-2xl max-w-md w-full">
        <div class="w-20 h-20 bg-indigo-600/20 rounded-full flex items-center justify-center mx-auto mb-6 border border-indigo-500/30 text-indigo-400">
            <i class="fas fa-satellite-dish text-3xl animate-pulse"></i>
        </div>
        <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter mb-4">Node Link Required</h2>
        <p class="text-slate-400 text-[9px] mb-8 uppercase font-black tracking-widest">Establish screen connection to resume session</p>
        <button onclick="initiateTracking()" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-4 rounded-2xl font-black uppercase italic text-xs transition-all shadow-lg active:scale-95">
            Establish Signal
        </button>
    </div>
</div>

{{-- 🚀 2. MAIN HUB --}}
<div id="dashboard-content" class="opacity-0 blur-xl pointer-events-none transition-all duration-1000">
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative z-10 p-6 md:p-12">
        
        {{-- HEADER SECTION --}}
        <div class="mb-10 bg-white/10 backdrop-blur-md rounded-3xl p-6 md:p-8 shadow-2xl border border-white/20">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff' }}" 
                             class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-xl">
                        <div id="activity-node-status" class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                            <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white uppercase tracking-tight">Operator <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">Node</span></h2>
                        <p class="text-white/60 text-[10px] md:text-xs font-medium uppercase tracking-widest mt-2">
                            ID: {{ $user->name }} | 
                            <span class="text-indigo-300">SHIFT: <span id="shift-display" class="font-black">DETECTING...</span></span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-4 rounded-2xl shadow-xl flex flex-col items-center min-w-[140px] border border-white/10">
                        <p class="text-[8px] font-black uppercase tracking-widest opacity-80 mb-1">Session Duration</p>
                        <p id="session-timer" class="text-xl font-mono font-bold tracking-tight text-white">00:00:00</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-xl border border-white/20 flex flex-col items-center min-w-[140px]">
                        <p class="text-[8px] font-black uppercase tracking-widest text-white/60 mb-1">Telemetry Status</p>
                        <p id="sync-status" class="text-[10px] font-mono font-bold text-emerald-400 mt-1 uppercase tracking-tighter">Linking...</p>
                    </div>
                    <button onclick="stopSession()" class="w-12 h-12 flex items-center justify-center bg-rose-500/20 text-rose-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all border border-rose-500/30 shadow-lg">
                        <i class="fas fa-power-off text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <h3 class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-6 italic">Deployment Intel</h3>
                    <div class="space-y-4">
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                            <p class="text-[8px] font-black text-white/50 uppercase mb-1 tracking-tighter">System Status</p>
                            <p id="monitor-text" class="text-emerald-400 font-bold text-[10px] uppercase italic">Signal Secured</p>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-900/20 backdrop-blur-md rounded-[2.5rem] p-8 border border-emerald-500/20 shadow-2xl">
                    <h4 class="text-xs font-black text-emerald-400 uppercase tracking-widest mb-6 italic">Commander's Briefing</h4>
                    <div class="space-y-4 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($adminFeedbacks ?? [] as $feedback)
                        <div class="bg-white/5 rounded-2xl p-4 border border-emerald-500/10">
                            <div class="flex justify-between mb-2">
                                <span class="text-[8px] font-black bg-emerald-500 text-white px-2 py-0.5 rounded italic">RATING: {{ $feedback->rating_score }}/10</span>
                                <span class="text-[7px] text-white/30">{{ $feedback->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10px] text-slate-200 italic leading-relaxed">"{{ $feedback->review_note }}"</p>
                        </div>
                        @empty
                        <p class="text-center text-white/20 italic text-[9px] py-10 tracking-widest uppercase">Waiting for command evaluation...</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white/10 backdrop-blur-md rounded-[3rem] p-8 shadow-2xl border border-white/20 min-h-[450px]">
                    <h4 class="text-xl font-bold text-white uppercase mb-8 italic tracking-tight">Active <span class="text-indigo-400">Objectives</span></h4>
                    <div class="space-y-6">
                        @forelse($tasks as $task)
                        <div class="bg-white/5 rounded-3xl p-6 border border-white/10 hover:bg-white/10 transition-all group">
                            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                <div class="flex-1 w-full">
                                    <span class="text-[8px] font-black px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-lg uppercase italic border border-indigo-500/20">{{ $task->status }}</span>
                                    <h5 class="text-2xl font-bold text-white mt-3 tracking-tight">{{ $task->title }}</h5>
                                    <p class="text-xs text-white/50 mt-1 italic">{{ $task->description }}</p>
                                    <div class="mt-5 w-full bg-white/5 h-1 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-full transition-all duration-1000" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full md:w-auto">
                                    <button onclick="openOperationalModal({{ $task->id }})" class="flex-1 md:flex-none bg-indigo-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase italic shadow-lg active:scale-95">Update Intel</button>
                                    <button onclick="openForwardModal({{ $task->id }}, '{{ $task->title }}')" class="bg-white/5 text-white px-4 py-3 rounded-xl border border-white/10 hover:bg-purple-600 transition-all">
                                        <i class="fas fa-share-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-white/20 py-20 uppercase font-black text-xs italic tracking-widest">No active nodes assigned</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
<div id="forwardModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900 max-w-sm w-full rounded-[2.5rem] border border-white/10 shadow-2xl p-8">
        <h3 class="text-xs font-black text-indigo-400 uppercase italic mb-6 tracking-widest text-center">Reassign Objective</h3>
        <p id="forward_task_title" class="text-white text-[10px] mb-6 opacity-60 text-center"></p>
        <form id="forwardForm" onsubmit="handleForward(event)">
            @csrf
            <input type="hidden" name="task_id" id="forward_task_id">
            <select name="receiver_id" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-4 text-xs font-bold text-white focus:ring-2 focus:ring-indigo-500 outline-none mb-6">
                <option value="" class="bg-slate-900">Select Operator...</option>
                @foreach($fleetMembers ?? [] as $member)
                    <option value="{{ $member->id }}" class="bg-slate-900">{{ $member->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-3">
                <button type="button" onclick="closeForwardModal()" class="flex-1 text-xs font-black uppercase text-white/50">Cancel</button>
                <button type="submit" class="flex-[2] bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest">Confirm</button>
            </div>
        </form>
    </div>
</div>

<div id="opModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white max-w-lg w-full rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="p-6 bg-slate-900 flex justify-between items-center border-b border-white/10">
            <h3 class="text-[10px] font-black uppercase italic tracking-widest text-indigo-400">Operational Log Entry</h3>
            <button onclick="closeOperationalModal()" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="task_id" id="modal_task_id">
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="completion_percentage" min="0" max="100" placeholder="Progress %" required class="bg-slate-50 border-none rounded-2xl px-4 py-4 text-xs font-bold text-slate-900">
                <input type="text" name="blockers" placeholder="Blockers?" class="bg-slate-50 border-none rounded-2xl px-4 py-4 text-xs font-bold text-slate-900">
            </div>
            <textarea name="update_note" rows="4" required class="w-full bg-slate-50 border-none rounded-2xl px-4 py-4 text-xs font-bold text-slate-900" placeholder="Enter achievements..."></textarea>
            <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-3xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95 shadow-lg">Transmit Report</button>
        </form>
    </div>
</div>

<canvas id="screenshotCanvas" style="display:none;"></canvas>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let videoTrack = null;
    let timerInterval = null;
    let idleSeconds = 0;
    const IDLE_LIMIT = 1200; 
    const captureIntervals = [1, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55];
    
    let workSeconds = parseInt(localStorage.getItem('currentWorkSeconds')) || {{ $totalSeconds ?? 0 }};
    
    // ⭐ SAFE UI UPDATE LOGIC
    function updateUI(id, content, isHTML = false) {
        const el = document.getElementById(id);
        if (el) {
            isHTML ? el.innerHTML = content : el.innerText = content;
        }
    }

    // ⭐ 1. LIVE SYNC TIMER
    function syncTimer() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch("{{ route('employee.sync.timer') }}", {
            method: "POST", 
            headers: { 
                "X-CSRF-TOKEN": csrfToken, 
                "Content-Type": "application/json",
                "Accept": "application/json" 
            },
            body: JSON.stringify({ seconds: workSeconds })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // FIXED: Safe update to prevent crash
                updateUI('sync-status', `<span class="text-emerald-400 font-bold uppercase tracking-tighter">● SECURED: ${new Date().toLocaleTimeString()}</span>`, true);
            }
        }).catch(err => console.error("Sync Failure", err));
    }

    // ⭐ 2. ADMIN TRIGGER POLLING
    setInterval(async () => {
        try {
            const res = await fetch("{{ route('employee.check.trigger') }}");
            if (res.ok) {
                const data = await res.json();
                if(data.trigger) captureAndUpload();
            }
        } catch(e) {}
    }, 8000);

    function updateTimerUI() {
        const h = Math.floor(workSeconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((workSeconds % 3600) / 60).toString().padStart(2, '0');
        const s = (workSeconds % 60).toString().padStart(2, '0');
        
        updateUI('session-timer', `${h}:${m}:${s}`);
        localStorage.setItem('currentWorkSeconds', workSeconds);
    }

    function startOperationalSystems() {
        if(timerInterval) clearInterval(timerInterval);
        updateShiftDisplay();
        
        timerInterval = setInterval(() => {
            idleSeconds++;
            if (idleSeconds < IDLE_LIMIT) {
                workSeconds++;
                updateTimerUI();
                if(workSeconds % 20 === 0) syncTimer(); 
            } else if (idleSeconds === IDLE_LIMIT) {
                sendActivitySignal('idle');
            }

            if (captureIntervals.includes(Math.floor(workSeconds / 60) % 60) && (workSeconds % 60 === 0)) {
                captureAndUpload();
            }
        }, 1000);
    }

    async function initiateTracking() {
        try {
            const stream = await navigator.mediaDevices.getDisplayMedia({ video: { displaySurface: "monitor" } });
            videoTrack = stream.getVideoTracks()[0];
            document.getElementById('lock-overlay').style.display = 'none';
            document.getElementById('dashboard-content').classList.remove('opacity-0', 'blur-xl', 'pointer-events-none');
            sessionStorage.setItem('is_tracking_active', 'true');
            startOperationalSystems();
            videoTrack.onended = () => {
                sessionStorage.removeItem('is_tracking_active');
                location.reload();
            };
        } catch (err) {
            Swal.fire({ title: 'SIGNAL LOST', text: 'Screen sharing required', icon: 'error', background: '#020617', color: '#fff' });
        }
    }

    async function captureAndUpload() {
        if (!videoTrack || videoTrack.readyState !== 'live') return;
        const canvas = document.getElementById('screenshotCanvas');
        const video = document.createElement('video');
        try {
            video.srcObject = new MediaStream([videoTrack]);
            await video.play();
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            fetch("{{ route('employee.upload.screenshot') }}", { 
                method: "POST", headers: { "X-CSRF-TOKEN": '{{ csrf_token() }}', "Content-Type": "application/json" }, 
                body: JSON.stringify({ image: canvas.toDataURL('image/jpeg', 0.2), window_title: document.title })
            });
            video.srcObject = null;
        } catch (e) {}
    }

    function openForwardModal(id, title) {
        document.getElementById('forward_task_id').value = id;
        updateUI('forward_task_title', "Forwarding: " + title);
        document.getElementById('forwardModal').classList.remove('hidden');
    }

    function closeForwardModal() { document.getElementById('forwardModal').classList.add('hidden'); }

    async function handleForward(e) {
        e.preventDefault();
        const taskId = document.getElementById('forward_task_id').value;
        const receiverId = e.target.receiver_id.value;
        const res = await fetch(`/employee/tasks/${taskId}/forward`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ receiver_id: receiverId })
        });
        if((await res.json()).success) location.reload();
    }

    function openOperationalModal(id) {
        document.getElementById('modal_task_id').value = id;
        document.getElementById('opModal').classList.remove('hidden');
    }
    function closeOperationalModal() { document.getElementById('opModal').classList.add('hidden'); }

    function sendActivitySignal(statusVal) {
        fetch('/employee/attendance/report-idle', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: statusVal })
        });
    }

    async function stopSession() {
        const { value: summary } = await Swal.fire({ title: 'SHIFT SUMMARY', input: 'textarea', background: '#020617', color: '#fff', showCancelButton: true });
        if (summary) {
            await syncTimer(); 
            fetch("{{ route('employee.session.stop') }}", { 
                method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
                body: JSON.stringify({ summary: summary })
            }).then(() => {
                localStorage.removeItem('currentWorkSeconds');
                window.location.href = "/login";
            });
        }
    }

    function updateShiftDisplay() {
        const hour = new Date().getHours();
        let shiftName = "EVENING/NIGHT";
        if (hour >= 5 && hour < 12) shiftName = "MORNING CYCLE";
        else if (hour >= 12 && hour < 17) shiftName = "AFTERNOON CYCLE";
        
        updateUI('shift-display', shiftName);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateTimerUI();
        setInterval(() => {
            const clockElem = document.getElementById('live-clock');
            if (clockElem) clockElem.innerText = new Date().toLocaleTimeString('en-GB', { hour12: false });
        }, 1000);
        if (sessionStorage.getItem('is_tracking_active') === 'true') initiateTracking();
    });

    window.onmousemove = window.onkeydown = window.onclick = () => { 
        if (idleSeconds >= IDLE_LIMIT) sendActivitySignal('present');
        idleSeconds = 0; 
    };
</script>

<style>
    .glass-effect { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.3); border-radius: 10px; }
</style>
@endsection