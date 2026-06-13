@extends('layouts.superadmin')

@section('title', 'Edit Tenant | Master Control')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-[#d2a63d]">Modify Tenant Data</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Update configurations, logo, and keys for {{ $company->name }}.</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="text-[#eaeef2]/60 hover:text-[#d2a63d] transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-500/10 text-red-400 px-4 py-3 rounded-xl border border-red-500/30">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-lg">
        <form action="{{ route('company.update', $company->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <h3 class="text-lg font-bold text-[#eaeef2] pb-2 border-b border-[#d2a63d]/10">1. Company Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Company Name *</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Update Company Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-2.5 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#d2a63d]/10 file:text-[#d2a63d] hover:file:bg-[#d2a63d]/20">
                    <p class="text-[#eaeef2]/40 text-[10px] mt-1">Leave blank to retain current logo asset.</p>
                </div>

                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">SaaS Subscription Tier *</label>
                    <select name="plan_type" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                        <option value="starter" {{ $company->plan_type == 'starter' ? 'selected' : '' }}>Starter (Basic Tracking)</option>
                        <option value="basic" {{ $company->plan_type == 'basic' ? 'selected' : '' }}>Basic (Screenshots)</option>
                        <option value="pro" {{ $company->plan_type == 'pro' ? 'selected' : '' }}>Pro (Chat & Forwarding)</option>
                        <option value="advanced" {{ $company->plan_type == 'advanced' ? 'selected' : '' }}>Advanced (OCR & AI)</option>
                        <option value="private_label" {{ $company->plan_type == 'private_label' ? 'selected' : '' }}>Private-Label</option>
                        <option value="enterprise" {{ $company->plan_type == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Employee Quota (Max Limit) *</label>
                    <input type="number" name="max_employees" value="{{ old('max_employees', $company->max_employees) }}" min="1" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>

                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Primary Contact Email *</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
            </div>

            <h3 class="text-lg font-bold text-[#eaeef2] pt-4 pb-2 border-b border-[#d2a63d]/10">2. Admin Credentials (Optional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Reset Admin Password</label>
                    <input type="text" name="admin_password" placeholder="Leave empty to keep current password" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                    <p class="text-[#eaeef2]/40 text-[10px] mt-1">Only fill this field if you want to force change the admin's password.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-[#d2a63d]/10 flex justify-end">
                <button type="submit" class="bg-[#d2a63d] text-[#010717] px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-[#b88e2c] transition-all">
                    <i class="fa-solid fa-save"></i> Save Configurations
                </button>
            </div>
        </form>
    </div>
</div>
@endsection