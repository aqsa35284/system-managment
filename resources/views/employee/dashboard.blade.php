@extends('layouts.employee')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

{{-- ═══════════════════════════════════════════════════════════
     NODE LINK OVERLAY
═══════════════════════════════════════════════════════════ --}}
<div id="lock-overlay" class="fixed inset-0 z-[9999] backdrop-blur-xl flex items-center justify-center transition-all duration-700" style="background-color: rgba(1,7,23,0.96);">
    <div class="text-center p-12 rounded-[3rem] shadow-2xl max-w-md w-full relative overflow-hidden" style="background: rgba(5,16,31,0.9); border: 1px solid rgba(210,166,61,0.25);">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-2xl" style="background: rgba(210,166,61,0.06);"></div>

        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 relative overflow-hidden z-10" style="background: linear-gradient(135deg, #d2a63d, #a07828); border: 1px solid rgba(210,166,61,0.5); box-shadow: 0 0 30px rgba(210,166,61,0.25);">
            <div class="absolute inset-0 bg-white opacity-10 animate-ping"></div>
            <i class="fas fa-satellite-dish text-4xl relative z-10" style="color: #010717;"></i>
        </div>

        <h2 class="text-3xl font-black uppercase tracking-widest mb-4 relative z-10" style="color: #eaeef2;">
            Node Link Required
        </h2>

        <p class="text-[10px] mb-8 uppercase font-bold tracking-[0.2em] relative z-10" style="color: rgba(210,166,61,0.7);">
            Establish secure connection to resume session
        </p>

        <button onclick="initiateTracking()"
            class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-lg active:scale-95 relative z-10"
            style="background: #d2a63d; color: #010717; border: none;"
            onmouseover="this.style.background='#e6b84a'"
            onmouseout="this.style.background='#d2a63d'">
            <i class="fas fa-link mr-2"></i> Establish Signal
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     INACTIVITY WARNING OVERLAY
═══════════════════════════════════════════════════════════ --}}
<div id="inactivity-overlay"
    class="fixed inset-0 z-[9998] hidden backdrop-blur-xl flex items-center justify-center" style="background-color: rgba(1,7,23,0.96);">

    <div class="text-center p-12 rounded-[3rem] shadow-2xl max-w-md w-full" style="background: rgba(5,16,31,0.9); border: 1px solid rgba(210,166,61,0.25);">

        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background: rgba(210,166,61,0.1); border: 1px solid rgba(210,166,61,0.25);">
            <i class="fas fa-exclamation-triangle text-3xl animate-pulse" style="color: #d2a63d;"></i>
        </div>

        <h2 class="text-2xl font-black uppercase tracking-widest mb-3" style="color: #eaeef2;">
            Idle Detected
        </h2>

        <p class="text-[10px] mb-2 font-black tracking-[0.2em] uppercase" style="color: #d2a63d;">
            20 Minutes of Inactivity
        </p>

        <p class="text-xs mb-8 font-medium" style="color: #8a9bb0;">
            Timer paused. Are you still working?
        </p>

        <div class="flex gap-3">
            <button onclick="resumeFromInactivity()"
                class="flex-1 py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-lg active:scale-95"
                style="background: #d2a63d; color: #010717;"
                onmouseover="this.style.background='#e6b84a'"
                onmouseout="this.style.background='#d2a63d'">
                <i class="fas fa-play mr-2"></i> Resume
            </button>

            <button onclick="stopSession()"
                class="flex-1 py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all"
                style="background: rgba(239,68,68,0.08); color: #ef4444; border: 1px solid rgba(239,68,68,0.3);"
                onmouseover="this.style.background='#ef4444'; this.style.color='#fff';"
                onmouseout="this.style.background='rgba(239,68,68,0.08)'; this.style.color='#ef4444';">
                <i class="fas fa-power-off mr-2"></i> End Shift
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MAIN DASHBOARD CONTENT
═══════════════════════════════════════════════════════════ --}}
<div id="dashboard-content" class="h-screen overflow-hidden opacity-0 blur-xl pointer-events-none transition-all duration-1000">

    <div class="min-h-screen relative z-10 p-6 md:p-10" style="background-color: #010717; color: #eaeef2;">

        {{-- TOP BAR --}}
        <div class="mb-8 rounded-3xl p-6 md:p-8 shadow-2xl" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.15);">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <img src="{{ auth()->user()->profile_image ? asset('storage/'.auth()->user()->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=d2a63d&color=010717' }}"
                            class="w-20 h-20 rounded-2xl object-cover shadow-xl"
                            style="border: 1px solid rgba(210,166,61,0.4);">
                        <div class="absolute -bottom-2 -right-2 w-5 h-5 rounded-full border-2 flex items-center justify-center shadow-lg" style="background-color: #1dd182; border-color: #05101f;">
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-3xl font-black uppercase tracking-widest" style="color: #eaeef2;">
                            Employee <span style="color: #d2a63d;">Operator</span>
                        </h2>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] mt-1" style="color: #8a9bb0;">
                            {{ auth()->user()->name }} | <span style="color: #d2a63d;">SHIFT: <span id="shift-display" class="font-black" style="color: #eaeef2;">DETECTING...</span></span>
                        </p>
                        <div id="activity-status" class="mt-2 hidden">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[8px] font-black uppercase tracking-widest" style="background: rgba(210,166,61,0.1); border: 1px solid rgba(210,166,61,0.3); color: #d2a63d;">
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color: #d2a63d;"></span>
                                <span id="activity-status-text">Active</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- LIVE CLOCK --}}
                    <div class="px-6 py-3 rounded-2xl flex flex-col items-center min-w-[120px]" style="background-color: #010717; border: 1px solid rgba(210,166,61,0.15);">
                        <p class="text-[8px] font-bold uppercase tracking-widest mb-1" style="color: #8a9bb0;">Current Time</p>
                        <p id="live-clock" class="text-lg font-black tracking-widest" style="color: #eaeef2;">00:00:00</p>
                    </div>

                    {{-- TODAY LOGIN --}}
                    <div class="px-6 py-3 rounded-2xl flex flex-col items-center min-w-[120px]" style="background-color: #010717; border: 1px solid rgba(210,166,61,0.15);">
                        <p class="text-[8px] font-bold uppercase tracking-widest mb-1" style="color: #8a9bb0;">Logins Today</p>
                        <p class="text-lg font-black" style="color: #eaeef2;">
                            {{ \App\Models\Attendance::where('user_id', Auth::id())->whereDate('date', now())->count() }}
                        </p>
                    </div>

                    {{-- TIMER --}}
                    <div class="px-6 py-3 rounded-2xl flex flex-col items-center min-w-[140px]" style="background: linear-gradient(135deg, #d2a63d, #a07828); color: #010717; border: 1px solid rgba(210,166,61,0.5); box-shadow: 0 0 20px rgba(210,166,61,0.2);">
                        <p class="text-[8px] font-bold uppercase tracking-widest mb-1" style="color: rgba(1,7,23,0.7);">Active Session</p>
                        <p id="session-timer" class="text-xl font-mono font-black tracking-widest">00:00:00</p>
                    </div>

                    {{-- STOP SESSION BUTTON --}}
                    <button onclick="stopSession()"
                            class="w-14 h-14 flex items-center justify-center rounded-2xl transition-all shadow-lg"
                            style="background: rgba(239,68,68,0.08); color: #ef4444; border: 1px solid rgba(239,68,68,0.2);"
                            onmouseover="this.style.background='#ef4444'; this.style.color='#fff';"
                            onmouseout="this.style.background='rgba(239,68,68,0.08)'; this.style.color='#ef4444';"
                            title="End Shift">
                        <i class="fas fa-power-off text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- SUMMARY OVERVIEW STATS BOX --}}
        <div class="mb-8">
            <div class="rounded-3xl p-6 shadow-xl flex justify-around items-center" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.15);">
                <div class="text-center w-1/2">
                    <p class="text-[8px] font-black uppercase tracking-[0.2em] mb-1" style="color: #8a9bb0;">Active Tasks</p>
                    <p class="text-2xl font-black" style="color: #eaeef2;">{{ $tasks->count() }}</p>
                </div>
                <div class="h-10 w-[1px]" style="background-color: rgba(210,166,61,0.15);"></div>
                <div class="text-center w-1/2">
                    <p class="text-[8px] font-black uppercase tracking-[0.2em] mb-1" style="color: #8a9bb0;">Hours Worked Today</p>
                    @php $wh = floor($totalSeconds/3600); $wm = floor(($totalSeconds%3600)/60); @endphp
                    <p class="text-2xl font-black" style="color: #1dd182;">{{ $wh }}h {{ $wm }}m</p>
                </div>
            </div>
        </div>

        {{-- GRIDS & HISTORICAL VISUAL ANALYTICS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Commander's Briefing & Weekly Graph --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.15);">
                    <div class="absolute top-0 right-0 w-32 h-32 blur-2xl rounded-full" style="background: rgba(210,166,61,0.06);"></div>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 flex items-center gap-2 relative z-10" style="color: #d2a63d;">
                        <i class="fas fa-broadcast-tower"></i> Commander's Briefing
                    </h4>
                    <div class="space-y-4 max-h-[250px] overflow-y-auto custom-scrollbar pr-2 relative z-10">
                        @forelse($adminFeedbacks as $feedback)
                        <div class="rounded-xl p-4" style="background-color: #010717; border: 1px solid rgba(210,166,61,0.1); border-left: 2px solid #d2a63d;">
                            <div class="flex justify-between mb-2 items-center">
                                <span class="text-[8px] font-black px-2 py-0.5 rounded tracking-widest" style="background: rgba(210,166,61,0.1); color: #d2a63d;">SCORE: {{ $feedback->rating_score }}/10</span>
                                <span class="text-[7px] uppercase tracking-widest font-bold" style="color: #4a5568;">{{ $feedback->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10px] italic font-medium leading-relaxed" style="color: #8a9bb0;">"{{ $feedback->review_note }}"</p>
                        </div>
                        @empty
                        <p class="text-center italic text-[10px] py-10 uppercase tracking-widest font-bold" style="color: #4a5568;">No new commands...</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2.5rem] p-8 shadow-2xl" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.15);">
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6" style="color: #d2a63d;">Weekly Activity</h4>
                    <div id="weeklyChart"></div>
                </div>
            </div>

            {{-- Active Objectives / Tasks --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-[3rem] p-8 shadow-2xl" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.15);">
                    <h4 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-3" style="color: #eaeef2;">
                        <i class="fas fa-crosshairs" style="color: #d2a63d;"></i> Active Objectives
                    </h4>
                    <div class="space-y-4">
                        @forelse($tasks as $task)
                        <div class="rounded-2xl p-5 transition-all" style="background-color: #010717; border: 1px solid rgba(210,166,61,0.12);"
                             onmouseover="this.style.borderColor='rgba(210,166,61,0.35)'"
                             onmouseout="this.style.borderColor='rgba(210,166,61,0.12)'">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                <div class="flex-1 w-full">
                                    <span class="text-[7px] font-black px-2 py-0.5 rounded tracking-widest uppercase" style="background: rgba(210,166,61,0.1); color: #d2a63d; border: 1px solid rgba(210,166,61,0.2);">{{ str_replace('_', ' ', $task->status) }}</span>
                                    <h5 class="text-sm font-bold mt-2" style="color: #eaeef2;">{{ $task->title }}</h5>

                                    {{-- Micro details info row --}}
                                    <div class="mt-1 flex items-center gap-3 text-[9px] font-mono uppercase" style="color: #8a9bb0;">
                                        <span>Days Active: <b class="font-black" style="color: #eaeef2;">{{ $task->days_worked ?? 0 }}d</b></span>
                                        @if(($task->overdue_days ?? 0) > 0)
                                            <span class="font-black animate-pulse" style="color: #ef4444;">[⚠️ {{ $task->overdue_days }}d Overdue]</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background-color: rgba(210,166,61,0.1); border: 1px solid rgba(210,166,61,0.08);">
                                            <div class="h-full transition-all duration-1000 rounded-full" style="width: {{ $task->completion_percentage ?? 0 }}%; background: #d2a63d;"></div>
                                        </div>
                                        <span class="text-[8px] font-black tracking-widest" style="color: #8a9bb0;">{{ $task->completion_percentage ?? 0 }}%</span>
                                    </div>
                                </div>

                                <div class="flex gap-2 w-full md:w-auto mt-4 md:mt-0">
                                    {{-- ⭐ 🔒 PRO FEATURE: Task Forwarding Lock --}}
                                    @if(auth()->user()->company->hasFeature('task_forwarding'))
                                        <button onclick="openForwardModal({{ $task->id }})"
                                                class="px-4 py-2.5 rounded-xl text-[10px] font-black transition-all shadow-lg"
                                                style="background: rgba(210,166,61,0.1); color: #d2a63d; border: 1px solid rgba(210,166,61,0.25);"
                                                onmouseover="this.style.background='#d2a63d'; this.style.color='#010717'; this.style.borderColor='#d2a63d';"
                                                onmouseout="this.style.background='rgba(210,166,61,0.1)'; this.style.color='#d2a63d'; this.style.borderColor='rgba(210,166,61,0.25)';"
                                                title="Forward Task">
                                            <i class="fas fa-share-square"></i>
                                        </button>
                                    @else
                                        <button onclick="upgradePrompt('Pro', 'Task Forwarding')"
                                                class="px-4 py-2.5 rounded-xl text-[10px] font-black transition-all shadow-lg cursor-not-allowed group relative"
                                                style="background: rgba(138,155,176,0.05); color: #4a5568; border: 1px solid rgba(138,155,176,0.1);"
                                                title="Requires Pro Plan">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @endif

                                    <button onclick="openOperationalModal({{ $task->id }})"
                                            class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] transition-all shadow-lg"
                                            style="background: #d2a63d; color: #010717;"
                                            onmouseover="this.style.background='#e6b84a'"
                                            onmouseout="this.style.background='#d2a63d'">Update</button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center py-10 uppercase tracking-widest font-bold text-[10px]" style="color: #4a5568;">No active objectives</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<canvas id="screenshotCanvas" style="display:none;"></canvas>

{{-- ══════════════════════════════════════════════════════
     MODALS SECTION
══════════════════════════════════════════════════════ --}}

{{-- Update Task Modal --}}
<div id="opModal" class="fixed inset-0 z-[10000] hidden backdrop-blur-md flex items-center justify-center p-4" style="background-color: rgba(1,7,23,0.95);">
    <div id="opModalContent" class="max-w-lg w-full rounded-[2.5rem] shadow-[0_0_50px_rgba(0,0,0,0.6)] overflow-hidden transform scale-95 opacity-0 transition-all duration-300" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.2);">
        <div class="p-6 flex justify-between items-center" style="background: rgba(210,166,61,0.05); border-bottom: 1px solid rgba(210,166,61,0.15);">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em]" style="color: #d2a63d;">Operational Log Entry</h3>
            <button onclick="closeOperationalModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full transition-all shadow"
                    style="background: rgba(138,155,176,0.1); color: #8a9bb0; border: 1px solid rgba(138,155,176,0.15);"
                    onmouseover="this.style.background='#ef4444'; this.style.color='#fff';"
                    onmouseout="this.style.background='rgba(138,155,176,0.1)'; this.style.color='#8a9bb0';">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form class="p-8 space-y-5">
            @csrf
            <input type="hidden" name="task_id" id="modal_task_id">

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[8px] font-black uppercase tracking-widest pl-1" style="color: #8a9bb0;">Progress (%)</label>
                    <input type="number" name="completion_percentage" min="0" max="100" required
                           class="w-full rounded-xl px-4 py-3.5 text-xs font-black outline-none transition-all shadow-inner"
                           style="background-color: #010717; border: 1px solid rgba(210,166,61,0.2); color: #eaeef2;"
                           onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.2)'"
                           placeholder="Progress %">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[8px] font-black uppercase tracking-widest pl-1" style="color: #8a9bb0;">Completion Stage</label>
                    <div class="relative">
                        <select name="completion_stage" required
                                class="w-full rounded-xl px-4 py-3.5 text-xs font-black outline-none transition-all appearance-none cursor-pointer shadow-inner"
                                style="background-color: #010717; border: 1px solid rgba(210,166,61,0.2); color: #eaeef2;"
                                onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.2)'">
                            <option value="progress" style="background: #010717; color: #d2a63d;">In Progress</option>
                            <option value="halfway" style="background: #010717; color: #d2a63d;">Half Completion (50%)</option>
                            <option value="completed" style="background: #010717; color: #1dd182;">Fully Completed</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] pointer-events-none" style="color: #4a5568;"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[8px] font-black uppercase tracking-widest pl-1" style="color: #8a9bb0;">System Blockers / Issues</label>
                <input type="text" name="blockers" placeholder="Any blockers? or type 'None'"
                       class="w-full rounded-xl px-4 py-3.5 text-xs font-black outline-none transition-all shadow-inner"
                       style="background-color: #010717; border: 1px solid rgba(210,166,61,0.2); color: #eaeef2;"
                       onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.2)'">
            </div>

            <div class="space-y-1.5">
                <label class="text-[8px] font-black uppercase tracking-widest pl-1" style="color: #8a9bb0;">Work Description Logs</label>
                <textarea name="update_note" rows="4" required
                          class="w-full rounded-xl px-4 py-3.5 text-xs font-bold resize-none outline-none transition-all shadow-inner"
                          style="background-color: #010717; border: 1px solid rgba(210,166,61,0.2); color: #eaeef2;"
                          onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.2)'"
                          placeholder="Update details..."></textarea>
            </div>

            <button type="submit"
                    class="w-full py-4 rounded-xl font-black uppercase tracking-[0.2em] text-[10px] active:scale-95 transition-all mt-2"
                    style="background: #d2a63d; color: #010717; box-shadow: 0 0 25px rgba(210,166,61,0.3); border: 1px solid rgba(210,166,61,0.5);"
                    onmouseover="this.style.background='#e6b84a'"
                    onmouseout="this.style.background='#d2a63d'">Transmit Report</button>
        </form>
    </div>
</div>

{{-- Forward Task Modal (Only functional if feature is unlocked, but good to keep structure) --}}
<div id="forwardModal" class="fixed inset-0 z-[10000] hidden backdrop-blur-md flex items-center justify-center p-4" style="background-color: rgba(1,7,23,0.95);">
    <div id="forwardModalContent" class="max-w-lg w-full rounded-[2.5rem] shadow-[0_0_50px_rgba(0,0,0,0.6)] overflow-hidden transform scale-95 opacity-0 transition-all duration-300" style="background-color: #05101f; border: 1px solid rgba(210,166,61,0.2);">
        <div class="p-6 flex justify-between items-center" style="background: rgba(210,166,61,0.05); border-bottom: 1px solid rgba(210,166,61,0.15);">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em]" style="color: #d2a63d;"><i class="fas fa-share mr-2"></i> Forward Objective</h3>
            <button onclick="closeForwardModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full transition-all shadow"
                    style="background: rgba(138,155,176,0.1); color: #8a9bb0; border: 1px solid rgba(138,155,176,0.15);"
                    onmouseover="this.style.background='#ef4444'; this.style.color='#fff';"
                    onmouseout="this.style.background='rgba(138,155,176,0.1)'; this.style.color='#8a9bb0';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="forward-form" method="POST" class="p-8 space-y-5">
            @csrf
            <input type="hidden" name="task_id" id="forward_task_id">
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest mb-2 pl-1" style="color: #8a9bb0;">Select Team Member</label>
                <div class="relative">
                    <select name="forwarded_to" required
                            class="w-full rounded-xl px-4 py-3.5 text-xs font-black outline-none transition-all appearance-none cursor-pointer shadow-inner"
                            style="background-color: #010717; border: 1px solid rgba(210,166,61,0.2); color: #eaeef2;"
                            onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.2)'">
                        <option value="" style="background: #010717;">-- Choose Colleague --</option>
                        @foreach($users ?? [] as $teamMember)
                            @if($teamMember->id !== auth()->id())
                                <option value="{{ $teamMember->id }}" style="background: #010717;">{{ $teamMember->name }} ({{ $teamMember->role ?? 'Employee' }})</option>
                            @endif
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] pointer-events-none" style="color: #4a5568;"></i>
                </div>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest mb-2 pl-1" style="color: #8a9bb0;">Forwarding Intel Reasons</label>
                <textarea name="forward_reason" rows="3" required
                          class="w-full rounded-xl px-4 py-3.5 text-xs font-bold resize-none outline-none transition-all shadow-inner"
                          style="background-color: #010717; border: 1px solid rgba(210,166,61,0.2); color: #eaeef2;"
                          onfocus="this.style.borderColor='rgba(210,166,61,0.6)'" onblur="this.style.borderColor='rgba(210,166,61,0.2)'"
                          placeholder="Why are you forwarding this task?"></textarea>
            </div>
            <button type="submit"
                    class="w-full py-4 rounded-xl font-black uppercase tracking-[0.2em] text-[10px] active:scale-95 transition-all mt-2"
                    style="background: #d2a63d; color: #010717; box-shadow: 0 0 25px rgba(210,166,61,0.3); border: 1px solid rgba(210,166,61,0.5);"
                    onmouseover="this.style.background='#e6b84a'"
                    onmouseout="this.style.background='#d2a63d'">Forward Task</button>
        </form>
    </div>
</div>

{{-- GLOBAL SCRIPTS LAYERS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>

@if(session('success'))
document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
        toast: true, position: 'top-end',
        showConfirmButton: false, timer: 4000, timerProgressBar: true,
        icon: 'success', title: "{{ session('success') }}",
        background: '#05101f', color: '#eaeef2', iconColor: '#1dd182'
    });
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 }, colors: ['#d2a63d', '#1dd182', '#eaeef2'] });
});
@endif

@if(session('error'))
document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
        toast: true, position: 'top-end',
        showConfirmButton: false, timer: 4000, timerProgressBar: true,
        icon: 'error', title: "{{ session('error') }}",
        background: '#05101f', color: '#eaeef2'
    });
});
@endif

// Live clock
function updateLiveClock() {
    const clockEl = document.getElementById('live-clock');
    if (clockEl) {
        const now = new Date();
        clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}
setInterval(updateLiveClock, 1000);
updateLiveClock();

// GLOBALS
let videoTrack        = null;
let workSeconds       = {{ $totalSeconds ?? 0 }};
let isTimerRunning    = false;
let isInactiveOverlay = false;
let lastActivityTime  = Date.now();

let timerInterval   = null;
let triggerInterval = null;
let aiInterval      = null;

const captureTargets = [30, 60, 120, 180, 300, 420, 540, 660, 780, 900, 1080, 1260];
let currentCaptureIndex = 0;
let cycleStartTime = 0;
const INACTIVITY_LIMIT = 20 * 60 * 1000;

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function getSurfaceLabel() {
    if (videoTrack && videoTrack.label && videoTrack.label.trim().length > 0) { return videoTrack.label; }
    return document.title;
}

function resetActivityTimer() {
    lastActivityTime = Date.now();
    if (isInactiveOverlay) resumeFromInactivity();
}

['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click', 'focus'].forEach(event => {
    document.addEventListener(event, resetActivityTimer, { passive: true });
});

document.addEventListener('visibilitychange', () => { if (!document.hidden) resetActivityTimer(); });

function checkInactivity() {
    if (!isTimerRunning || isInactiveOverlay) return;
    const idleMs = Date.now() - lastActivityTime;
    if (idleMs >= INACTIVITY_LIMIT) triggerInactivityAlert();
}

function triggerInactivityAlert() {
    isInactiveOverlay = true;
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
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
    if (!timerInterval && isTimerRunning) startTimerInterval();
}

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
        if (secondsInCurrentCycle > 1260) { cycleStartTime = workSeconds; currentCaptureIndex = 0; }
    }, 1000);
}

function updateTimerDisplay() {
    const h = Math.floor(workSeconds / 3600).toString().padStart(2, '0');
    const m = Math.floor((workSeconds % 3600) / 60).toString().padStart(2, '0');
    const s = (workSeconds % 60).toString().padStart(2, '0');
    const timerEl = document.getElementById('session-timer');
    if (timerEl) timerEl.innerText = `${h}:${m}:${s}`;
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

async function autoCapture(isAdmin = false) {
    try {
        if (!videoTrack || videoTrack.readyState !== 'live') return;
        const canvas = document.getElementById('screenshotCanvas');
        const video = document.createElement('video');
        video.srcObject = new MediaStream([videoTrack]);
        await video.play();

        canvas.width  = 1280;
        canvas.height = 720;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, 1280, 720);

        const img = canvas.toDataURL('image/jpeg', 0.4);

        const response = await fetch("{{ route('employee.upload.screenshot') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ image: img, window_title: getSurfaceLabel(), admin_trigger: isAdmin })
        });
        const data = await response.json();
        video.pause();
        video.remove();
    } catch (error) { console.error('Screenshot Error:', error); }
}

async function initiateTracking() {
    try {
        const stream = await navigator.mediaDevices.getDisplayMedia({ video: { displaySurface: 'monitor' } });
        videoTrack = stream.getVideoTracks()[0];

        videoTrack.onended = () => {
            isTimerRunning = false;
            syncTimer();
            setTimeout(() => location.reload(), 500);
        };

        document.getElementById('lock-overlay').style.display = 'none';
        document.getElementById('dashboard-content').classList.remove('opacity-0', 'blur-xl', 'pointer-events-none', 'h-screen', 'overflow-hidden');

        startOperationalSystems();

        fetch("{{ route('employee.attendance.checkin') }}", { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    } catch (err) {
        Swal.fire({
            title: 'Monitoring Required',
            text: 'Screen monitoring is required to start session.',
            icon: 'error',
            background: '#05101f',
            color: '#eaeef2',
            confirmButtonColor: '#d2a63d'
        });
    }
}

function syncTimer() {
    fetch("{{ route('employee.sync.timer') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ seconds: workSeconds })
    });
}

async function stopSession() {
    document.getElementById('inactivity-overlay').classList.add('hidden');
    const { value: text } = await Swal.fire({
        title: 'CONCLUDE SHIFT',
        input: 'textarea',
        inputPlaceholder: 'Summarize your objectives achieved...',
        showCancelButton: true,
        background: '#05101f',
        color: '#eaeef2',
        confirmButtonColor: '#d2a63d',
        cancelButtonColor: '#4a5568'
    });

    if (text !== undefined && text !== null) {
        isTimerRunning = false;
        if (timerInterval) clearInterval(timerInterval);
        if (triggerInterval) clearInterval(triggerInterval);
        if (aiInterval) clearInterval(aiInterval);
        if (videoTrack) videoTrack.stop();

        await fetch("{{ route('employee.session.stop') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ summary: text, seconds: workSeconds })
        });
        window.location.href = '/login';
    }
}

function openOperationalModal(taskId) {
    document.getElementById('modal_task_id').value = taskId;
    const modal = document.getElementById('opModal');
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
    const modal = document.getElementById('forwardModal');
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

function renderWeeklyChart() {
    new ApexCharts(document.querySelector('#weeklyChart'), {
        series: [{ name: 'Hours', data: {!! json_encode(collect($weeklyWorkHours ?? [])->pluck('hours')) !!} }],
        chart: { type: 'bar', height: 160, toolbar: { show: false }, background: 'transparent' },
        xaxis: {
            categories: {!! json_encode(collect($weeklyWorkHours ?? [])->pluck('day')) !!},
            labels: { style: { colors: '#8a9bb0', fontSize: '9px', fontWeight: 'bold' } }
        },
        yaxis: { labels: { style: { colors: '#8a9bb0', fontSize: '9px' } } },
        colors: ['#d2a63d'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        grid: { borderColor: 'rgba(210,166,61,0.08)' },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', style: { background: '#05101f' } }
    }).render();
}

// ⭐ AJAX OPERATIONAL SUBMIT LISTENER INTEGRATION
document.addEventListener('DOMContentLoaded', () => {
    updateShiftDisplay();
    setInterval(updateShiftDisplay, 60000);
    renderWeeklyChart();

    const opForm = document.querySelector('#opModal form');
    if (opForm) {
        opForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const taskId = document.getElementById('modal_task_id').value;
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            Swal.fire({
                title: 'TRANSMITTING PROTOCOL...',
                background: '#05101f', color: '#eaeef2',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/employee/tasks/${taskId}/update-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(resData => {
                if(resData.success) {
                    closeOperationalModal();
                    Swal.fire({
                        title: 'SIGNAL TRANSMITTED',
                        text: 'Operational log update successfully registered on core radar.',
                        icon: 'success',
                        background: '#05101f',
                        color: '#eaeef2',
                        confirmButtonColor: '#d2a63d'
                    }).then(() => { location.reload(); });
                }
            })
            .catch(() => {
                Swal.fire({ title: 'Transmission Error', icon: 'error', background: '#05101f', color: '#eaeef2' });
            });
        });
    }
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #010717; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(210, 166, 61, 0.3); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(210, 166, 61, 0.5); }
</style>
@endsection