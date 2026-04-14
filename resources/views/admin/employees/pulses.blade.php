@extends('layouts.admin')

@section('content')
<div class="container-fluid p-6 bg-[#020617] min-h-screen text-white">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold uppercase tracking-tighter">Operator Pulses: <span class="text-indigo-400">{{ $employee->name }}</span></h1>
            <p class="text-slate-400 text-sm italic">Uplink: Visual Activity Logs Detected</p>
        </div>

        {{-- 🔍 Filter Buttons --}}
        <div class="flex flex-wrap gap-2 bg-slate-900 p-1.5 rounded-2xl border border-white/5 shadow-2xl">
            <a href="{{ route('admin.employees.pulses', [$employee->id, 'filter' => 'today']) }}" 
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('filter') == 'today' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:text-white' }}">Today</a>
            
            <a href="{{ route('admin.employees.pulses', [$employee->id, 'filter' => 'weekly']) }}" 
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('filter') == 'weekly' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:text-white' }}">Weekly</a>
            
            <a href="{{ route('admin.employees.pulses', [$employee->id, 'filter' => 'monthly']) }}" 
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('filter') == 'monthly' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:text-white' }}">Monthly</a>

            <a href="{{ route('admin.employees.pulses', [$employee->id, 'filter' => 'yearly']) }}" 
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('filter') == 'yearly' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:text-white' }}">Yearly</a>
            
            <a href="{{ route('admin.employees.pulses', $employee->id) }}" 
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !request('filter') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white' }}">All Logs</a>
        </div>
    </div>

    {{-- 🖼️ Screenshots Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        @forelse($screenshots as $snap)
            <div id="pulse-card-{{ $snap->id }}" class="group relative bg-slate-900 border border-white/5 rounded-[2rem] overflow-hidden hover:border-indigo-500/50 transition-all shadow-2xl">
                
                {{-- ⭐ Delete Icon (Top Left) --}}
                <div class="absolute top-3 left-3 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="deletePulse({{ $snap->id }})" class="w-8 h-8 bg-rose-600/20 text-rose-500 hover:bg-rose-600 hover:text-white border border-rose-500/30 rounded-full flex items-center justify-center backdrop-blur-md transition-all">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </button>
                </div>

                {{-- Image --}}
                <img src="{{ asset('storage/' . $snap->image_path) }}" 
                     class="w-full h-44 object-cover cursor-zoom-in group-hover:scale-110 transition-transform duration-700" 
                     alt="Pulse"
                     onclick="viewFullImage(this.src)">
                
                <div class="p-4 bg-slate-900/90 backdrop-blur-xl border-t border-white/5">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[9px] text-indigo-400 font-black uppercase italic">Pulse Detected</span>
                        <span class="text-[8px] text-white/30 font-mono">{{ $snap->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[10px] text-white font-bold truncate tracking-tight">{{ $snap->window_title }}</p>
                    <p class="text-[8px] text-slate-500 mt-1 font-mono uppercase">{{ $snap->created_at->format('d.M.Y | H:i:s') }}</p>
                </div>

                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-expand text-[10px]"></i>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center bg-slate-900/50 rounded-[3rem] border border-dashed border-white/10">
                <i class="fas fa-satellite-dish text-5xl text-slate-700 mb-4 animate-pulse"></i>
                <p class="text-slate-500 uppercase font-black italic tracking-[0.3em] text-xs">No visual pulses detected in this temporal range.</p>
            </div>
        @endforelse
    </div>

    {{-- 🛰️ Pagination --}}
    <div class="mt-12 flex justify-center">
        {{ $screenshots->appends(request()->query())->links() }}
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" class="fixed inset-0 z-[9999] hidden bg-black/98 flex items-center justify-center p-8 cursor-zoom-out" onclick="this.classList.add('hidden')">
    <div class="relative max-w-7xl w-full flex flex-col items-center">
        <img id="fullImg" src="" class="w-full rounded-[2rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-white/10">
        <p class="mt-6 text-slate-400 text-[10px] font-black uppercase tracking-[0.5em]">Press anywhere to close uplink</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewFullImage(src) {
        document.getElementById('fullImg').src = src;
        document.getElementById('lightbox').classList.remove('hidden');
    }

    // ⭐ MANUAL DELETE FUNCTION
    function deletePulse(id) {
        Swal.fire({
            title: 'TERMINATE PULSE?',
            text: "This visual log will be wiped from storage!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4f46e5',
            confirmButtonText: 'Yes, Delete',
            background: '#020617',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/screenshots/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        // Card ko UI se gayab kar dena bagair reload ke
                        const card = document.getElementById(`pulse-card-${id}`);
                        card.classList.add('opacity-0', 'scale-50');
                        setTimeout(() => card.remove(), 400);

                        Swal.fire({
                            title: 'WIPED',
                            text: data.message,
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            background: '#1e1b4b',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
</script>

<style>
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #020617; }
    ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 10px; }
</style>
@endsection