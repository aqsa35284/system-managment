@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        {{-- 🛰️ 1. MAIN PROFILE CARD --}}
        <div class="bg-white rounded-[3.5rem] shadow-2xl p-12 border border-slate-100 mb-8 flex flex-col md:flex-row gap-12 items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-500/5 rounded-bl-[5rem]"></div>

            {{-- Profile Image --}}
            <div class="w-64 h-64 bg-indigo-600 rounded-[3.5rem] flex items-center justify-center text-8xl text-white font-black italic shadow-2xl border-8 border-white overflow-hidden shrink-0">
                @if($employee->profile_image)
                    <img src="{{ asset('storage/'.$employee->profile_image) }}" class="w-full h-full object-cover">
                @else
                    {{ substr($employee->name, 0, 1) }}
                @endif
            </div>
            
            <div class="flex-1 text-center md:text-left z-10">
                <div class="flex flex-wrap gap-2 mb-4 justify-center md:justify-start">
                    <span class="bg-indigo-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Fleet Node</span>
                    <span class="bg-emerald-50 text-emerald-600 px-4 py-1 rounded-full text-[10px] font-black uppercase border border-emerald-100">{{ $employee->status ?? 'Active' }}</span>
                </div>
                <h2 class="text-6xl lg:text-7xl font-black text-slate-800 uppercase italic tracking-tighter leading-tight">{{ $employee->name }}</h2>
                <p class="text-indigo-600 text-2xl font-bold italic mt-2 uppercase tracking-widest">{{ $employee->designation }}</p>
                
                {{-- ⭐ ACTION HUB (THE BUTTONS) --}}
                <div class="flex flex-wrap gap-4 mt-10 justify-center md:justify-start">
                    <button onclick="triggerPulse({{ $employee->id }})" class="group flex items-center gap-3 bg-slate-900 text-white px-8 py-4 rounded-2xl font-black uppercase italic text-xs tracking-widest hover:bg-rose-600 transition-all shadow-xl">
                        <i class="fas fa-camera animate-pulse text-rose-400 group-hover:text-white"></i>
                        Trigger Pulse
                    </button>
                    
                    <a href="{{ route('admin.employees.pulses', $employee->id) }}" class="flex items-center gap-3 bg-white text-indigo-600 border-2 border-indigo-100 px-8 py-4 rounded-2xl font-black uppercase italic text-xs tracking-widest hover:border-indigo-600 transition-all shadow-lg">
                        <i class="fas fa-images"></i>
                        Gallery
                    </a>

                    {{-- ✍️ NEW: Send Feedback Button --}}
                    <button onclick="openFeedbackModal()" class="flex items-center gap-3 bg-indigo-50 text-indigo-600 px-8 py-4 rounded-2xl font-black uppercase italic text-xs tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                        <i class="fas fa-comment-dots"></i>
                        Send Review
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- 📊 LEFT: AI LOGS --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                    <h4 class="text-xs font-black text-slate-800 uppercase italic tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-brain text-rose-500"></i> AI Interactivity (Today)
                    </h4>
                    <div class="space-y-4">
                        @forelse($aiLogs as $log)
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-500 shadow-sm"><i class="fas fa-robot"></i></div>
                                <div>
                                    <p class="text-xs font-black text-slate-700 uppercase">{{ $log->ai_tool }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $log->activity_title }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-black text-slate-600">{{ round($log->duration_seconds / 60) }}m</span>
                        </div>
                        @empty
                        <p class="text-center py-10 text-slate-400 italic text-xs uppercase font-black">No AI signals today</p>
                        @endforelse
                    </div>
                </div>

                {{-- 📑 NEW: RECENT SHIFT REPORTS --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                    <h4 class="text-xs font-black text-slate-800 uppercase italic tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-file-invoice text-indigo-500"></i> Terminal Shift Intel
                    </h4>
                    <div class="space-y-4">
                        @php $recentShifts = \App\Models\ShiftReport::where('user_id', $employee->id)->latest()->take(3)->get(); @endphp
                        @forelse($recentShifts as $report)
                        <div class="p-6 rounded-3xl border border-slate-100 bg-slate-50/50">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-slate-900 text-white px-3 py-1 rounded-lg text-[9px] font-black italic">{{ $report->report_date }}</span>
                                <span class="text-indigo-600 font-black text-[10px] uppercase tracking-tighter">{{ $report->shift_name }} CYCLE</span>
                            </div>
                            <p class="text-[11px] text-slate-600 italic leading-relaxed mb-4">"{{ $report->task_summary }}"</p>
                            <div class="flex gap-4">
                                <div class="text-[9px] font-black uppercase text-slate-400">AI: <span class="text-rose-500">{{ $report->ai_percentage }}%</span></div>
                                <div class="text-[9px] font-black uppercase text-slate-400">Pulses: <span class="text-indigo-500">{{ $report->pulse_count }}</span></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center py-10 text-slate-400 italic text-xs">No shift reports submitted yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 🛡️ RIGHT: SURVEILLANCE & BIO --}}
            <div class="space-y-8">
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl text-white">
                    <h5 class="text-[10px] font-black uppercase italic tracking-widest mb-8 opacity-50">Surveillance Hub</h5>
                    <div class="space-y-6">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[9px] font-bold text-indigo-300 uppercase mb-1">Today's Pulses</p>
                                <h4 class="text-4xl font-black italic">{{ $todayPulseCount ?? 0 }}</h4>
                            </div>
                            <i class="fas fa-satellite-dish text-indigo-500 text-3xl animate-pulse"></i>
                        </div>
                        <div class="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-400 h-full" style="width: {{ min(($todayPulseCount ?? 0) * 5, 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase italic mb-6">Operator Metadata</p>
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Node</span>
                            <span class="text-[10px] font-black text-slate-700 uppercase">{{ $employee->department->name ?? 'Fleet' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Package</span>
                            <span class="text-[10px] font-black text-emerald-600">Rs. {{ number_format($employee->salary ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 📝 FEEDBACK MODAL --}}
<div id="feedbackModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-[3rem] shadow-2xl p-10 border border-white/20">
        <h3 class="text-2xl font-black text-slate-800 uppercase italic tracking-tighter mb-2">Technical Review</h3>
        <p class="text-xs text-slate-400 uppercase font-bold mb-8">Broadcasting evaluation to {{ $employee->name }}</p>
        
        <form action="{{ route('admin.feedback.store') }}" method="POST">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="task_id" value="0"> {{-- Global Profile Feedback --}}
            
            <div class="mb-6">
                <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block">Performance Rating (1-10)</label>
                <input type="number" name="rating_score" min="1" max="10" value="8" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-black text-slate-700">
            </div>

            <div class="mb-6">
                <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block">Commander's Note</label>
                <textarea name="review_note" rows="4" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm italic font-medium text-slate-700" placeholder="Enter review..."></textarea>
            </div>

            <div class="mb-8">
                <select name="status" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-xs font-black uppercase text-slate-700">
                    <option value="excellent">Excellent Performance</option>
                    <option value="good">Good Progress</option>
                    <option value="needs_improvement">Needs Improvement</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="button" onclick="closeFeedbackModal()" class="flex-1 text-xs font-black uppercase text-slate-400">Abort</button>
                <button type="submit" class="flex-[2] bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-xs shadow-lg">Transmit Intel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openFeedbackModal() { document.getElementById('feedbackModal').classList.remove('hidden'); }
    function closeFeedbackModal() { document.getElementById('feedbackModal').classList.add('hidden'); }
    
    function triggerPulse(userId) {
        fetch(`/admin/employees/${userId}/trigger-screenshot`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                Swal.fire({ title: 'PULSE SENT', icon: 'success', background: '#020617', color: '#fff', showConfirmButton: false, timer: 2000 });
            }
        });
    }
</script>
@endsection