@extends('layouts.superadmin')

@section('title', 'Shadow Audit Logs | Master Control')

@section('content')
<div class="space-y-6 w-full">
    
    {{-- Header & Advanced Filter Section --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Shadow <span class="font-bold text-[#d2a63d]">Audit Logs</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Real-time intelligence feed of all administrative operations.</p>
        </div>
        
        {{-- Dual Filter Bar (Tenant & Role) --}}
        <form method="GET" action="{{ route('superadmin.audits.index') }}" class="flex gap-3 items-end">
            <div>
                <select name="company_id" class="bg-[#010717] border border-[#d2a63d]/30 text-white p-2.5 rounded-xl text-xs focus:ring-1 focus:ring-[#d2a63d] outline-none w-48">
                    <option value="">Global Tenants...</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="role" class="bg-[#010717] border border-[#d2a63d]/30 text-white p-2.5 rounded-xl text-xs focus:ring-1 focus:ring-[#d2a63d] outline-none w-36">
                    <option value="">All Roles</option>
                    <option value="partner" {{ request('role') == 'partner' ? 'selected' : '' }}>Partners</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                    <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employees</option>
                </select>
            </div>
            <button type="submit" class="bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(210,166,61,0.2)]">
                Scan Matrix
            </button>
            @if(request()->has('company_id') || request()->has('role'))
                <a href="{{ route('superadmin.audits.index') }}" class="text-red-400 text-[10px] font-bold uppercase tracking-widest flex items-center hover:text-red-300 ml-2">Clear</a>
            @endif
        </form>
    </div>

    {{-- Performance & Operations Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg relative overflow-hidden backdrop-blur-xl">
            <div class="absolute top-0 right-0 w-24 h-24 bg-[#d2a63d]/5 blur-xl rounded-full"></div>
            <h3 class="text-[#eaeef2]/60 text-xs uppercase tracking-widest font-bold">Total Operations Tracked</h3>
            <p class="text-4xl font-black mt-2 text-[#d2a63d] font-mono">{{ $stats['total_actions'] ?? 0 }}</p>
        </div>
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg relative overflow-hidden backdrop-blur-xl">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-500/5 blur-xl rounded-full"></div>
            <h3 class="text-[#eaeef2]/60 text-xs uppercase tracking-widest font-bold">Modifications & Purges</h3>
            <p class="text-4xl font-black mt-2 text-red-400 font-mono">{{ $stats['critical_updates'] ?? 0 }}</p>
        </div>
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg relative overflow-hidden backdrop-blur-xl">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 blur-xl rounded-full"></div>
            <h3 class="text-[#eaeef2]/60 text-xs uppercase tracking-widest font-bold">Top System Contributor</h3>
            <p class="text-2xl font-bold mt-3 text-green-400 truncate">{{ $stats['top_contributor'] ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Live Stream Terminal --}}
        <div class="lg:col-span-2">
            <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-2xl">
                <div class="px-6 py-4 border-b border-[#d2a63d]/20 bg-[#d2a63d]/5 flex justify-between items-center">
                    <h3 class="text-sm font-black uppercase text-[#d2a63d] tracking-widest"><i class="fa-solid fa-terminal mr-2"></i>Live Intel Stream</h3>
                </div>

                <div class="divide-y divide-[#d2a63d]/10 max-h-[600px] overflow-y-auto custom-scrollbar">
                    @forelse($logs as $log)
                        <div class="p-6 hover:bg-[#d2a63d]/5 transition-colors">
                            <div class="flex justify-between items-start gap-4">
                                <div class="w-full">
                                    <div class="flex items-center gap-2 mb-3">
                                        {{-- Action Badge --}}
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase font-black tracking-widest border 
                                            {{ $log->action === 'created' || $log->action === 'login' ? 'bg-green-500/10 text-green-400 border-green-500/30' : '' }}
                                            {{ $log->action === 'updated' ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : '' }}
                                            {{ $log->action === 'deleted' || $log->action === 'logout' ? 'bg-red-500/10 text-red-400 border-red-500/30' : '' }}
                                        ">
                                            {{ $log->action }}
                                        </span>
                                        
                                        {{-- 🚀 VISUAL ROLE BADGE --}}
                                        @if($log->user)
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-black tracking-widest border 
                                                {{ strtolower($log->user->role) === 'partner' ? 'bg-purple-500/10 text-purple-400 border-purple-500/30' : '' }}
                                                {{ strtolower($log->user->role) === 'admin' ? 'bg-orange-500/10 text-orange-400 border-orange-500/30' : '' }}
                                                {{ strtolower($log->user->role) === 'employee' ? 'bg-slate-500/10 text-slate-400 border-slate-500/30' : '' }}
                                                {{ strtolower($log->user->role) === 'super_admin' ? 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/30' : '' }}
                                            ">
                                                <i class="fa-solid fa-user-shield mr-1"></i> {{ $log->user->role }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Readable Description --}}
                                    <p class="text-[14px] text-[#eaeef2] font-medium">{{ $log->description }}</p>
                                    
                                    {{-- Clean Data Diff Checker --}}
                                    @if($log->action === 'updated' && !empty($log->new_values))
                                        <div class="mt-4 p-4 bg-[#0a1225] border border-[#d2a63d]/10 rounded-xl text-xs space-y-2 text-left">
                                            @foreach($log->new_values as $key => $val)
                                                <div class="grid grid-cols-12 gap-2 items-center">
                                                    <div class="col-span-3 text-[#eaeef2]/40 uppercase text-[9px] font-black tracking-widest truncate">{{ str_replace('_', ' ', $key) }}</div>
                                                    <div class="col-span-4 text-red-400 line-through truncate px-2 py-1 bg-red-500/5 rounded border border-red-500/10 font-mono text-[11px] text-center">
                                                        {{ is_array($log->old_values[$key] ?? '') ? json_encode($log->old_values[$key]) : ($log->old_values[$key] ?? 'null') }}
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
                                
                                {{-- Timestamp --}}
                                <div class="text-right min-w-[90px]">
                                    <span class="text-[10px] text-[#eaeef2]/40 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-white/30 italic text-sm">
                            <i class="fa-solid fa-satellite-dish text-3xl mb-4 block opacity-20 text-[#d2a63d]"></i>
                            No operations recorded in the active network frame.
                        </div>
                    @endforelse
                </div>
                
                <div class="p-4 border-t border-[#d2a63d]/10 bg-[#060b17]">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>

        {{-- Right Leaderboard Section --}}
        <div>
            <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-xs font-black uppercase text-[#d2a63d] tracking-widest mb-4 border-b border-[#d2a63d]/10 pb-3"><i class="fa-solid fa-chart-network mr-1"></i> Deployment Hierarchy</h3>
                <p class="text-[10px] text-[#eaeef2]/50 mb-6 uppercase tracking-widest font-bold">Total tenants registered by network owners:</p>
                
                <div class="space-y-3">
                    @forelse($userPerformance ?? [] as $user)
                        @if($user->companies_count > 0)
                            <div class="p-3 bg-[#0a1225] border border-[#d2a63d]/10 rounded-xl flex justify-between items-center hover:border-[#d2a63d]/30 transition-all">
                                <div class="flex items-center gap-3">
                                    {{-- AZ Monogram Style (No Images) --}}
                                    <div class="w-8 h-8 rounded bg-[#d2a63d]/10 border border-[#d2a63d]/20 flex items-center justify-center text-[#d2a63d] font-black text-xs uppercase shadow-inner">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-[#eaeef2] leading-tight">{{ $user->name }}</h4>
                                        <span class="text-[8px] text-[#d2a63d] uppercase tracking-widest font-black">{{ $user->role }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-base font-black text-[#eaeef2] font-mono">{{ $user->companies_count }}</span>
                                    <span class="text-[8px] text-white/30 block uppercase tracking-widest">Tenants</span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-xs text-[#eaeef2]/40 italic py-4">No deployment data available.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection