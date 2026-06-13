@extends('layouts.superadmin')

@section('title', 'Shadow Audit Logs | Master Panel')

@section('content')
<div class="space-y-6">
    {{-- Header & Filter Section --}}
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-white">Shadow Audit Logs</h2>
            <p class="text-[#d2a63d]/70 text-sm">Real-time intelligence feed of all administrative operations.</p>
        </div>
        
        {{-- Filter Bar (Optimized for 100+ Companies) --}}
        <form method="GET" action="{{ route('superadmin.audits') }}" class="flex gap-3">
            <select name="company_id" class="bg-[#010717] border border-[#d2a63d]/30 text-white p-2.5 rounded-xl text-xs focus:ring-1 focus:ring-[#d2a63d] outline-none max-h-48 overflow-y-auto">
                <option value="">Filter by Tenant...</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} (ID: {{ $c->id }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-[0_0_15px_rgba(210,166,61,0.2)]">
                Scan Logs
            </button>
            @if(request()->has('company_id'))
                <a href="{{ route('superadmin.audits') }}" class="text-red-400 text-xs flex items-center hover:text-red-300 ml-2">Clear</a>
            @endif
        </form>
    </div>

    {{-- Audit Logs Table (ADVANCED VIEW) --}}
    <div class="bg-white/[0.02] border border-[#d2a63d]/10 rounded-2xl overflow-hidden backdrop-blur-xl shadow-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#d2a63d]/5 text-[#d2a63d] text-[10px] uppercase tracking-wider">
                    <th class="px-6 py-4 font-bold w-48">Actor & Tenant</th>
                    <th class="px-6 py-4 font-bold w-64">Operation Triggered</th>
                    <th class="px-6 py-4 font-bold">Intelligence Details (What Changed?)</th>
                    <th class="px-6 py-4 font-bold text-right w-32">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d2a63d]/10">
                @forelse($logs as $log)
                <tr class="hover:bg-[#d2a63d]/5 transition-colors group">
                    
                    {{-- Column 1: Actor & Company --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-[#eaeef2] text-xs font-bold flex items-center gap-2">
                                <i class="fa-solid fa-user-secret text-[#d2a63d]"></i>
                                {{ $log->admin->name ?? 'System' }}
                            </span>
                            <span class="bg-[#d2a63d]/10 text-[#d2a63d] px-2 py-0.5 rounded text-[9px] uppercase tracking-widest font-black inline-block w-max">
                                {{ $log->company->name ?? 'Global Environment' }}
                            </span>
                        </div>
                    </td>

                    {{-- Column 2: Action & URL Path --}}
                    <td class="px-6 py-4">
                        @php
                            $action = $log->action;
                            $badgeClass = 'bg-gray-500/10 text-gray-400 border-gray-500/20';
                            if(str_contains(strtolower($action), 'delete')) $badgeClass = 'bg-red-500/10 text-red-400 border-red-500/20';
                            if(str_contains(strtolower($action), 'creat')) $badgeClass = 'bg-green-500/10 text-green-400 border-green-500/20';
                            if(str_contains(strtolower($action), 'updat')) $badgeClass = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                        @endphp
                        <span class="px-2 py-1 rounded border text-[9px] font-black uppercase {{ $badgeClass }}">
                            {{ $action }}
                        </span>
                        <span class="text-[#eaeef2]/50 text-[10px] mt-2 block font-mono truncate max-w-[200px]" title="{{ $log->url }}">
                            <i class="fa-solid fa-link text-[#d2a63d]/50 mr-1"></i> {{ str_replace(url('/'), '', $log->url) }}
                        </span>
                    </td>

                    {{-- Column 3: THE DETAILS (Advanced Intel Block) --}}
                    <td class="px-6 py-4">
                        <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-lg p-3 text-[11px] font-mono text-[#eaeef2]/80 h-16 overflow-hidden hover:h-auto hover:min-h-16 transition-all duration-300 relative group-hover:border-[#d2a63d]/50">
                            @if($log->details)
                                @php 
                                    // Parse JSON once for performance
                                    $parsedDetails = json_decode($log->details, true); 
                                @endphp
                                
                                @if(is_array($parsedDetails))
                                    <ul class="list-none space-y-1">
                                        @foreach($parsedDetails as $key => $value)
                                            <li>
                                                <span class="text-[#d2a63d]">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> 
                                                <span class="text-white">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $log->details }}
                                @endif
                            @else
                                <span class="text-gray-600 italic">No specific payload detected.</span>
                            @endif
                            <div class="absolute bottom-0 left-0 w-full h-4 bg-gradient-to-t from-[#010717] to-transparent group-hover:hidden"></div>
                        </div>
                    </td>

                    {{-- Column 4: Timestamp --}}
                    <td class="px-6 py-4 text-[#eaeef2]/50 text-xs text-right">
                        <span class="block text-[#eaeef2]">{{ $log->created_at->format('M d, Y') }}</span>
                        <span class="text-[10px]">{{ $log->created_at->format('H:i:s A') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-[#eaeef2]/40 italic">
                        <i class="fa-solid fa-satellite-dish text-2xl mb-3 block opacity-20"></i>
                        No audit records found in the shadow stream.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-[#d2a63d]/10 bg-[#d2a63d]/5">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection