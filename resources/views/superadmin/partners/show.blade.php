@extends('layouts.superadmin')

@section('title', 'Partner Intel | Master Control')

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8 border-b border-[#d2a63d]/20 pb-6">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]"><span class="font-bold text-[#d2a63d]">Partner</span> Intel</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Detailed overview and tenant network of <span class="text-white font-bold">{{ $partner->name }}</span>.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('superadmin.partners.edit', $partner->id) }}" class="border border-[#d2a63d]/30 hover:bg-[#d2a63d]/10 text-[#d2a63d] px-5 py-2.5 rounded-xl transition-all flex items-center font-bold text-xs uppercase tracking-widest">
                <i class="fa-solid fa-pen mr-2"></i> Modify Config
            </a>
            <a href="{{ route('superadmin.partners.index') }}" class="bg-white/5 hover:bg-white/10 border border-white/10 text-[#eaeef2] px-5 py-2.5 rounded-xl transition-colors flex items-center font-bold text-xs uppercase tracking-widest">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        
        {{-- Core Identity Panel --}}
        <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#d2a63d]/5 blur-2xl rounded-full pointer-events-none"></div>
            <h3 class="text-lg font-bold text-[#eaeef2] mb-6 flex items-center gap-2 border-b border-white/5 pb-3">
                <i class="fa-solid fa-fingerprint text-[#d2a63d]"></i> Partner Identity
            </h3>
            
            <div class="space-y-5">
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Full Name</span>
                    <span class="text-[#eaeef2] text-sm font-bold flex items-center gap-3">
                        <div class="w-6 h-6 rounded border border-[#d2a63d]/50 flex items-center justify-center text-[10px] text-[#d2a63d] font-black uppercase">
                            {{ substr($partner->name, 0, 2) }}
                        </div>
                        {{ $partner->name }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Primary Email</span>
                    <span class="text-[#d2a63d] text-sm font-medium">{{ $partner->email }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Phone Number</span>
                    <span class="text-[#eaeef2] text-sm font-medium">{{ $partner->phone ?? 'Not Provided' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Account Status</span>
                    <span class="px-3 py-1 rounded border text-[10px] font-black uppercase tracking-widest {{ $partner->status == 'active' ? 'bg-green-500/10 text-green-400 border-green-500/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }}">
                        {{ $partner->status }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Joined On</span>
                    <span class="text-[#eaeef2] text-sm font-mono">
                        {{ \Carbon\Carbon::parse($partner->created_at)->format('d M, Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Partner Operations Panel --}}
        <div class="bg-[#0a1225] border border-[#d2a63d]/30 rounded-3xl p-8 shadow-[0_0_30px_rgba(210,166,61,0.1)] relative">
            <h3 class="text-lg font-bold text-[#d2a63d] mb-6 flex items-center gap-2 border-b border-[#d2a63d]/20 pb-3">
                <i class="fa-solid fa-chart-network"></i> Operations Overview
            </h3>
            
            <div class="flex items-center justify-center flex-col h-40 bg-[#010717] border border-[#d2a63d]/10 rounded-2xl shadow-inner relative overflow-hidden">
                <div class="absolute -top-4 -right-4 w-20 h-20 bg-[#d2a63d]/10 blur-xl rounded-full"></div>
                <span class="text-[#d2a63d] text-5xl font-black mb-2">{{ $partner->companies->count() ?? 0 }}</span>
                <span class="text-[#eaeef2]/50 text-xs font-bold uppercase tracking-widest">Total Managed Tenants</span>
            </div>
        </div>
    </div>

    {{-- Managed Tenants Table --}}
    <h3 class="text-xl font-bold text-[#d2a63d] mb-4 flex items-center gap-2"><i class="fa-solid fa-building"></i> Network Tenants</h3>
    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-2xl overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm text-[#eaeef2]">
            <thead class="bg-[#d2a63d]/5 text-[#d2a63d] text-[10px] uppercase tracking-widest border-b border-[#d2a63d]/20">
                <tr>
                    <th class="px-6 py-5 font-black">Tenant Identity</th>
                    <th class="px-6 py-5 font-black">Plan Tier</th>
                    <th class="px-6 py-5 font-black">System Status</th>
                    <th class="px-6 py-5 font-black text-right">Registered On</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d2a63d]/10">
                @forelse($partner->companies ?? [] as $company)
                <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                    <td class="px-6 py-4 font-bold flex items-center gap-4">
                        <div class="w-8 h-8 rounded border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] shadow-inner text-xs bg-[#0a1225]">
                            {{ substr($company->name, 0, 1) }}
                        </div>
                        {{ $company->name }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-[#d2a63d]/20 text-[#d2a63d] px-2 py-0.5 rounded text-[9px] uppercase tracking-widest font-black">
                            {{ str_replace('_', ' ', $company->plan_type ?? 'Starter') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ $company->status === 'active' ? 'text-green-400' : 'text-red-400' }} text-[10px] uppercase font-black tracking-widest">
                            <i class="fa-solid fa-circle text-[8px] mr-1"></i> {{ $company->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-[#eaeef2]/70 text-xs font-mono">
                        {{ \Carbon\Carbon::parse($company->created_at)->format('d M, Y') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-[#eaeef2]/40 text-xs uppercase tracking-widest font-bold">No tenants found under this partner.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection