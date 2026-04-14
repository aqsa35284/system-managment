@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-xl bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-8 bg-indigo-600 text-white">
            <h2 class="text-2xl font-black uppercase italic tracking-tighter">Initialize <span class="opacity-60">Department</span></h2>
            <p class="text-[10px] font-bold uppercase tracking-widest mt-1">Setup New Organizational Node</p>
        </div>

        <form action="{{ route('admin.departments.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Department Name</label>
                <input type="text" name="name" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none transition-all" placeholder="e.g. Information Technology" required>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Node Code (Optional)</label>
                <input type="text" name="department_code" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-indigo-500 outline-none transition-all" placeholder="e.g. IT-01">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase italic shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition transform hover:-translate-y-1">
                    Establish Node <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection