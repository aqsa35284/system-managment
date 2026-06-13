@extends('layouts.superadmin')

@section('title', 'Add New Tenant | Master Control')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-[#d2a63d]">Register New Tenant</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Add a new company and set their admin password.</p>
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
        <form action="{{ route('company.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <h3 class="text-lg font-bold text-[#eaeef2] pb-2 border-b border-[#d2a63d]/10">1. Company Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Company Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Company Logo (Optional)</label>
                    <input type="file" name="logo" accept="image/*" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-2.5 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#d2a63d]/10 file:text-[#d2a63d] hover:file:bg-[#d2a63d]/20">
                </div>

                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">SaaS Subscription Tier *</label>
                    <select name="plan_type" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                        <option value="starter">Starter (Basic Tracking)</option>
                        <option value="basic">Basic (Screenshots)</option>
                        <option value="pro">Pro (Chat & Forwarding)</option>
                        <option value="advanced">Advanced (OCR & AI Tracking)</option>
                        <option value="private_label">Private-Label (Custom Branding)</option>
                        <option value="enterprise">Enterprise (Custom Scalability)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Employee Quota (Max Limit) *</label>
                    <input type="number" name="max_employees" value="15" min="1" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
            </div>

            <h3 class="text-lg font-bold text-[#eaeef2] pt-4 pb-2 border-b border-[#d2a63d]/10">2. Admin Credentials</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Admin Login Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[#d2a63d] text-sm font-bold mb-2">Set Admin Password *</label>
                    <input type="text" name="admin_password" required placeholder="Enter strong password" class="w-full bg-[#0a1225] border border-[#d2a63d]/30 rounded-xl px-4 py-3 text-[#eaeef2] focus:border-[#d2a63d] outline-none transition-all">
                </div>
            </div>

            <div class="pt-6 border-t border-[#d2a63d]/10 flex justify-end">
                <button type="submit" class="bg-[#d2a63d] text-[#010717] px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-[#b88e2c] transition-all">
                    <i class="fa-solid fa-save"></i> Create Company & Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection