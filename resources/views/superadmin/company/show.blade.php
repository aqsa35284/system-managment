@extends('layouts.superadmin')

@section('title', 'Tenant Details | Master Control')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-[#d2a63d]">Company Overview</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Detailed overview of {{ $company->name }}.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('company.edit', $company->id) }}" class="border border-[#d2a63d]/30 hover:bg-[#d2a63d]/10 text-[#d2a63d] px-5 py-2 rounded-xl transition-all flex items-center">
                <i class="fa-solid fa-pen mr-2"></i> Edit Config
            </a>
            <a href="{{ route('superadmin.dashboard') }}" class="text-[#eaeef2]/60 hover:text-[#d2a63d] transition-colors flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl p-8 shadow-lg mb-8">
        {{-- Changed from grid-cols-4 to grid-cols-5 to fit Plan Type --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="flex flex-col gap-1">
                <span class="text-[#eaeef2]/50 text-xs uppercase font-bold">Company Name</span>
                <span class="text-[#eaeef2] text-lg font-medium">{{ $company->name }}</span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-[#eaeef2]/50 text-xs uppercase font-bold">Primary Email</span>
                <span class="text-[#d2a63d] text-lg font-medium">{{ $company->email }}</span>
            </div>
            
            {{-- NEW: Subscription Tier --}}
            <div class="flex flex-col gap-1">
                <span class="text-[#eaeef2]/50 text-xs uppercase font-bold">Plan Tier</span>
                <span class="text-[#d2a63d] text-lg font-bold uppercase tracking-wider">
                    {{ str_replace('_', ' ', $company->plan_type ?? 'Starter') }}
                </span>
            </div>

            <div class="flex flex-col gap-1">
                <span class="text-[#eaeef2]/50 text-xs uppercase font-bold">Status</span>
                <span class="text-lg font-medium {{ $company->status == 'active' ? 'text-green-400' : 'text-red-400' }}">
                    {{ ucfirst($company->status) }}
                </span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-[#eaeef2]/50 text-xs uppercase font-bold">Employee Quota</span>
                <span class="text-[#eaeef2] text-lg font-medium">
                    <span class="text-[#d2a63d] font-bold">{{ $company->users->count() }}</span> / {{ $company->max_employees }} Used
                </span>
            </div>
        </div>
    </div>

    <h3 class="text-xl font-bold text-[#d2a63d] mb-4"><i class="fa-solid fa-users mr-2"></i>Registered Accounts</h3>
    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-2xl overflow-hidden shadow-lg">
        <table class="w-full text-left text-sm text-[#eaeef2]">
            <thead class="bg-[#d2a63d]/5 text-[#d2a63d] text-xs uppercase border-b border-[#d2a63d]/20">
                <tr>
                    <th class="px-6 py-4 font-bold">Name</th>
                    <th class="px-6 py-4 font-bold">Email Address</th>
                    <th class="px-6 py-4 font-bold">Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d2a63d]/10">
                @forelse($company->users as $user)
                <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                    <td class="px-6 py-4 font-bold flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#0a1225] border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d]">
                            <i class="fa-solid fa-user text-xs"></i>
                        </div>
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 text-[#eaeef2]/70">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-[#d2a63d]/10 text-[#d2a63d] px-3 py-1 rounded-full text-[10px] uppercase font-bold tracking-wider border border-[#d2a63d]/30">
                            {{ $user->role }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-[#eaeef2]/50">No users registered under this tenant yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection