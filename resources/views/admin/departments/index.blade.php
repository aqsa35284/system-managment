@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase italic tracking-tighter">System <span class="text-indigo-600">Nodes</span></h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Department Directory</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] shadow-lg shadow-indigo-100 hover:-translate-y-1 transition-all">
            <i class="fas fa-plus mr-2"></i> Create New Node
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400">Node Name</th>
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400">Code ID</th>
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($departments as $dept)
                <tr class="hover:bg-slate-50/30 transition-all">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 font-black">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <span class="font-bold text-slate-700 uppercase text-sm">{{ $dept->name }}</span>
                        </div>
                    </td>
                    <td class="p-6 text-xs font-mono font-bold text-slate-400 uppercase">
                        {{ $dept->department_code ?? 'N/A' }}
                    </td>
                    <td class="p-6 text-right space-x-3">
                        <a href="{{ route('admin.departments.edit', $dept->id) }}" class="text-slate-300 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-300 hover:text-rose-500 transition-colors" onclick="return confirm('Purge this node from system?')">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection