@extends('layouts.superadmin')

@section('title', 'Tenant Details | Master Control')

@section('content')

{{-- 💡 SMART ROUTING: System khud decide karega route ka prefix --}}
@php
    $routePrefix = auth()->check() && auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';
@endphp

<div class="max-w-6xl mx-auto">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8 border-b border-[#d2a63d]/20 pb-6">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]"><span class="font-bold text-[#d2a63d]">Tenant</span> Overview</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Detailed operational and financial overview of <span class="text-white font-bold">{{ $company->name }}</span>.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route($routePrefix . 'company.edit', $company->id) }}" class="border border-[#d2a63d]/30 hover:bg-[#d2a63d]/10 text-[#d2a63d] px-5 py-2.5 rounded-xl transition-all flex items-center font-bold text-xs uppercase tracking-widest">
                <i class="fa-solid fa-pen mr-2"></i> Modify Config
            </a>
            <a href="{{ route($routePrefix . 'dashboard') }}" class="bg-white/5 hover:bg-white/10 border border-white/10 text-[#eaeef2] px-5 py-2.5 rounded-xl transition-colors flex items-center font-bold text-xs uppercase tracking-widest">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        
        {{-- Core Identity Panel --}}
        <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#d2a63d]/5 blur-2xl rounded-full pointer-events-none"></div>
            <h3 class="text-lg font-bold text-[#eaeef2] mb-6 flex items-center gap-2 border-b border-white/5 pb-3">
                <i class="fa-solid fa-fingerprint text-[#d2a63d]"></i> Core Identity
            </h3>
            
            <div class="space-y-5">
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Company Name</span>
                    <span class="text-[#eaeef2] text-sm font-bold flex items-center gap-3">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" class="w-6 h-6 rounded-full border border-[#d2a63d]/50 object-cover">
                        @endif
                        {{ $company->name }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Primary Email</span>
                    <span class="text-[#d2a63d] text-sm font-medium">{{ $company->email }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Phone Number</span>
                    <span class="text-[#eaeef2] text-sm font-medium">{{ $company->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">System Status</span>
                    <span class="px-3 py-1 rounded border text-[10px] font-black uppercase tracking-widest {{ $company->status == 'active' ? 'bg-green-500/10 text-green-400 border-green-500/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }}">
                        {{ $company->status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Subscription & Billing Panel --}}
        <div class="bg-[#0a1225] border border-[#d2a63d]/30 rounded-3xl p-8 shadow-[0_0_30px_rgba(210,166,61,0.1)] relative">
            <h3 class="text-lg font-bold text-[#d2a63d] mb-6 flex items-center gap-2 border-b border-[#d2a63d]/20 pb-3">
                <i class="fa-solid fa-file-invoice-dollar"></i> SaaS Operations & Billing
            </h3>
            
            <div class="space-y-5">
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Plan Tier</span>
                    <span class="text-[#010717] bg-[#d2a63d] px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest shadow-md">
                        {{ str_replace('_', ' ', $company->plan_type ?? 'Starter') }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Invoice Status</span>
                    <span class="px-3 py-1 rounded border text-[10px] font-black uppercase tracking-widest {{ $company->payment_status == 'paid' ? 'bg-green-500/10 text-green-400 border-green-500/30' : 'bg-orange-500/10 text-orange-400 border-orange-500/30' }}">
                        {{ $company->payment_status ?? 'Unpaid' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Subscription Ends</span>
                    <span class="text-[#eaeef2] text-sm font-mono {{ $company->subscription_ends_at && \Carbon\Carbon::parse($company->subscription_ends_at)->isPast() ? 'text-red-400 font-bold' : '' }}">
                        {{ $company->subscription_ends_at ? \Carbon\Carbon::parse($company->subscription_ends_at)->format('d M, Y') : 'Not set' }}
                    </span>
                </div>

                <div class="pt-3 border-t border-white/5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[#eaeef2]/50 text-[10px] uppercase font-bold tracking-widest">Fleet Quota Usage</span>
                        <span class="text-[#eaeef2] text-xs font-bold"><span class="text-[#d2a63d]">{{ $company->users->count() }}</span> / {{ $company->max_employees }}</span>
                    </div>
                    @php 
                        $percentage = $company->max_employees > 0 ? ($company->users->count() / $company->max_employees) * 100 : 0;
                        $barColor = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-[#d2a63d]');
                    @endphp
                    <div class="w-full bg-[#010717] rounded-full h-2 border border-[#d2a63d]/20">
                        <div class="{{ $barColor }} h-2 rounded-full transition-all duration-1000" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enabled Features Panel --}}
        <div class="lg:col-span-2 bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-lg font-bold text-[#eaeef2] mb-6 flex items-center gap-2 border-b border-white/5 pb-3">
                <i class="fa-solid fa-microchip text-[#d2a63d]"></i> Active Plan Features
            </h3>
            @php
                $featureLabels = config('features.all', []);
                $enabled = is_array($company->features) ? $company->features : [];
            @endphp
            @if(count($enabled) === 0)
                <p class="text-sm text-[#eaeef2]/50">Starter plan — core modules only (dashboard, employees, tasks, attendance).</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($enabled as $slug)
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-[#d2a63d]/10 text-[#d2a63d] border border-[#d2a63d]/30 px-3 py-1.5 rounded-lg">
                            {{ $featureLabels[$slug] ?? str_replace('_', ' ', $slug) }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Registered Accounts Table --}}
    <h3 class="text-xl font-bold text-[#d2a63d] mb-4 flex items-center gap-2"><i class="fa-solid fa-network-wired"></i> Registered Fleet Accounts</h3>
    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-2xl overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm text-[#eaeef2]">
            <thead class="bg-[#d2a63d]/5 text-[#d2a63d] text-[10px] uppercase tracking-widest border-b border-[#d2a63d]/20">
                <tr>
                    <th class="px-6 py-5 font-black">Identity</th>
                    <th class="px-6 py-5 font-black">Email Address</th>
                    <th class="px-6 py-5 font-black">Designation / Role</th>
                    <th class="px-6 py-5 font-black text-right">Account Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d2a63d]/10">
                @forelse($company->users as $user)
                <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                    <td class="px-6 py-4 font-bold flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-[#0a1225] border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] shadow-inner">
                            <i class="fa-solid {{ $user->role === 'admin' ? 'fa-user-tie' : 'fa-user' }} text-sm"></i>
                        </div>
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 text-[#eaeef2]/70 font-medium">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $user->role === 'admin' ? 'bg-purple-500/10 text-purple-400 border-purple-500/30' : 'bg-blue-500/10 text-blue-400 border-blue-500/30' }} px-3 py-1.5 rounded text-[9px] uppercase font-black tracking-widest border shadow-sm">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="{{ $user->status === 'active' ? 'text-green-400' : 'text-red-400' }} text-[10px] uppercase font-black tracking-widest">
                            <i class="fa-solid fa-circle text-[8px] mr-1"></i> {{ $user->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-[#eaeef2]/40 text-xs uppercase tracking-widest font-bold">No fleet members registered under this tenant yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection