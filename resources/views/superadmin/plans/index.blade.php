@extends('layouts.superadmin')
@section('title', 'SaaS Pricing Engine | Master Panel')

@section('content')

{{-- 💡 SMART ROUTING PREFIX --}}
@php
    $routePrefix = auth()->check() && auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';
@endphp

<div class="w-full">
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">SaaS <span class="font-bold text-[#d2a63d]">Pricing Engine</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Manage platform tiers, rates, and feature distribution.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl font-bold">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-2xl p-6 relative shadow-lg">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-black text-[#eaeef2] uppercase tracking-widest">{{ $plan->name }}</h3>
                    <span class="text-[#d2a63d] text-xs font-bold uppercase bg-[#d2a63d]/10 px-2 py-1 rounded">5 Users Min</span>
                </div>
                
                <div class="mb-6">
                    <p class="text-[10px] text-[#eaeef2]/50 uppercase tracking-widest font-bold mb-1">Base Rate / Mo</p>
                    <p class="text-3xl font-black text-[#d2a63d]">PKR {{ number_format($plan->price) }}</p>
                    <p class="text-xs text-[#eaeef2]/50 mt-2 border-t border-[#d2a63d]/10 pt-2">+ PKR {{ number_format($plan->extra_user_rate) }} / extra user</p>
                </div>

                <div class="mb-6">
                    <p class="text-xs text-[#eaeef2]/70"><i class="fa-solid fa-database text-[#d2a63d] mr-2"></i> Retention: {{ $plan->retention_days }} Days</p>
                    <p class="text-xs text-[#eaeef2]/70 mt-2"><i class="fa-solid fa-key text-[#d2a63d] mr-2"></i> Active Features: {{ count($plan->features ?? []) }}</p>
                </div>

                {{-- FIXED DYNAMIC ROUTE --}}
                @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('superadmin.plans.edit', $plan->id) }}" class="block w-full text-center bg-[#d2a63d]/10 hover:bg-[#d2a63d] text-[#d2a63d] hover:text-[#010717] border border-[#d2a63d]/30 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-widest transition-all">
                    Configure Matrix <i class="fa-solid fa-gear ml-1"></i>
                </a>
                @else
                <p class="text-center text-[10px] text-[#eaeef2]/40 uppercase tracking-widest font-bold py-2">View Only — deploy tenants with this tier</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection