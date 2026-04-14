@extends('layouts.employee')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="lock-overlay" class="fixed inset-0 z-[9999] bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 backdrop-blur-xl flex items-center justify-center transition-all duration-700">
    <div class="text-center p-12 bg-slate-900/60 backdrop-blur-lg border border-indigo-500/30 rounded-[3rem] shadow-2xl max-w-md w-full">
        <div class="w-24 h-24 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-8 border border-indigo-400/50 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-indigo-600 opacity-20 animate-ping"></div>
            <i class="fas fa-satellite-dish text-4xl text-white relative z-10"></i>
        </div>
        <h2 class="text-3xl font-bold text-white uppercase tracking-tighter mb-4 bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Node Link Required</h2>
        <p class="text-slate-400 text-xs mb-8 uppercase font-bold tracking-widest">Establish secure connection to resume session</p>
        <button onclick="initiateTracking()" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white py-4 rounded-2xl font-bold uppercase text-sm transition-all shadow-lg active:scale-95">
            <i class="fas fa-link mr-2"></i>Establish Signal
        </button>
    </div>
</div>

<div id="dashboard-content" class="opacity-0 blur-xl pointer-events-none transition-all duration-1000">
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950/50 to-purple-950/50 relative z-10 p-6 md:p-12 text-white">
        
        <div class="mb-10 bg-white/10 backdrop-blur-md rounded-3xl p-6 md:p-8 shadow-2xl border border-white/20">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff' }}" 
                             class="w-24 h-24 rounded-full object-cover border-2 border-indigo-400 shadow-xl group-hover:scale-105 transition-transform duration-300">
                        <div id="activity-node-status" class="absolute -bottom-1 -right-1 w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                            <div class="w-4 h-4 bg-white rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white uppercase tracking-tight">Operator <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Node</span></h2>
                        <p class="text-white/60 text-xs md:text-sm font-medium uppercase tracking-widest mt-2">
                            ID: {{ $user->name }} | 
                            <span class="text-indigo-300">SHIFT: <span id="shift-display" class="font-bold">DETECTING...</span></span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <div class="bg-slate-800/50 border border-white/10 px-5 py-4 rounded-2xl flex flex-col items-center min-w-[100px]">
                        <p class="text-[8px] font-bold uppercase text-white/40 tracking-widest mb-1">Local Signal</p>
                        <p id="live-clock" class="text-lg font-mono font-bold text-emerald-400 tracking-tighter">00:00:00</p>
                    </div>
                    <div class="bg-slate-800/50 border border-white/10 px-5 py-4 rounded-2xl flex flex-col items-center min-w-[100px]">
                        <p class="text-[8px] font-bold uppercase text-white/40 tracking-widest mb-1">Daily Logins</p>
                        <p class="text-lg font-bold text-indigo-400">{{ \App\Models\Attendance::where('user_id', Auth::id())->whereDate('date', now())->count() }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 rounded-2xl shadow-xl flex flex-col items-center min-w-[140px] border border-white/10">
                        <p class="text-[8px] font-bold uppercase tracking-widest opacity-80 mb-1">Session Duration</p>
                        <p id="session-timer" class="text-xl font-mono font-bold tracking-tight text-white">00:00:00</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-xl border border-white/20 flex flex-col items-center min-w-[140px]">
                        <p class="text-[8px] font-bold uppercase tracking-widest text-white/60 mb-1">Telemetry Status</p>
                        <p id="sync-status" class="text-[10px] font-mono font-bold text-emerald-400 mt-1 uppercase tracking-tighter italic">● SECURED</p>
                    </div>
                    <button onclick="stopSession()" class="w-12 h-12 flex items-center justify-center bg-rose-500/20 text-rose-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all border border-rose-500/30 shadow-lg group">
                        <i class="fas fa-power-off text-sm group-hover:rotate-90 transition-transform"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <h3 class="text-sm font-bold text-indigo-400 uppercase tracking-widest mb-6 flex items-center"><i class="fas fa-satellite-dish mr-2"></i>Deployment Intel</h3>
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                        <p class="text-[8px] font-bold text-white/50 uppercase mb-2 tracking-tighter">System Status</p>
                        <p id="monitor-text" class="text-emerald-400 font-bold text-sm uppercase italic flex items-center">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>Signal Secured
                        </p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-900/30 to-teal-900/30 backdrop-blur-md rounded-[2.5rem] p-8 border border-emerald-500/20 shadow-2xl">
                    <h4 class="text-sm font-bold text-emerald-400 uppercase tracking-widest mb-6 flex items-center"><i class="fas fa-broadcast-tower mr-2"></i>Commander's Briefing</h4>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($adminFeedbacks ?? [] as $feedback)
                        <div class="bg-white/5 rounded-2xl p-4 border border-emerald-500/10 transition-all duration-300">
                            <div class="flex justify-between mb-2">
                                <span class="text-[8px] font-bold bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-3 py-1 rounded-full italic">RATING: {{ $feedback->rating_score }}/10</span>
                                <span class="text-[7px] text-white/30">{{ $feedback->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-200 italic leading-relaxed">"{{ $feedback->review_note }}"</p>
                            <p class="text-[8px] text-indigo-400 mt-3 font-bold uppercase flex items-center"><i class="fas fa-tasks mr-1"></i>OBJ: {{ $feedback->task->title ?? 'N/A' }}</p>
                        </div>
                        @empty
                        <div class="text-center py-10 opacity-20"><i class="fas fa-inbox text-4xl mb-4"></i><p class="text-sm uppercase tracking-widest">Waiting for Command...</p></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white/10 backdrop-blur-md rounded-[3rem] p-8 shadow-2xl border border-white/20 min-h-[450px]">
                    <h4 class="text-xl font-bold text-white uppercase mb-8 flex items-center">Active <span class="text-indigo-400 ml-2">Objectives</span></h4>
                    <div class="space-y-6">
                        @forelse($tasks as $task)
                        <div class="bg-white/5 rounded-3xl p-6 border border-white/10 hover:border-indigo-500/30 transition-all duration-300">
                            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                <div class="flex-1 w-full">
                                    <span class="text-[8px] font-bold px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full uppercase border border-indigo-500/20 inline-flex items-center"><span class="w-2 h-2 bg-indigo-400 rounded-full mr-2 animate-pulse"></span>{{ $task->status }}</span>
                                    <h5 class="text-2xl font-bold text-white mt-3 tracking-tight">{{ $task->title }}</h5>
                                    <p class="text-sm text-white/60 mt-2 italic">{{ $task->description }}</p>
                                    <div class="mt-5 w-full bg-white/5 h-2 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-full transition-all duration-1000" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-white/40 mt-2 font-mono">{{ $task->completion_percentage ?? 0 }}% Complete</p>
                                </div>
                                <div class="flex gap-2 w-full md:w-auto">
                                    <button onclick="openOperationalModal({{ $task->id }})" class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-xl text-xs font-bold uppercase transition-all shadow-lg active:scale-95"><i class="fas fa-edit mr-2"></i>Update Intel</button>
                                    <button onclick="openForwardModal({{ $task->id }}, '{{ $task->title }}')" class="bg-white/5 text-white px-4 py-3 rounded-xl border border-white/10 hover:bg-purple-600/20 transition-all"><i class="fas fa-share-alt"></i></button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-20 opacity-20"><i class="fas fa-clipboard-list text-6xl mb-4"></i><p class="uppercase font-bold text-sm tracking-widest">No assigned nodes</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="forwardModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div id="forwardModalContent" class="bg-slate-900 max-w-sm w-full rounded-[2.5rem] border border-white/10 shadow-2xl p-8 transform scale-95 opacity-0 transition-all duration-300">
        <h3 class="text-sm font-bold text-indigo-400 uppercase mb-6 tracking-widest text-center italic">Reassign Objective</h3>
        <p id="forward_task_title" class="text-white text-xs mb-6 opacity-60 text-center bg-white/5 p-3 rounded-lg"></p>
        <form onsubmit="handleForward(event)">
            @csrf
            <input type="hidden" name="task_id" id="forward_task_id">
            <select id="receiver_id_select" name="receiver_id" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-4 text-sm font-bold text-white outline-none mb-6 appearance-none">
                <option value="" class="bg-slate-900">Select Operator...</option>
                @foreach($fleetMembers ?? [] as $member) <option value="{{ $member->id }}" class="bg-slate-900">{{ $member->name }}</option> @endforeach
            </select>
            <div class="flex gap-3">
                <button type="button" onclick="closeForwardModal()" class="flex-1 text-sm font-bold uppercase text-white/50">Cancel</button>
                <button type="submit" class="flex-[2] bg-indigo-600 text-white py-4 rounded-2xl font-bold uppercase text-xs tracking-widest shadow-lg">Confirm</button>
            </div>
        </form>
    </div>
</div>

<div id="opModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-4">
    <div id="opModalContent" class="bg-white max-w-lg w-full rounded-[3rem] shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <div class="p-6 bg-slate-900 flex justify-between items-center border-b border-white/10 text-indigo-400">
            <h3 class="text-xs font-bold uppercase tracking-widest">Operational Log Entry</h3>
            <button onclick="closeOperationalModal()" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="task_id" id="modal_task_id">
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="completion_percentage" min="0" max="100" placeholder="Progress %" required class="bg-slate-100 border-none rounded-2xl px-4 py-4 text-sm font-bold text-slate-900">
                <input type="text" name="blockers" placeholder="Blockers?" class="bg-slate-100 border-none rounded-2xl px-4 py-4 text-sm font-bold text-slate-900">
            </div>
            <textarea name="update_note" rows="4" required class="w-full bg-slate-100 border-none rounded-2xl px-4 py-4 text-sm font-bold text-slate-900 resize-none" placeholder="Achievements..."></textarea>
            <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-3xl font-bold uppercase text-xs tracking-widest shadow-lg active:scale-95 transition-all">Transmit Report</button>
        </form>
    </div>
</div>

<canvas id="screenshotCanvas" style="display:none;"></canvas>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let videoTrack = null;
    let workSeconds = {{ $totalSeconds ?? 0 }};
    
    // Persistent Timer
    if(workSeconds === 0) localStorage.removeItem('currentWorkSeconds');
    else workSeconds = parseInt(localStorage.getItem('currentWorkSeconds')) || workSeconds;

    // 🕒 1. LIVE CLOCK
    function startLiveClock() {
        setInterval(() => {
            const el = document.getElementById('live-clock');
            if(el) el.innerText = new Date().toLocaleTimeString('en-GB', { hour12: false });
        }, 1000);
    }

    // 🚀 2. FORWARD LOGIC
    async function handleForward(e) {
        e.preventDefault();
        const taskId = document.getElementById('forward_task_id').value;
        const receiverId = document.getElementById('receiver_id_select').value;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const res = await fetch(`/employee/tasks/${taskId}/forward`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ receiver_id: receiverId })
            });
            const data = await res.json();
            if(data.success) {
                Swal.fire({ title: 'Success!', text: 'Objective Forwarded.', icon: 'success', background: '#020617', color: '#fff' }).then(() => location.reload());
            }
        } catch (error) { console.error(error); }
    }

    // 🛰️ 3. DATABASE SYNC & TIMER
    function syncTimer() {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch("{{ route('employee.sync.timer') }}", {
            method: "POST", 
            headers: { "X-CSRF-TOKEN": token, "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify({ seconds: workSeconds })
        });
    }

    function startOperationalSystems() {
        updateShiftDisplay();
        setInterval(() => {
            workSeconds++;
            const h = Math.floor(workSeconds / 3600).toString().padStart(2, '0');
            const m = Math.floor((workSeconds % 3600) / 60).toString().padStart(2, '0');
            const s = (workSeconds % 60).toString().padStart(2, '0');
            const timerEl = document.getElementById('session-timer');
            if(timerEl) timerEl.innerText = `${h}:${m}:${s}`;
            localStorage.setItem('currentWorkSeconds', workSeconds);

            // ⭐ LOGIC FIX: Har 15 sec baad Sync
            if(workSeconds % 15 === 0) syncTimer();
            
            // ⭐ LOGIC FIX: Har 5 Minute (300 sec) baad Auto Screenshot (Jaisa kal tha)
            if(workSeconds % 300 === 0) captureAndUpload(false); 
        }, 1000);

        // Silent Trigger check har 5 sec
        setInterval(async () => {
            try {
                const res = await fetch("{{ route('employee.check.trigger') }}");
                const data = await res.json();
                if(data.trigger) captureAndUpload(true);
            } catch(e) {}
        }, 5000);
    }

    // 📸 4. SCREENSHOT CAPTURE (PRO VERSION)
    async function captureAndUpload(isSilent) {
        if (!videoTrack || videoTrack.readyState !== 'live') return;
        const canvas = document.getElementById('screenshotCanvas');
        const video = document.createElement('video');
        try {
            video.srcObject = new MediaStream([videoTrack]);
            await video.play();
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const data = canvas.toDataURL('image/jpeg', 0.3); // High Quality
            fetch("{{ route('employee.upload.screenshot') }}", { 
                method: "POST", headers: { "X-CSRF-TOKEN": '{{ csrf_token() }}', "Content-Type": "application/json" }, 
                body: JSON.stringify({ image: data, window_title: isSilent ? "ADMIN_SURVEILLANCE_TRIGGER" : document.title })
            });
            video.srcObject = null;
            video.remove();
        } catch (e) { console.error("Capture Failed"); }
    }

    // 🏁 5. SESSION MANAGEMENT
    async function initiateTracking() {
        try {
            const stream = await navigator.mediaDevices.getDisplayMedia({ video: { displaySurface: "monitor" } });
            videoTrack = stream.getVideoTracks()[0];
            document.getElementById('lock-overlay').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('lock-overlay').style.display = 'none';
                document.getElementById('dashboard-content').classList.remove('opacity-0', 'blur-xl', 'pointer-events-none');
            }, 700);
            startOperationalSystems();
            fetch("{{ route('employee.attendance.checkin') }}", { method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" } });
        } catch (err) { Swal.fire({ title: 'ACCESS DENIED', text: 'Signal required.', icon: 'error' }); }
    }

    async function stopSession() {
        const { value: summary } = await Swal.fire({
            title: 'STOP SESSION?',
            input: 'textarea',
            inputPlaceholder: 'Briefly explain your shift achievements...',
            showCancelButton: true,
            background: '#020617',
            color: '#fff',
            confirmButtonColor: '#4f46e5'
        });
        if (summary) {
            fetch("{{ route('employee.session.stop') }}", { 
                method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
                body: JSON.stringify({ summary: summary })
            }).then(() => {
                localStorage.removeItem('currentWorkSeconds');
                window.location.href = "/login";
            });
        }
    }

    // Modal Helpers
    function openForwardModal(id, title) {
        document.getElementById('forward_task_id').value = id;
        document.getElementById('forward_task_title').innerText = "Forwarding: " + title;
        document.getElementById('forwardModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('forwardModalContent').classList.add('scale-100', 'opacity-100'), 10);
    }
    function closeForwardModal() { 
        document.getElementById('forwardModalContent').classList.remove('scale-100', 'opacity-100');
        setTimeout(() => document.getElementById('forwardModal').classList.add('hidden'), 300);
    }
    function openOperationalModal(id) {
        document.getElementById('modal_task_id').value = id;
        document.getElementById('opModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('opModalContent').classList.add('scale-100', 'opacity-100'), 10);
    }
    function closeOperationalModal() { 
        document.getElementById('opModalContent').classList.remove('scale-100', 'opacity-100');
        setTimeout(() => document.getElementById('opModal').classList.add('hidden'), 300);
    }

    function updateShiftDisplay() {
        const h = new Date().getHours();
        let s = h >= 5 && h < 12 ? "MORNING CYCLE" : (h >= 12 && h < 17 ? "AFTERNOON CYCLE" : "EVENING/NIGHT CYCLE");
        document.getElementById('shift-display').innerText = s;
    }

    document.addEventListener('DOMContentLoaded', startLiveClock);
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.5); border-radius: 10px; }
    * { transition: color 0.3s, background-color 0.3s, transform 0.3s, opacity 0.3s; }
</style>
@endsection