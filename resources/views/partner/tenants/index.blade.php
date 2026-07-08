@extends('layouts.superadmin') {{-- Yahan wahi combined layout aayega --}}

@section('title', 'Manage Tenants | Workspace')

@section('content')
<div class="space-y-6 w-full">
    
    {{-- Header Area --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Manage <span class="font-bold text-[#d2a63d]">Tenants</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Deploy, monitor, and configure client workspaces.</p>
        </div>
        
        <div>
            <a href="{{ route('partner.company.create') }}" class="bg-[#d2a63d] hover:bg-[#b58c2e] text-[#010717] px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(210,166,61,0.2)] flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Deploy New Tenant
            </a>
        </div>
    </div>

    {{-- Tenants List Grid --}}
    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-[#d2a63d]/20 bg-[#d2a63d]/5 flex justify-between items-center">
            <h3 class="text-sm font-black uppercase text-[#d2a63d] tracking-widest"><i class="fa-solid fa-server mr-2"></i> Active Workspaces</h3>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#d2a63d]/5 text-[#d2a63d] text-[10px] uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold w-1/3">Tenant Workspace</th>
                        <th class="px-6 py-4 font-bold">Plan Details</th>
                        <th class="px-6 py-4 font-bold text-center">Workforce</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#d2a63d]/10 text-sm">
                    @forelse($companies as $company)
                        <tr class="hover:bg-[#d2a63d]/5 transition-colors group">
                            
                            {{-- Tenant Name & Monogram --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    {{-- Clean Typography Monogram --}}
                                    <div class="w-10 h-10 rounded-lg bg-[#d2a63d]/10 border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] font-black text-lg uppercase shadow-inner">
                                        {{ substr($company->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-[#eaeef2] font-bold">{{ $company->name }}</div>
                                        <div class="text-[#eaeef2]/40 text-[10px] font-mono tracking-widest">{{ $company->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Plan Details --}}
                            <td class="px-6 py-4">
                                <span class="bg-orange-500/10 text-orange-400 border border-orange-500/20 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest inline-block mb-1">
                                    {{ $company->plan_type ?? 'Starter' }}
                                </span>
                                <div class="text-[#eaeef2]/40 text-[9px] uppercase tracking-widest font-bold">
                                    Ends: {{ $company->subscription_ends_at ? \Carbon\Carbon::parse($company->subscription_ends_at)->format('d M, Y') : 'N/A' }}
                                </div>
                            </td>

                            {{-- Workforce Stats --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="text-center">
                                        <span class="block text-[#d2a63d] font-black font-mono">{{ $company->admins_count ?? 0 }}</span>
                                        <span class="text-[8px] text-[#eaeef2]/30 uppercase tracking-widest">Admins</span>
                                    </div>
                                    <div class="w-px h-6 bg-[#d2a63d]/20"></div>
                                    <div class="text-center">
                                        <span class="block text-[#eaeef2] font-black font-mono">{{ $company->employees_count ?? 0 }}</span>
                                        <span class="text-[8px] text-[#eaeef2]/30 uppercase tracking-widest">Staff</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if($company->status === 'active')
                                    <span class="bg-green-500/10 text-green-400 border border-green-500/20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                        <i class="fa-solid fa-circle text-[6px] mr-1 pb-0.5 align-middle"></i> Active
                                    </span>
                                @else
                                    <span class="bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                        <i class="fa-solid fa-circle text-[6px] mr-1 pb-0.5 align-middle"></i> Suspended
                                    </span>
                                @endif
                            </td>

                            {{-- Operations / Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Manage Settings --}}
                                    <a href="{{ route('partner.company.edit', $company->id) }}" class="w-8 h-8 rounded bg-[#0a1225] border border-[#d2a63d]/20 flex items-center justify-center text-[#eaeef2]/50 hover:text-[#d2a63d] hover:border-[#d2a63d] transition-all" title="Configure Workspace">
                                        <i class="fa-solid fa-sliders"></i>
                                    </a>
                                    
                                    {{-- God Mode (Impersonate) --}}
                                    <form action="{{ route('partner.impersonate', $company->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded bg-[#d2a63d]/10 border border-[#d2a63d]/20 flex items-center justify-center text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all" title="Enter God Mode">
                                            <i class="fa-solid fa-bolt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#eaeef2]/30 italic text-sm">
                                <i class="fa-solid fa-network-wired text-3xl mb-4 block opacity-20 text-[#d2a63d]"></i>
                                No tenant workspaces deployed yet. Click "Deploy New Tenant" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="p-4 border-t border-[#d2a63d]/10 bg-[#060b17]">
            {{ $companies->links() }}
        </div>
    </div>
</div>
@endsection