@extends('layouts.admin')

@section('content')
 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="p-8 bg-[#010717] min-h-screen flex justify-center items-start">
    <div class="max-w-4xl w-full">
        
        {{-- Header Section --}}
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-[#eaeef2] uppercase italic tracking-tighter leading-none">
                    Modify <span class="text-[#d2a63d]">Objective</span>
                </h2>
                <p class="text-[10px] font-bold text-[#eaeef2]/50 uppercase tracking-widest mt-2 italic">
                    Update Mission Parameters for Node: {{ $task->id }}
                </p>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Back to Board">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
        </div>

        {{-- Edit Form Card --}}
        <div class="bg-[#030712] rounded-[3rem] shadow-2xl border border-[#d2a63d]/30 overflow-hidden">
            <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" class="p-12">
                @csrf 
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    {{-- Objective Title --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Objective Title</label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}" 
                               class="w-full p-5 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none transition-all text-[#eaeef2]" required>
                    </div>

                    {{-- Target Project --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Target Project</label>
                        <select name="project_id" class="w-full p-5 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none cursor-pointer text-[#eaeef2]">
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $task->project_id == $project->id ? 'selected' : '' }} class="bg-[#010717]">
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fleet Member --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#d2a63d] italic ml-2">Assigned Operator</label>
                        <select name="user_id" class="w-full p-5 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none cursor-pointer text-[#eaeef2]">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $task->user_id == $emp->id ? 'selected' : '' }} class="bg-[#010717]">
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority Level --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Priority Level</label>
                        <select name="priority" class="w-full p-5 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]">
                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }} class="bg-[#010717]">Low</option>
                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }} class="bg-[#010717]">Medium</option>
                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }} class="bg-[#010717]">High</option>
                            <option value="urgent" {{ $task->priority == 'urgent' ? 'selected' : '' }} class="bg-[#010717]">Urgent</option>
                        </select>
                    </div>

                    {{-- Objective Status --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#d2a63d] italic ml-2">Mission Status</label>
                        <select name="status" class="w-full p-5 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/40 focus:border-[#d2a63d] outline-none cursor-pointer text-[#eaeef2]">
                            <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }} class="bg-[#010717]">To Do</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }} class="bg-[#010717]">In Progress</option>
                            <option value="review" {{ $task->status == 'review' ? 'selected' : '' }} class="bg-[#010717]">Review</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }} class="bg-[#010717]">Completed</option>
                        </select>
                    </div>

                    {{-- Completion Deadline --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Deadline Update</label>
                        <input type="date" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" 
                               class="w-full p-5 bg-[#010717] rounded-2xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none text-[#eaeef2]" style="color-scheme: dark;">
                    </div>

                    {{-- Mission Brief --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase text-[#eaeef2]/60 italic ml-2">Briefing Update</label>
                        <textarea name="description" rows="5" 
                                  class="w-full p-5 bg-[#010717] rounded-3xl font-bold border border-[#d2a63d]/30 focus:border-[#d2a63d] outline-none transition-all text-[#eaeef2]" 
                                  placeholder="Provide updated tactical instructions...">{{ old('description', $task->description) }}</textarea>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-12">
                    <button type="submit" class="w-full bg-[#d2a63d] text-[#010717] py-6 rounded-3xl font-black uppercase italic shadow-[0_0_20px_rgba(210,166,61,0.3)] hover:bg-[#b8860b] transition-all flex items-center justify-center gap-4 group">
                        Sync Intel Update 
                        <i class="fas fa-sync group-hover:rotate-180 transition-transform duration-500"></i>
                    </button>
                </div>
            </form>
        </div>
        
        {{-- Footer Branding --}}
        <div class="mt-8 text-center">
            <p class="text-[9px] font-black text-[#eaeef2]/30 uppercase tracking-[0.5em] italic">
                Authorized Admin Session | Encryption: Active
            </p>
        </div>
    </div>
</div>

<style>
    select option { background-color: #010717 !important; color: #eaeef2 !important; }
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.7; cursor: pointer; }
</style>
@endsection