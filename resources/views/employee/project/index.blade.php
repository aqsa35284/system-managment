@extends('layouts.employee')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-[#eaeef2] uppercase tracking-widest">
            <i class="fas fa-rocket text-[#d2a63d] mr-2"></i> Project Fleet
        </h2>
        <p class="text-[#eaeef2]/40 text-sm mt-1">All active missions assigned to your unit.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($projects as $project)
        <div class="bg-[#010717] p-6 rounded-2xl border border-[#d2a63d]/20 hover:border-[#d2a63d]/60 transition-all group relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #010717 0%, #0a1225 100%);">
            <div class="absolute top-0 right-0 p-4">
                <span class="bg-[#d2a63d]/10 text-[#d2a63d] px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest border border-[#d2a63d]/30">
                    {{ str_replace('_', ' ', $project->status ?? 'ACTIVE') }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-[#eaeef2] mb-2 pr-16 truncate group-hover:text-[#d2a63d] transition-colors">{{ $project->name }}</h3>
            <p class="text-[#eaeef2]/40 text-sm mb-6 flex items-center">
                <i class="fas fa-building w-5 text-[#d2a63d]/50"></i> 
                {{ $project->client_name ?? 'Internal Operation' }}
            </p>
            
            <div class="flex items-center justify-between border-t border-[#d2a63d]/10 pt-4 mt-auto">
                <div class="text-xs text-[#eaeef2]/50 font-medium flex items-center">
                    <i class="fas fa-clock w-4 text-rose-400"></i> 
                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M, Y') }}
                </div>
                <a href="{{ route('employee.projects.show', $project->id) }}" class="text-[#d2a63d] text-xs font-bold uppercase tracking-widest hover:text-[#eaeef2] group-hover:translate-x-1 transition-all flex items-center">
                    Open <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full py-16 text-center bg-[#010717] rounded-2xl border border-dashed border-[#d2a63d]/20">
            <div class="w-20 h-20 mx-auto bg-[#d2a63d]/10 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-folder-open text-3xl text-[#d2a63d]/50"></i>
            </div>
            <p class="text-[#eaeef2] font-bold uppercase tracking-widest text-lg">No Active Projects</p>
            <p class="text-[#eaeef2]/40 text-sm mt-2">You haven't been assigned to any missions yet.</p>
        </div>
    @endforelse
</div>
@endsection