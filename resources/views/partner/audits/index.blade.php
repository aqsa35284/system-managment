@extends('layouts.superadmin')

@section('title', 'Workspace Activity Monitor')

@section('content')
<div class="space-y-6 w-full">
    
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Workspace <span class="font-bold text-[#d2a63d]">Activity</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Monitor recent updates and actions performed by your tenant admins and staff.</p>
        </div>
        
        {{-- Tenant Filter --}}
        <form method="GET" action="{{ route('partner.audits.index') }}" class="flex gap-3 items-end">
            <div>
                <select name="company_id" class="bg-[#010717] border border-[#d2a63d]/30 text-white p-2.5 rounded-xl text-xs focus:ring-1 focus:ring-[#d2a63d] outline-none w-56">
                    <option value="">All My Workspaces...</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-[#d2a63d] hover:bg-[#b58c2e] text-[#010717] px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                Filter
            </button>
            @if(request()->has('company_id'))
                <a href="{{ route('partner.audits.index') }}" class="text-red-400 text-[10px] font-bold uppercase tracking-widest flex items-center hover:text-red-300 ml-2">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-[#d2a63d]/20 bg-[#d2a63d]/5 flex justify-between items-center">
            <h3 class="text-sm font-black uppercase text-[#d2a63d] tracking-widest"><i class="fa-solid fa-desktop mr-2"></i>Staff Operations Log</h3>
        </div>

        <div class="divide-y divide-[#d2a63d]/10 max-h-[700px] overflow-y-auto custom-scrollbar">
            @forelse($logs as $log)
                <div class="p-6 hover:bg-[#d2a63d]/5 transition-colors">
                    <div class="flex justify-between items-start gap-4">
                        <div class="w-full">
                            <div class="flex items-center gap-3 mb-3">
                                {{-- AZ Monogram Style for User --}}
                                <div class="w-6 h-6 rounded bg-[#d2a63d]/10 border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] font-black text-[10px] uppercase shadow-inner">
                                    {{ substr($log->user->name ?? 'U', 0, 2) }}
                                </div>

                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-black tracking-widest border 
                                    {{ $log->user->role === 'admin' ? 'bg-orange-500/10 text-orange-400 border-orange-500/30' : 'bg-slate-500/10 text-slate-400 border-slate-500/30' }}">
                                    {{ $log->user->role }}
                                </span>
                            </div>
                            
                            <p class="text-[14px] text-[#eaeef2] font-medium">{{ $log->description }}</p>
                            
                            {{-- Diff Checker --}}
                            @if($log->action === 'updated' && !empty($log->new_values))
                                <div class="mt-4 p-4 bg-[#0a1225] border border-[#d2a63d]/10 rounded-xl text-xs space-y-2 text-left">
                                    @foreach($log->new_values as $key => $val)
                                        <div class="grid grid-cols-12 gap-2 items-center">
                                            <div class="col-span-3 text-[#eaeef2]/40 uppercase text-[9px] font-black tracking-widest truncate">{{ str_replace('_', ' ', $key) }}</div>
                                            <div class="col-span-4 text-red-400 line-through truncate px-2 py-1 bg-red-500/5 rounded border border-red-500/10 font-mono text-[11px] text-center">
                                                {{ is_array($log->old_values[$key] ?? '') ? json_encode($log->old_values[$key]) : ($log->old_values[$key] ?? 'N/A') }}
                                            </div>
                                            <div class="col-span-1 flex justify-center text-[#d2a63d]"><i class="fa-solid fa-arrow-right-long"></i></div>
                                            <div class="col-span-4 text-green-400 font-bold truncate px-2 py-1 bg-green-500/5 rounded border border-green-500/10 font-mono text-[11px] text-center">
                                                {{ is_array($val) ? json_encode($val) : $val }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-right min-w-[90px]">
                            <span class="text-[10px] text-[#eaeef2]/40 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-white/30 italic text-sm">
                    No staff operations recorded yet.
                </div>
            @endforelse
        </div>
        <div class="p-4 border-t border-[#d2a63d]/10 bg-[#060b17]">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection