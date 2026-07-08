@extends('layouts.superadmin')

@section('title', 'Register Partner | Master Command')

@section('content')
<div class="max-w-xl mx-auto">
    {{-- Glassmorphism Card --}}
    <div class="bg-[#010717]/80 backdrop-blur-xl border border-[#d2a63d]/20 rounded-[2rem] p-10 shadow-[0_0_50px_rgba(0,0,0,0.5)] relative overflow-hidden">
        
        {{-- Decorative Glow --}}
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#d2a63d]/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col items-center mb-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-[#d2a63d]/10 flex items-center justify-center mb-4 border border-[#d2a63d]/20">
                <i class="fa-solid fa-user-tie text-2xl text-[#d2a63d]"></i>
            </div>
            <h2 class="text-2xl font-black text-[#eaeef2] uppercase tracking-widest">New Partner Access</h2>
            <p class="text-[#eaeef2]/50 text-xs uppercase tracking-widest mt-1">Initialize Authorized Business Profile</p>
        </div>
        
        <form action="{{ route('superadmin.partners.store') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Name --}}
            <div class="relative">
                <label class="text-[10px] font-black text-[#d2a63d] uppercase tracking-widest ml-1 mb-1 block">Full Name</label>
                <input type="text" name="name" required class="w-full bg-[#0a1225]/50 border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm text-white focus:border-[#d2a63d] outline-none transition-all focus:ring-1 focus:ring-[#d2a63d]/30">
            </div>

            {{-- Email --}}
            <div class="relative">
                <label class="text-[10px] font-black text-[#d2a63d] uppercase tracking-widest ml-1 mb-1 block">Email Address</label>
                <input type="email" name="email" required class="w-full bg-[#0a1225]/50 border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm text-white focus:border-[#d2a63d] outline-none transition-all">
            </div>

            {{-- Phone --}}
            <div class="relative">
                <label class="text-[10px] font-black text-[#d2a63d] uppercase tracking-widest ml-1 mb-1 block">Phone Number</label>
                <input type="text" name="phone" placeholder="+92 XXX XXXXXXX" class="w-full bg-[#0a1225]/50 border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm text-white focus:border-[#d2a63d] outline-none transition-all">
            </div>

            {{-- Password with Show/Hide Toggle --}}
            <div class="relative" x-data="{ show: false }">
                <label class="text-[10px] font-black text-[#d2a63d] uppercase tracking-widest ml-1 mb-1 block">Secure Password</label>
                <input :type="show ? 'text' : 'password'" name="password" required class="w-full bg-[#0a1225]/50 border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm text-white focus:border-[#d2a63d] outline-none transition-all">
                <button type="button" @click="show = !show" class="absolute right-4 top-9 text-[#d2a63d]/50 hover:text-[#d2a63d]">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            
            <button type="submit" class="w-full bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] py-4 rounded-xl font-black uppercase tracking-widest text-xs transition-all hover:scale-[1.02] shadow-[0_0_20px_rgba(210,166,61,0.2)]">
                Authorize Access
            </button>
        </form>
    </div>
</div>

{{-- AlpineJS for Password Toggle --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection