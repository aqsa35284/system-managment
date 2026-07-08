@extends('layouts.superadmin')

@section('title', 'Modify Tenant | Master Control')

@section('content')

{{-- 💡 SMART ROUTING --}}
@php
    $routePrefix = auth()->check() && auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';
@endphp

<div class="max-w-6xl mx-auto">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-[#d2a63d]">Modify Tenant Data</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Update configurations, limits, and pricing tiers for <span class="font-bold text-[#eaeef2]">{{ $company->name }}</span>.</p>
        </div>
        <a href="{{ route($routePrefix . 'dashboard') }}" class="text-[#eaeef2]/60 hover:text-[#d2a63d] transition-colors font-bold text-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-500/10 text-red-400 px-4 py-3 rounded-xl border border-red-500/30">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- LEFT SIDE: Data Update Form --}}
        <div class="flex-1 bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-2xl relative">
            <form action="{{ route($routePrefix . 'company.update', $company->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="tenantForm">
                @csrf
                @method('PUT')
                
                <h3 class="text-lg font-bold text-[#eaeef2] pb-2 border-b border-[#d2a63d]/10 flex items-center gap-2">
                    <i class="fa-solid fa-building text-[#d2a63d]"></i> 1. Company Settings
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">Company Name *</label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                    </div>
                    
                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">Update Logo (Optional)</label>
                        <input type="file" name="logo" accept="image/*" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-2.5 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">SaaS Subscription Tier *</label>
                        <select name="plan_type" id="planSelector" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                            <option value="" disabled>Select a Tier...</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->slug }}" data-base="{{ $plan->price }}" data-extra="{{ $plan->extra_user_rate }}" {{ $company->plan_type == $plan->slug ? 'selected' : '' }}>
                                    {{ $plan->name }} (Base: PKR {{ number_format($plan->price) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-[#eaeef2]/50 mt-1 uppercase tracking-widest">Features will automatically sync on update.</p>
                    </div>

                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">Employee Quota (Min 5) *</label>
                        <input type="number" name="max_employees" id="employeeCount" value="{{ old('max_employees', $company->max_employees) }}" min="5" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">Primary Contact Email *</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                    </div>
                </div>

                <h3 class="text-lg font-bold text-[#eaeef2] pt-4 pb-2 border-b border-[#d2a63d]/10 flex items-center gap-2">
                    <i class="fa-solid fa-key text-[#d2a63d]"></i> 2. Admin Credentials (Optional)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[#d2a63d] text-[11px] uppercase tracking-widest font-black mb-2">Reset Admin Password</label>
                        <input type="text" name="admin_password" placeholder="Leave empty to keep current" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all shadow-inner">
                        <p class="text-[#eaeef2]/40 text-[10px] mt-2 italic">Fill this only to force change the password.</p>
                    </div>
                </div>

                <div class="pt-8 border-t border-[#d2a63d]/10 flex justify-end">
                    <button type="submit" class="bg-[#d2a63d] text-[#010717] px-10 py-4 rounded-xl font-black uppercase tracking-widest text-[11px] flex items-center gap-3 hover:bg-[#b88e2c] transition-all shadow-[0_0_20px_rgba(210,166,61,0.3)] hover:scale-105">
                        <i class="fa-solid fa-save"></i> Save Configurations
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE: Real-Time Dynamic Pricing Engine --}}
        <div class="w-full lg:w-96 bg-[#0a1225] border border-[#d2a63d]/50 rounded-3xl p-8 shadow-[0_0_30px_rgba(210,166,61,0.1)] h-fit sticky top-24">
            <div class="border-b border-[#d2a63d]/20 pb-4 mb-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-[#d2a63d]"><i class="fa-solid fa-chart-line mr-2"></i> Projected Billing Summary</h3>
            </div>
            
            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-[#eaeef2]/70 font-medium">Base Plan (5 Users)</span>
                    <span class="text-sm font-mono text-[#eaeef2]" id="summaryBasePrice">PKR 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-[#eaeef2]/70 font-medium">Extra Users (<span id="extraUserCount">0</span>)</span>
                    <span class="text-sm font-mono text-[#eaeef2]" id="summaryExtraCost">PKR 0</span>
                </div>
                <div class="flex justify-between items-center text-[10px] text-[#d2a63d] uppercase font-bold border-t border-white/5 pt-4">
                    <span>Rate / Extra User:</span>
                    <span id="summaryRate">PKR 0</span>
                </div>
            </div>

            <div class="bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-[#d2a63d]/20 blur-xl rounded-full"></div>
                <span class="block text-[10px] text-[#d2a63d] uppercase tracking-widest font-black mb-2 relative z-10">Total Monthly Invoice</span>
                <span class="text-3xl font-black text-[#eaeef2] relative z-10" id="summaryTotal">PKR 0</span>
                <span class="block text-[9px] text-[#eaeef2]/50 mt-3 italic relative z-10 border-t border-[#d2a63d]/20 pt-2">*This is the new projected amount for future invoices based on these settings.</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const planSelector = document.getElementById('planSelector');
        const employeeCount = document.getElementById('employeeCount');
        const uiBasePrice = document.getElementById('summaryBasePrice');
        const uiExtraCost = document.getElementById('summaryExtraCost');
        const uiExtraCount = document.getElementById('extraUserCount');
        const uiRate = document.getElementById('summaryRate');
        const uiTotal = document.getElementById('summaryTotal');

        function calculateInvoice() {
            const selectedOption = planSelector.options[planSelector.selectedIndex];
            if(!selectedOption.value) return;

            const basePrice = parseInt(selectedOption.getAttribute('data-base')) || 0;
            const extraUserRate = parseInt(selectedOption.getAttribute('data-extra')) || 0;
            const totalEmployees = parseInt(employeeCount.value) || 5;

            const extraUsers = Math.max(0, totalEmployees - 5);
            const extraUsersCost = extraUsers * extraUserRate;
            const grandTotal = basePrice + extraUsersCost;

            uiBasePrice.innerText = 'PKR ' + basePrice.toLocaleString();
            uiExtraCount.innerText = extraUsers;
            uiExtraCost.innerText = 'PKR ' + extraUsersCost.toLocaleString();
            uiRate.innerText = 'PKR ' + extraUserRate.toLocaleString();
            uiTotal.innerText = 'PKR ' + grandTotal.toLocaleString();
        }

        planSelector.addEventListener('change', calculateInvoice);
        employeeCount.addEventListener('input', calculateInvoice);
        calculateInvoice();
    });
</script>
@endsection