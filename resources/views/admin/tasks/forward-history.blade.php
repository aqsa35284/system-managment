@extends('layouts.admin')

@section('content')
 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="p-6 md:p-10 bg-[#010717] min-h-screen font-sans text-[#eaeef2]">
    <div class="max-w-[1600px] mx-auto space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-[#d2a63d]/20 gap-4">
            <div class="flex items-center gap-5">
                <a href="{{ route('admin.tasks.index') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Back to Board">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-white uppercase tracking-widest">Task <span class="text-[#d2a63d]">Forward History</span></h2>
                    <p class="text-[10px] font-bold text-[#eaeef2]/60 uppercase tracking-widest mt-1 italic">// Complete transfer chain of all objectives</p>
                </div>
            </div>
        </div>

        {{-- Intelligence Filters --}}
        <div class="bg-[#030712] p-6 rounded-3xl border border-[#d2a63d]/20 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#d2a63d]/5 rounded-full blur-3xl"></div>
            
            <form method="GET" class="relative z-10 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-[9px] font-black uppercase text-[#eaeef2]/50 tracking-widest mb-2 block pl-1">Filter by Operator</label>
                    <select name="user_id" class="w-full bg-[#010717] border border-[#d2a63d]/30 focus:border-[#d2a63d] rounded-xl px-4 py-3 text-xs font-bold text-[#eaeef2] outline-none transition-all cursor-pointer">
                        <option value="" class="bg-[#010717]">ALL OPERATORS</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }} class="bg-[#010717]">
                                {{ strtoupper($emp->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-[200px]">
                    <label class="text-[9px] font-black uppercase text-[#eaeef2]/50 tracking-widest mb-2 block pl-1">Filter by Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" style="color-scheme: dark;" class="w-full bg-[#010717] border border-[#d2a63d]/30 focus:border-[#d2a63d] rounded-xl px-4 py-3 text-xs font-bold text-[#eaeef2] outline-none transition-all">
                </div>
                
                <div class="flex gap-2 w-full md:w-auto mt-2 md:mt-0">
                    <button type="submit" class="flex-1 md:flex-none bg-[#d2a63d] hover:bg-[#b8860b] text-[#010717] px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(210,166,61,0.3)] transition-all">
                        <i class="fas fa-filter mr-2"></i> Scan
                    </button>
                    <a href="{{ route('admin.tasks.forward-history') }}" class="bg-[#010717] hover:bg-[#d2a63d]/10 border border-[#d2a63d]/30 text-[#eaeef2]/60 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Operations Matrix Table --}}
        <div class="bg-[#030712] rounded-3xl shadow-2xl border border-[#d2a63d]/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#d2a63d]/5 border-b border-[#d2a63d]/20 text-[8px] md:text-[9px] font-black text-[#eaeef2]/50 uppercase tracking-[0.2em]">
                        <tr>
                            <th class="px-6 py-4"># Sequence</th>
                            <th class="px-4 py-4 max-w-[200px]">Task Identity</th>
                            <th class="px-4 py-4">Transferred From</th>
                            <th class="px-4 py-4">Assigned To</th>
                            <th class="px-4 py-4">Time Signature</th>
                            <th class="px-6 py-4">Transfer Intel Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2a63d]/10">
                        @forelse($forwards as $fwd)
                        <tr class="hover:bg-[#d2a63d]/5 transition-colors group">
                            
                            <td class="px-6 py-4 text-xs font-black text-[#d2a63d] font-mono tracking-wider">
                                #FWD-{{ $fwd->id }}
                            </td>
                            
                            <td class="px-4 py-4 max-w-[200px]">
                                <h4 class="text-xs font-black text-white group-hover:text-[#d2a63d] transition-colors uppercase tracking-wide truncate">{{ $fwd->task->title ?? 'N/A' }}</h4>
                                <p class="text-[8px] font-bold font-mono text-[#eaeef2]/40 uppercase tracking-widest mt-1">Task Node: #{{ $fwd->task_id }}</p>
                            </td>

                            <td class="px-4 py-4">
                                <span class="px-3 py-1 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">
                                    <i class="fas fa-sign-out-alt mr-1"></i> {{ $fwd->fromUser->name ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">
                                    <i class="fas fa-sign-in-alt mr-1"></i> {{ $fwd->toUser->name ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="px-4 py-4 font-mono">
                                <div class="text-[10px] font-black text-[#eaeef2]">{{ $fwd->created_at->format('d M Y') }}</div>
                                <div class="text-[9px] font-bold text-[#d2a63d] tracking-widest mt-0.5">{{ $fwd->created_at->format('H:i:s') }}</div>
                            </td>

                            <td class="px-6 py-4 text-[10px] text-[#eaeef2]/50 font-medium italic max-w-[250px] leading-relaxed">
                                "{{ $fwd->note ?? 'No justification appended.' }}"
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[10px] font-black text-[#eaeef2]/30 uppercase tracking-widest">
                                <i class="fas fa-ghost text-2xl mb-3 block opacity-20"></i>
                                No forward transmission records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($forwards->hasPages())
            <div class="p-6 border-t border-[#d2a63d]/20 bg-[#d2a63d]/5">
                {{ $forwards->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    nav[role="navigation"] p { color: #eaeef2; opacity: 0.6; font-size: 10px; font-weight: 900; text-transform: uppercase; }
    nav[role="navigation"] span, nav[role="navigation"] a { font-size: 10px; font-weight: 900; color: #d2a63d; }
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.7; cursor: pointer; }
    select option { background-color: #010717 !important; color: #eaeef2 !important; }
</style>
@endsection