@extends('layouts.superadmin')

@section('title', 'Partner Directory | Master Control')

@section('content')
<div class="w-full">
    
    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]"><span class="font-bold text-[#d2a63d]">Partner</span> Directory</h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Manage, monitor, and configure your authorized business partners.</p>
        </div>
        <a href="{{ route('superadmin.partners.create') }}" class="bg-[#d2a63d] text-[#010717] px-6 py-3 rounded-xl font-black uppercase text-xs hover:bg-[#b88e2c] transition-all shadow-[0_0_20px_rgba(210,166,61,0.3)] hover:scale-105 flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Register New Partner
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="mb-6 bg-green-500/10 text-green-400 p-4 rounded-xl border border-green-500/30 font-bold flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-2xl relative">
        {{-- Subtle Background Glow --}}
        <div class="absolute top-0 right-1/2 w-64 h-64 bg-[#d2a63d]/5 blur-[100px] pointer-events-none z-0"></div>

        <div class="overflow-x-auto relative z-10">
            <table class="w-full text-left text-sm text-[#eaeef2]">
                <thead class="bg-[#d2a63d]/5 text-[#d2a63d] uppercase text-[10px] tracking-widest font-black border-b border-[#d2a63d]/20">
                    <tr>
                        <th class="px-8 py-5">Partner Identity</th>
                        <th class="px-6 py-5 text-center">Tenants Managed</th>
                        <th class="px-6 py-5 text-center">Account Status</th>
                        <th class="px-8 py-5 text-right">Controls & Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#d2a63d]/10">
                    @forelse($partners as $partner)
                    <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                        
                        {{-- Partner Details --}}
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#0a1225] border border-[#d2a63d]/30 flex items-center justify-center text-[#d2a63d] shadow-inner font-black text-lg">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-base text-[#eaeef2]">{{ $partner->name }}</div>
                                    <div class="text-[11px] text-[#eaeef2]/50 mt-0.5"><i class="fa-solid fa-envelope mr-1 text-[#d2a63d]/70"></i> {{ $partner->email }}</div>
                                    <div class="text-[11px] text-[#eaeef2]/50"><i class="fa-solid fa-phone mr-1 text-[#d2a63d]/70"></i> {{ $partner->phone ?? 'Not Provided' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Tenants Managed --}}
                        <td class="px-6 py-5 text-center">
                            <div class="inline-flex flex-col items-center justify-center bg-[#0a1225] border border-[#d2a63d]/20 w-12 h-12 rounded-xl shadow-inner">
                                <span class="font-black text-[#d2a63d] text-lg leading-none">{{ $partner->companies_count }}</span>
                                <span class="text-[8px] text-[#eaeef2]/40 uppercase tracking-widest mt-1">Total</span>
                            </div>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1.5 rounded-full text-[10px] uppercase font-black tracking-widest border {{ $partner->status == 'active' ? 'bg-[#d2a63d]/10 text-[#d2a63d] border-[#d2a63d]/30' : 'bg-red-500/10 text-red-400 border-red-500/30' }}">
                                <i class="fa-solid fa-circle text-[8px] mr-1"></i> {{ $partner->status }}
                            </span>
                        </td>

                        {{-- Controls & Actions --}}
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end items-center gap-2">
                                
                                {{-- ⚡ 0. Enter God Mode (Impersonate Partner) --}}
                                <form action="{{ route('superadmin.partners.impersonate', $partner->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Enter God Mode" class="w-8 h-8 rounded-lg bg-[#d2a63d]/10 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] border border-[#d2a63d]/30 flex items-center justify-center transition-all shadow-[0_0_10px_rgba(210,166,61,0.2)]">
                                        <i class="fa-solid fa-bolt text-xs"></i>
                                    </button>
                                </form>

                                {{-- 1. View / Show Route --}}
                                <a href="{{ route('superadmin.partners.show', $partner->id) }}" title="View Intel" class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white border border-blue-500/30 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                {{-- 2. Edit Route --}}
                                <a href="{{ route('superadmin.partners.edit', $partner->id) }}" title="Modify Config" class="w-8 h-8 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500 hover:text-[#010717] border border-yellow-500/30 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                
                                {{-- 3. Toggle (Kick / Unban) --}}
                                <form action="{{ route('superadmin.partners.toggle', $partner->id) }}" method="POST" class="inline">
                                    @csrf
                                    @if($partner->status == 'active')
                                        <button type="submit" title="Kick / Suspend" class="h-8 px-3 rounded-lg bg-orange-500/10 text-orange-400 border border-orange-500/30 hover:bg-orange-500 hover:text-white flex items-center justify-center gap-1 font-black text-[9px] uppercase tracking-widest transition-all">
                                            <i class="fa-solid fa-user-slash text-[10px]"></i> KICK
                                        </button>
                                    @else
                                        <button type="submit" title="Unban / Restore" class="h-8 px-3 rounded-lg bg-green-500/10 text-green-400 border border-green-500/30 hover:bg-green-500 hover:text-[#010717] flex items-center justify-center gap-1 font-black text-[9px] uppercase tracking-widest transition-all">
                                            <i class="fa-solid fa-user-check text-[10px]"></i> UNBAN
                                        </button>
                                    @endif
                                </form>

                                {{-- 4. Delete Route --}}
                                <form action="{{ route('superadmin.partners.destroy', $partner->id) }}" method="POST" class="inline" onsubmit="return confirm('WARNING: Permanently delete this partner and all their managed companies?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Purge Partner" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/30 flex items-center justify-center transition-all">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-16 text-center">
                            <div class="inline-flex w-16 h-16 rounded-full bg-slate-800/50 items-center justify-center mb-4 border border-slate-700">
                                <i class="fa-solid fa-users-slash text-2xl text-slate-500"></i>
                            </div>
                            <p class="text-white font-black uppercase tracking-widest mb-1">No Partners Found</p>
                            <p class="text-xs text-slate-500">Aapne abhi tak koi partner register nahi kiya hai.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection