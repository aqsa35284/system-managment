{{-- resources/views/admin/tasks/forward-history.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">

        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter">Task <span class="text-indigo-600">Forward History</span></h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Complete transfer chain of all objectives</p>
            </div>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-8 flex flex-wrap gap-3 bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm">
            <select name="user_id" class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-slate-700 outline-none">
                <option value="">All Operators</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-slate-700 outline-none">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-xs font-black uppercase">Filter</button>
            <a href="{{ route('admin.tasks.forward-history') }}" class="bg-slate-100 text-slate-500 px-6 py-2 rounded-xl text-xs font-black uppercase">Reset</a>
        </form>

        {{-- TABLE --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left p-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">#</th>
                        <th class="text-left p-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Task</th>
                        <th class="text-left p-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">From Operator</th>
                        <th class="text-left p-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">To Operator</th>
                        <th class="text-left p-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Time</th>
                        <th class="text-left p-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forwards as $fwd)
                    <tr class="border-b border-slate-50 hover:bg-indigo-50/30 transition-all">
                        <td class="p-5 text-xs font-black text-slate-300">{{ $fwd->id }}</td>
                        <td class="p-5">
                            <p class="text-xs font-black text-slate-800 uppercase italic">{{ $fwd->task->title ?? 'N/A' }}</p>
                            <span class="text-[8px] font-bold text-indigo-400 uppercase">Task #{{ $fwd->task_id }}</span>
                        </td>
                        <td class="p-5">
                            <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-[9px] font-black uppercase">
                                {{ $fwd->fromUser->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="p-5">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase">
                                {{ $fwd->toUser->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="p-5 text-[9px] font-bold text-slate-400">
                            {{ $fwd->created_at->format('d M Y') }}<br>
                            <span class="text-indigo-400">{{ $fwd->created_at->format('H:i') }}</span>
                        </td>
                        <td class="p-5 text-[10px] text-slate-500 italic">{{ $fwd->note ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-slate-300 text-xs font-black uppercase italic">
                            No transfer records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION --}}
            @if($forwards->hasPages())
            <div class="p-5 border-t border-slate-50">
                {{ $forwards->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection