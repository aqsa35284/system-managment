@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen">
    
    {{-- 🛰️ 1. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
        <div>
            <h2 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">
                Fleet <span class="text-indigo-600">Attendance</span>
            </h2>
            <p class="text-slate-400 text-[10px] font-black uppercase italic tracking-[0.4em] mt-3 leading-none">
                Operational Node Activity & Session Logs
            </p>
        </div>

        <div class="flex gap-4">
            <div class="bg-indigo-600 px-8 py-4 rounded-[2rem] shadow-xl shadow-indigo-100 flex flex-col items-center min-w-[140px]">
                <p class="text-[8px] font-black text-white/60 uppercase italic tracking-widest mb-1">Active Nodes Now</p>
                <p class="text-2xl font-black text-white leading-none">
                    {{ $stats['active_now'] ?? 0 }}
                </p>
            </div>
            <div class="bg-slate-900 px-8 py-4 rounded-[2rem] shadow-2xl flex flex-col items-center min-w-[140px]">
                <p class="text-[8px] font-black text-slate-500 uppercase italic tracking-widest mb-1">Current Cycle</p>
                <p class="text-sm font-bold text-emerald-400 leading-none">{{ now()->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- 🔍 2. ADVANCED FILTER BAR --}}
    <div class="bg-white rounded-[2.5rem] p-8 mb-8 shadow-sm border border-slate-100">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase ml-2 mb-2 block tracking-widest">Specific Day</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase ml-2 mb-2 block tracking-widest">Monthly Cycle</label>
                <input type="month" name="month" value="{{ request('month') }}" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase ml-2 mb-2 block tracking-widest">Operator Name</label>
                <select name="user_id" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Fleet Members</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase ml-2 mb-2 block tracking-widest">Operational Shift</label>
                <select name="shift" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Shifts</option>
                    <option value="Morning" {{ request('shift') == 'Morning' ? 'selected' : '' }}>Morning</option>
                    <option value="Evening" {{ request('shift') == 'Evening' ? 'selected' : '' }}>Evening</option>
                    <option value="Night" {{ request('shift') == 'Night' ? 'selected' : '' }}>Night</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl text-[10px] font-black uppercase italic hover:bg-slate-900 transition-all shadow-lg">
                    Execute Search
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="p-3 bg-slate-100 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- 📊 3. SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Aggregate Hours</p>
                <h4 class="text-2xl font-black text-indigo-600 italic">{{ number_format($stats['total_hours'], 2) }}H</h4>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-history"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Sessions</p>
                <h4 class="text-2xl font-black text-slate-800 italic">{{ $stats['total_sessions'] }}</h4>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-fingerprint"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Live Signal</p>
                <h4 class="text-2xl font-black text-emerald-600 italic">{{ $stats['active_now'] }} ACTIVE</h4>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-wifi animate-pulse"></i>
            </div>
        </div>
    </div>

    {{-- 📑 4. MAIN LOG TABLE --}}
    <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Node Operator</th>
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Assigned Shift</th>
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Entry Signal</th>
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Exit Signal</th>
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Total Active Time</th>
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest text-center">Integrity</th>
                        <th class="p-8 text-[10px] font-black text-slate-400 uppercase italic tracking-widest text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $log)
                    <tr class="hover:bg-slate-50/80 transition-all group {{ !$log->check_out ? 'bg-emerald-50/30' : '' }}">
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="{{ $log->user->profile_image ? asset('storage/'.$log->user->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($log->user->name) }}" class="w-14 h-14 rounded-2xl object-cover border-4 border-white shadow-lg">
                                    @if(!$log->check_out)
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-4 border-white animate-ping"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-800 uppercase italic tracking-tight">{{ $log->user->name }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $log->user->department->name ?? 'General Fleet' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-8">
                            @php
                                $sName = ucfirst(strtolower($log->shift ?? 'General'));
                                $shiftStyles = [
                                    'Morning' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'Evening' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'Night'   => 'bg-slate-900 text-slate-300 border-slate-700',
                                ];
                            @endphp
                            <span class="text-[9px] font-black px-5 py-2 rounded-xl border uppercase italic {{ $shiftStyles[$sName] ?? 'bg-slate-100' }}">
                                {{ $sName }}
                            </span>
                        </td>
                        <td class="p-8">
                            <p class="text-xs font-mono font-black text-slate-700">{{ $log->check_in ? $log->check_in->format('H:i:s') : '---' }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-widest">{{ $log->date->format('d.M.Y') }}</p>
                        </td>
                        <td class="p-8">
                            @if($log->check_out)
                                <p class="text-xs font-mono font-black text-slate-700">{{ $log->check_out->format('H:i:s') }}</p>
                            @else
                                <span class="text-[9px] font-black text-emerald-600 uppercase italic tracking-widest animate-pulse">BROADCASTING...</span>
                            @endif
                        </td>
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                @php
                                    $curH = $log->work_time / 3600;
                                    $perc = min(($curH / 9) * 100, 100);
                                @endphp
                                <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden min-w-[100px] shadow-inner">
                                    <div class="bg-indigo-600 h-full transition-all duration-1000" style="width: {{ $perc }}%"></div>
                                </div>
                                <span class="text-xs font-mono font-black text-slate-800">
                                    {{ number_format($curH, 2) }}<span class="text-[8px] opacity-40 ml-0.5">H</span>
                                </span>
                            </div>
                        </td>
                        <td class="p-8 text-center">
                            <span class="text-[8px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest border {{ $log->status === 'present' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="p-8 text-center">
                            <form action="{{ route('admin.attendance.destroy', $log->id) }}" method="POST" onsubmit="return confirm('🚨 WARN: PURGE THIS LOG ENTRY?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-32 text-center text-slate-300 font-black italic uppercase tracking-[0.5em] opacity-20">
                            No Operational Data Detected
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="p-10 border-t border-slate-50 bg-slate-50/30 flex justify-center">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Pagination Cyber Fix */
    .pagination { display: flex; list-style: none; gap: 8px; }
    .page-item .page-link { 
        background: #fff; border: 1px solid #e2e8f0; color: #64748b; 
        padding: 10px 18px; border-radius: 14px; font-size: 11px; font-weight: 900;
        text-transform: uppercase; transition: all 0.3s ease;
    }
    .page-item.active .page-link { background: #4f46e5; color: white; border-color: #4f46e5; box-shadow: 0 10px 15px rgba(79, 70, 229, 0.2); }
</style>
@endsection