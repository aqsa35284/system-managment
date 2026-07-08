@extends('layouts.superadmin')
@section('title', 'Configure Plan Matrix | Master Panel')

@section('content')

{{-- 💡 SMART ROUTING PREFIX --}}
@php
    $routePrefix = auth()->check() && auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';
@endphp

<div class="w-full max-w-4xl mx-auto">
    
    {{-- Header --}}
    <div class="mb-10 flex justify-between items-center border-b border-[#d2a63d]/20 pb-6">
        <div>
            {{-- FIXED BACK BUTTON --}}
            <a href="{{ route($routePrefix . 'plans.index') }}" class="text-[#d2a63d] hover:text-white text-sm mb-4 inline-block transition-colors font-bold">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Matrix
            </a>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Configure <span class="font-bold text-[#d2a63d]">{{ $plan->name }}</span> Plan</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Adjust pricing, limits, and feature distribution for this tier.</p>
        </div>
        <div class="bg-[#d2a63d]/10 border border-[#d2a63d]/30 px-4 py-2 rounded-lg shadow-[0_0_15px_rgba(210,166,61,0.15)]">
            <span class="text-[#d2a63d] font-black uppercase tracking-widest text-[10px]">Base Quota: 5 Users</span>
        </div>
    </div>

    {{-- Configuration Form --}}
    {{-- FIXED FORM ACTION --}}
    <form action="{{ route($routePrefix . 'plans.update', $plan->id) }}" method="POST" class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        @csrf
        @method('PUT')
        
        {{-- Background Glow --}}
        <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-[#d2a63d]/5 blur-[100px] pointer-events-none z-0"></div>

        <div class="relative z-10">
            {{-- 1. Financials & Limits --}}
            <h3 class="text-lg font-bold text-[#eaeef2] mb-6 border-b border-white/5 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-coins text-[#d2a63d]"></i> Financials & Limits
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div>
                    <label class="block text-[10px] text-[#eaeef2]/50 uppercase tracking-widest font-bold mb-2">Base Monthly Price (PKR)</label>
                    <input type="number" name="price" value="{{ $plan->price }}" class="w-full bg-white/5 border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] font-mono focus:outline-none focus:border-[#d2a63d] focus:bg-[#d2a63d]/5 transition-all shadow-inner" required>
                </div>
                <div>
                    <label class="block text-[10px] text-[#eaeef2]/50 uppercase tracking-widest font-bold mb-2">Extra User Rate (PKR/mo)</label>
                    <input type="number" name="extra_user_rate" value="{{ $plan->extra_user_rate }}" class="w-full bg-white/5 border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] font-mono focus:outline-none focus:border-[#d2a63d] focus:bg-[#d2a63d]/5 transition-all shadow-inner" required>
                </div>
                <div>
                    <label class="block text-[10px] text-[#eaeef2]/50 uppercase tracking-widest font-bold mb-2">Data Retention (Days)</label>
                    <input type="number" name="retention_days" value="{{ $plan->retention_days }}" class="w-full bg-white/5 border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] font-mono focus:outline-none focus:border-[#d2a63d] focus:bg-[#d2a63d]/5 transition-all shadow-inner" required>
                </div>
            </div>

            {{-- 2. Feature Authorization Matrix (Checkboxes) --}}
            <h3 class="text-lg font-bold text-[#eaeef2] mb-6 border-b border-white/5 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-unlock-keyhole text-[#d2a63d]"></i> Feature Authorization Matrix
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
                @foreach($allFeatures as $key => $labelData)
                    @php
                        // 🟢 Safe extraction logic for array protection
                        $safeKey = is_string($key) ? $key : (is_array($labelData) ? ($labelData['slug'] ?? 'feature') : 'feature');
                        $safeLabel = is_array($labelData) ? ($labelData['name'] ?? $labelData['label'] ?? 'Premium Feature') : (is_string($labelData) ? $labelData : 'Premium Feature');
                    @endphp
                    <label class="flex items-center gap-4 p-4 rounded-xl border border-[#d2a63d]/10 bg-white/[0.02] hover:bg-[#d2a63d]/5 hover:border-[#d2a63d]/30 cursor-pointer transition-all group shadow-sm">
                        <div class="relative flex items-center">
                            {{-- Tailwnid Accent Color Checkbox --}}
                            <input type="checkbox" name="features[]" value="{{ $safeKey }}" class="w-5 h-5 accent-[#d2a63d] cursor-pointer rounded bg-[#010717] border-[#d2a63d]/50 focus:ring-[#d2a63d]" 
                            {{ is_array($plan->features) && in_array($safeKey, $plan->features) ? 'checked' : '' }}>
                        </div>
                        <span class="text-sm font-medium text-[#eaeef2] group-hover:text-[#d2a63d] transition-colors">{{ $safeLabel }}</span>
                    </label>
                @endforeach
            </div>

            {{-- 3. Submit Update Button --}}
            <div class="flex justify-end pt-6 border-t border-[#d2a63d]/20">
                <button type="submit" class="bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs transition-all shadow-[0_0_20px_rgba(210,166,61,0.3)] hover:scale-105 flex items-center gap-2">
                    <i class="fa-solid fa-satellite-dish"></i> Deploy Matrix Updates
                </button>
            </div>
        </div>
    </form>

</div>
@endsection