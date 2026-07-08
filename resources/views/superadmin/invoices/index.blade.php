@extends('layouts.superadmin')
@section('title', 'Billing & Invoices | Master Panel')

@section('content')
@php
    $routePrefix = auth()->check() && auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';
@endphp
<div class="w-full">
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Financial <span class="font-bold text-[#d2a63d]">Engine</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Manage global billing, invoices, and revenue streams.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl font-bold">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Revenue Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="bg-[#0a1225] border border-green-500/30 p-8 rounded-3xl shadow-[0_0_30px_rgba(34,197,94,0.1)] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/10 blur-2xl rounded-full"></div>
            <h3 class="text-[#eaeef2]/60 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-vault text-green-400 mr-2"></i>Total Collected Revenue</h3>
            <p class="text-4xl font-black text-green-400">PKR {{ number_format($totalRevenue) }}</p>
        </div>
        
        <div class="bg-[#0a1225] border border-orange-500/30 p-8 rounded-3xl shadow-[0_0_30px_rgba(249,115,22,0.1)] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 blur-2xl rounded-full"></div>
            <h3 class="text-[#eaeef2]/60 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-clock-rotate-left text-orange-400 mr-2"></i>Pending / Unpaid Invoices</h3>
            <p class="text-4xl font-black text-orange-400">PKR {{ number_format($pendingRevenue) }}</p>
        </div>
    </div>

    {{-- Invoices Ledger Table --}}
    <div class="bg-[#010717] border border-[#d2a63d]/20 rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-[#d2a63d]/20 flex justify-between items-center">
            <h3 class="text-lg font-bold text-[#d2a63d]"><i class="fa-solid fa-file-invoice mr-2"></i>Global Invoice Ledger</h3>
        </div>
        <table class="w-full text-left text-sm text-[#eaeef2]">
            <thead class="bg-[#d2a63d]/5 text-[#d2a63d] text-[10px] uppercase tracking-widest border-b border-[#d2a63d]/20">
                <tr>
                    <th class="px-8 py-5 font-black">Invoice #</th>
                    <th class="px-6 py-5 font-black">Tenant (Company)</th>
                    <th class="px-6 py-5 font-black">Plan & Quota</th>
                    <th class="px-6 py-5 font-black">Amount</th>
                    <th class="px-6 py-5 font-black">Status</th>
                    <th class="px-8 py-5 font-black text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d2a63d]/10">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                    <td class="px-8 py-5 font-mono text-xs text-[#eaeef2]/70">{{ $invoice->invoice_number }}</td>
                    <td class="px-6 py-5 font-bold">{{ $invoice->company ? $invoice->company->name : 'Deleted Tenant' }}</td>
                    <td class="px-6 py-5">
                        <span class="text-[#d2a63d] font-black uppercase text-[10px]">{{ $invoice->plan_name }}</span>
                        <span class="text-[#eaeef2]/50 text-[10px] ml-2 block">{{ $invoice->total_users }} Users</span>
                    </td>
                    <td class="px-6 py-5 font-black">PKR {{ number_format($invoice->total_amount) }}</td>
                    <td class="px-6 py-5">
                        @if($invoice->status == 'paid')
                            <span class="bg-green-500/10 text-green-400 border border-green-500/30 px-3 py-1 rounded text-[9px] uppercase font-black tracking-widest">PAID</span>
                        @else
                            <span class="bg-orange-500/10 text-orange-400 border border-orange-500/30 px-3 py-1 rounded text-[9px] uppercase font-black tracking-widest">UNPAID</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if($invoice->status == 'unpaid')
                            <form action="{{ route($routePrefix . 'invoices.pay', $invoice->id) }}" method="POST" onsubmit="return confirm('Confirm payment received for this invoice?');">
                                @csrf
                                <button type="submit" class="bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] px-4 py-2 rounded-lg font-black uppercase tracking-widest text-[9px] transition-all">
                                    Mark as Paid
                                </button>
                            </form>
                        @else
                            <button class="bg-white/5 text-[#eaeef2]/30 px-4 py-2 rounded-lg font-black uppercase tracking-widest text-[9px] cursor-not-allowed">
                                Cleared
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-8 py-10 text-center text-[#eaeef2]/50 text-xs uppercase font-bold tracking-widest">No invoices generated yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection