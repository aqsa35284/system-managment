@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-10 bg-indigo-600 text-white">
            <h2 class="text-3xl font-black uppercase italic tracking-tighter">Modify <span class="opacity-50">Project</span></h2>
            <p class="text-[10px] font-bold uppercase tracking-[0.3em] mt-2 italic">Mission: {{ $project->name }}</p>
        </div>

        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" class="p-10 space-y-6">
            @csrf 
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Project Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Project Name</label>
                    <input type="text" name="name" value="{{ $project->name }}" class="form-input w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none text-slate-800">
                </div>
                
                {{-- Client Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Client Name</label>
                    <input type="text" name="client_name" value="{{ $project->client_name }}" class="form-input w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none text-slate-800">
                </div>
                
                {{-- Start Date --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '' }}" class="form-input w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none text-slate-800">
                </div>

                {{-- Deadline --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Deadline</label>
                    <input type="date" name="deadline" value="{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '' }}" class="form-input w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none text-slate-800">
                </div>

                {{-- Budget --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-emerald-500 italic ml-2">Project Budget (PKR)</label>
                    <input type="number" name="budget" value="{{ $project->budget }}" class="form-input w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-emerald-500 outline-none text-slate-800">
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-indigo-600 italic ml-2">Project Status</label>
                    <select name="status" class="form-select w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none cursor-pointer text-slate-800">
                        <option value="pending" {{ $project->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                {{-- Team Assignment (Multi-select) --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Assign Team Members</label>
                    <select name="employees[]" multiple class="form-select ...">
    @foreach($employees as $user)
        <option value="{{ $user->id }}" 
            {{-- Yahan 'users' ki jagah 'employees' likhein --}}
            @if($project->employees && $project->employees->contains($user->id)) selected @endif>
            {{ $user->name }} - {{ $user->designation }}
        </option>
    @endforeach
</select>
                    <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold italic">Hold Ctrl to select multiple nodes</p>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Project Description</label>
                    <textarea name="description" rows="4" class="form-input w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none text-slate-800">{{ $project->description }}</textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-6 rounded-3xl font-black uppercase italic shadow-2xl hover:bg-indigo-700 transition transform hover:-translate-y-1 mt-6">
                Update Project Config <i class="fas fa-save ml-2"></i>
            </button>
        </form>
    </div>
</div>

<style>
    .form-input, .form-select {
        color: #1e293b !important;
        -webkit-text-fill-color: #1e293b !important;
    }
</style>
@endsection