@extends('layouts.superadmin')

@section('title', 'Master Dashboard | Command Center')

@section('content')
<div class="w-full">

    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Welcome back, <span class="font-bold text-[#d2a63d]">Command.</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Global overview of your SaaS network.</p>
        </div>
        <a href="{{ route('company.create') }}" class="bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] px-6 py-2.5 rounded-xl shadow-[0_0_20px_rgba(210,166,61,0.3)] transition-all duration-300 font-bold flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Tenant
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg">
            <h3 class="text-[#eaeef2]/60 text-sm font-medium">Total Companies</h3>
            <p class="text-3xl font-bold mt-2 text-[#eaeef2]">{{ $stats['total_companies'] ?? 0 }}</p>
        </div>
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg">
            <h3 class="text-[#eaeef2]/60 text-sm font-medium">Active Subscriptions</h3>
            <p class="text-3xl font-bold mt-2 text-green-400">{{ $stats['active_companies'] ?? 0 }}</p>
        </div>
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg">
            <h3 class="text-[#eaeef2]/60 text-sm font-medium">Expired Accounts</h3>
            <p class="text-3xl font-bold mt-2 text-red-400">{{ $stats['expired_companies'] ?? 0 }}</p>
        </div>
        <div class="bg-[#010717] border border-[#d2a63d]/20 p-6 rounded-2xl shadow-lg">
            <h3 class="text-[#eaeef2]/60 text-sm font-medium">Unpaid Invoices</h3>
            <p class="text-3xl font-bold mt-2 text-[#d2a63d]">{{ $stats['unpaid_invoices'] ?? 0 }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl font-bold">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-lg">
        <div class="px-8 py-6 border-b border-[#d2a63d]/20 flex justify-between items-center">
            <h3 class="text-lg font-bold text-[#d2a63d]"><i class="fa-solid fa-server mr-2"></i>Tenant Directory</h3>
            <span class="text-xs text-[#eaeef2]/50">Showing all registered companies</span>
        </div>
        <table class="w-full text-left text-sm text-[#eaeef2]">
            <thead class="bg-[#d2a63d]/5 text-[#d2a63d] text-xs uppercase border-b border-[#d2a63d]/20">
                <tr>
                    <th class="px-8 py-5 font-bold">Logo & Company</th>
                    <th class="px-6 py-5 font-bold">Admin Email</th>
                    <th class="px-6 py-5 font-bold text-center w-40">Workforce Status</th>
                    <th class="px-6 py-5 font-bold">Status</th>
                    <th class="px-8 py-5 text-right font-bold">Controls</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d2a63d]/10">
                @forelse($companies ?? [] as $company)
                <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                    
                    {{-- UI FIX: Removed flex from <td> and added a wrapper <div> --}}
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" class="w-8 h-8 rounded-full border border-[#d2a63d]/50 object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-[#0a1225] border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] text-xs font-bold">
                                    {{ substr($company->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <span class="font-medium flex items-center gap-2">
                                    {{ $company->name }}
                                    <span class="bg-[#d2a63d]/20 text-[#d2a63d] px-2 py-0.5 rounded text-[9px] uppercase tracking-widest font-black">
                                        {{ str_replace('_', ' ', $company->plan_type ?? 'Starter') }}
                                    </span>
                                </span>
                                <span class="text-[10px] text-[#eaeef2]/40">Tenant ID: #{{ str_pad($company->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5 text-[#eaeef2]/70">{{ $company->email }}</td>
                    
                    {{-- Performance FIX: Using pre-loaded aggregates instead of new queries --}}
                    <td class="px-6 py-5 w-40">
                        @php 
                            $liveAdminCount = $company->admins_count ?? 0;
                            $liveEmployeeCount = $company->employees_count ?? 0;
                            $totalUsers = $liveAdminCount + $liveEmployeeCount; 
                            $percentage = $company->max_employees > 0 ? ($totalUsers / $company->max_employees) * 100 : 0;
                            $barColor = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-[#d2a63d]');
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="text-[10px] mb-1 font-bold text-[#eaeef2]/70">
                                <span class="text-blue-400">{{ $liveAdminCount }}A</span> &bull; 
                                <span class="text-green-400">{{ $liveEmployeeCount }}S</span> 
                                <span class="text-[#d2a63d]">/ {{ $company->max_employees }} Max</span>
                            </div>
                            <div class="w-32 bg-[#010717] rounded-full h-1.5 border border-[#d2a63d]/20">
                                <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $company->status == 'active' ? 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }}">
                            {{ $company->status }}
                        </span>
                    </td>
                    
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            
                            <form action="{{ route('superadmin.impersonate', $company->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Login as Admin" class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 hover:bg-purple-500 hover:text-white border border-purple-500/30 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-user-secret text-xs"></i>
                                </button>
                            </form>

                            <a href="{{ route('company.show', $company->id) }}" title="View Intel" class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white border border-blue-500/30 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>

                            <a href="{{ route('company.edit', $company->id) }}" title="Modify Tenant" class="w-8 h-8 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500 hover:text-[#010717] border border-yellow-500/30 flex items-center justify-center transition-all">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>

                            <form action="{{ route('company.renew', $company->id) }}" method="POST" class="inline" onsubmit="return confirm('Renew subscription for 30 days?');">
                                @csrf
                                <button type="submit" title="Renew Billing" class="w-8 h-8 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500 hover:text-[#010717] border border-green-500/30 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                                </button>
                            </form>

                            @if($company->status == 'active')
                                <form action="{{ route('company.toggle', $company->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Kick / Suspend Tenant" class="h-8 px-3 rounded-lg bg-orange-500/10 text-orange-400 border border-orange-500/30 hover:bg-orange-500 hover:text-white flex items-center justify-center gap-1 font-black text-[9px] uppercase tracking-widest transition-all">
                                        <i class="fa-solid fa-user-slash text-[10px]"></i> KICK
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('company.toggle', $company->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Unban / Restore Tenant" class="h-8 px-3 rounded-lg bg-green-500/10 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-[#010717] flex items-center justify-center gap-1 font-black text-[9px] uppercase tracking-widest transition-all">
                                        <i class="fa-solid fa-user-check text-[10px]"></i> UNBAN
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('company.destroy', $company->id) }}" method="POST" class="inline" onsubmit="return confirm('WARNING: Permanently delete tenant and all data?');">
                                @csrf @method('DELETE')
                                <button type="submit" title="Purge Tenant" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/30 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-8 py-10 text-center text-[#eaeef2]/50">No tenants registered yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection