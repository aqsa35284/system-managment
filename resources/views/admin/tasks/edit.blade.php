@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen flex justify-center items-start">
    <div class="max-w-4xl w-full">
        
        {{-- 🛰️ Header Section --}}
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">
                    Modify <span class="text-indigo-600">Objective</span>
                </h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 italic">
                    Update Mission Parameters for Node: {{ $task->id }}
                </p>
            </div>
            <a href="{{ route('admin.tasks.index') }}" class="text-slate-400 hover:text-slate-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> <span class="text-[10px] font-black uppercase italic">Back to Board</span>
            </a>
        </div>

        {{-- 🛠️ Edit Form Card --}}
        <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
            <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" class="p-12">
                @csrf 
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    {{-- Objective Title --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Objective Title</label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}" 
                               class="w-full p-5 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none transition-all" required>
                    </div>

                    {{-- Target Project (Read-only vibe but selectable) --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Target Project</label>
                        <select name="project_id" class="w-full p-5 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none cursor-pointer">
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $task->project_id == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fleet Member --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-blue-600 italic ml-2">Assigned Operator</label>
                        <select name="user_id" class="w-full p-5 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none cursor-pointer">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $task->user_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority Level --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Priority Level</label>
                        <select name="priority" class="w-full p-5 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-rose-500 outline-none">
                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $task->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    {{-- Objective Status (Crucial for Enum Sync) --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-indigo-600 italic ml-2">Mission Status</label>
                        <select name="status" class="w-full p-5 bg-indigo-50/50 rounded-2xl font-bold border-2 border-indigo-200 focus:border-indigo-500 outline-none cursor-pointer">
                            <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    {{-- Completion Deadline --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Deadline Update</label>
                        <input type="date" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" 
                               class="w-full p-5 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none">
                    </div>

                    {{-- Mission Brief (Details) --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Briefing Update</label>
                        <textarea name="description" rows="5" 
                                  class="w-full p-5 bg-slate-50 rounded-3xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none transition-all" 
                                  placeholder="Provide updated tactical instructions...">{{ old('description', $task->description) }}</textarea>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-12">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-3xl font-black uppercase italic shadow-xl shadow-indigo-100 hover:bg-slate-900 transition-all flex items-center justify-center gap-4 group">
                        Sync Intel Update 
                        <i class="fas fa-sync group-hover:rotate-180 transition-transform duration-500"></i>
                    </button>
                </div>
            </form>
        </div>
        
        {{-- Footer Branding --}}
        <div class="mt-8 text-center">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em] italic">
                Authorized Admin Session | Encryption: Active
            </p>
        </div>
    </div>
</div>
@endsection