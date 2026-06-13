@extends('layouts.admin')

@section('content')
{{-- Favicon Inject --}}
<link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

<div class="p-8 bg-[#010717] min-h-screen">
    <div class="max-w-4xl mx-auto bg-[#030712] rounded-[3rem] shadow-[0_0_30px_rgba(210,166,61,0.05)] border border-[#d2a63d]/30 overflow-hidden">
        
        <div class="p-10 bg-[#d2a63d]/10 border-b border-[#d2a63d]/30 text-[#d2a63d] relative">
            <a href="{{ route('admin.projects.index') }}" class="absolute top-8 right-8 flex items-center justify-center w-12 h-12 rounded-xl bg-[#010717] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.2)] group" title="Back to Fleet">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            
            <h2 class="text-3xl font-black uppercase italic tracking-tighter">Modify <span class="opacity-70 text-[#eaeef2]">Project</span></h2>
            <p class="text-[10px] font-bold uppercase tracking-[0.3em] mt-2 italic text-[#eaeef2]/70">Mission: {{ $project->name }}</p>
        </div>

        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" class="p-10 space-y-6">
            @csrf 
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Project Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Project Name</label>
                    <input type="text" name="name" value="{{ $project->name }}" class="form-input w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                </div>
                
                {{-- Client Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Client Name</label>
                    <input type="text" name="client_name" value="{{ $project->client_name }}" class="form-input w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                </div>
                
                {{-- Start Date --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '' }}" class="form-input w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                </div>

                {{-- Deadline --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Deadline</label>
                    <input type="date" name="deadline" value="{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '' }}" class="form-input w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                </div>

                {{-- Budget --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#d2a63d] italic ml-2">Project Budget (PKR)</label>
                    <input type="number" name="budget" value="{{ $project->budget }}" class="form-input w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#d2a63d] italic ml-2">Project Status</label>
                    <select name="status" class="form-select w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none cursor-pointer text-[#eaeef2]">
                        <option value="pending" {{ $project->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                {{-- Team Assignment (Multi-select) --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Assign Team Members</label>
                    <select name="employees[]" multiple class="form-select w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]" style="min-height: 150px;">
                        @foreach($employees as $user)
                            <option value="{{ $user->id }}" 
                                @if($project->employees && $project->employees->contains($user->id)) selected @endif
                                style="padding: 8px;">
                                {{ $user->name }} - {{ $user->designation }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[9px] text-[#eaeef2]/50 mt-1 uppercase font-bold italic">Hold Ctrl to select multiple nodes</p>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Project Description</label>
                    <textarea name="description" rows="4" class="form-input w-full p-4 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">{{ $project->description }}</textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#d2a63d] text-[#010717] py-6 rounded-3xl font-black uppercase tracking-widest italic shadow-[0_0_20px_rgba(210,166,61,0.3)] hover:bg-[#b8860b] transition transform hover:-translate-y-1 mt-6">
                Update Project Config <i class="fas fa-save ml-2"></i>
            </button>
        </form>
    </div>
</div>

<style>
    /* Dark Theme Inputs Fix */
    .form-input, .form-select {
        color: #eaeef2 !important;
        -webkit-text-fill-color: #eaeef2 !important;
    }
    input[type="date"]::-webkit-calendar-picker-indicator { 
        filter: invert(1); opacity: 0.7; cursor: pointer;
    }
    .form-select option:checked {
        background: rgba(210, 166, 61, 0.2) !important;
        color: #d2a63d !important;
    }
</style>
@endsection