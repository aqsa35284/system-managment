@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen">
    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter">Project <span class="text-blue-600">Fleet</span></h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Active Mission Directory</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black uppercase italic text-[10px] shadow-xl shadow-blue-100 hover:-translate-y-1 transition-all flex items-center gap-2">
            <i class="fas fa-rocket"></i> Launch New Mission
        </a>
    </div>

    {{-- Projects Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($projects as $project)
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 hover:shadow-xl transition-all flex flex-col justify-between group relative overflow-hidden">
            
            <div>
                <div class="flex justify-between items-start mb-6">
                    <span class="text-[9px] font-black uppercase px-4 py-1 rounded-full italic border 
                        {{ $project->status == 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-100' }}">
                        {{ $project->status }}
                    </span>
                    <p class="text-[10px] font-black text-slate-300 italic uppercase">#PROJ-{{ $project->id }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-[10px] font-black text-blue-600 uppercase italic tracking-widest mb-1">{{ $project->client_name ?? 'Internal Operation' }}</p>
                    <h3 class="text-2xl font-black text-slate-800 uppercase italic tracking-tighter leading-tight">{{ $project->name }}</h3>
                </div>

                {{-- Team Fleet Avatars --}}
                <div class="flex items-center mb-8">
                    <div class="flex -space-x-3 overflow-hidden">
                        @foreach($project->employees as $emp)
                            <div class="inline-block h-10 w-10 rounded-xl ring-4 ring-white bg-slate-100 overflow-hidden border border-slate-100" title="{{ $emp->name }}">
                                @if($emp->profile_image)
                                    <img src="{{ asset('storage/'.$emp->profile_image) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-blue-50 text-blue-600 font-black text-xs uppercase italic">
                                        {{ substr($emp->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <span class="ml-4 text-[9px] font-black text-slate-400 uppercase italic tracking-widest">+{{ $project->employees->count() }} Units</span>
                </div>
            </div>

            {{-- Footer & Actions --}}
            <div class="pt-6 border-t border-slate-50">
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase italic">Target Deadline</p>
                        <p class="text-xs font-black text-slate-600 italic tracking-tighter">
                            {{ $project->deadline ? $project->deadline->format('d M, Y') : 'Ongoing' }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        {{-- Edit Action --}}
                        <a href="{{ route('admin.projects.edit', $project->id) }}" 
                           class="p-3 bg-slate-50 text-slate-400 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all group/btn"
                           title="Edit Mission">
                            <i class="fas fa-cog group-hover/btn:rotate-90 transition-transform duration-500"></i>
                        </a>

                        {{-- Delete Action --}}
                        <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Abort Mission? All data for this unit will be decommissioned.')" 
                                    class="p-3 bg-slate-50 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-all group/btn"
                                    title="Delete Mission">
                                <i class="fas fa-trash-alt group-hover/btn:animate-pulse"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($projects->isEmpty())
    <div class="bg-white rounded-[2.5rem] p-20 border border-dashed border-slate-200 flex flex-col items-center justify-center text-center mt-8">
        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-300 text-3xl mb-4">
            <i class="fas fa-folder-open"></i>
        </div>
        <h3 class="text-xl font-black text-slate-800 uppercase italic">No Active Missions</h3>
        <p class="text-xs text-slate-400 font-bold max-w-xs mt-2">The fleet is currently idle. Launch a new project to begin operations.</p>
    </div>
    @endif
</div>
@endsection