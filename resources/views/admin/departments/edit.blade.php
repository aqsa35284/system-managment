@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-slate-800 mb-2">Edit Department</h1>
            <p class="text-slate-500">Modify configuration for {{ $department->name }}</p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-100">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>
                    Update Node Configuration
                </h2>
            </div>

            <form action="{{ route('admin.departments.update', $department->id) }}" method="POST" class="p-8 space-y-6" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Saving Changes...';">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Department Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $department->name) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900 font-medium" placeholder="e.g. Information Technology" required>
                    @error('name') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Node Code ID <span class="text-xs text-slate-400 font-normal">(Optional)</span></label>
                    <input type="text" name="department_code" value="{{ old('department_code', $department->department_code) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900 font-medium font-mono" placeholder="e.g. IT-01">
                    @error('department_code') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-6 mt-8">
                    <a href="{{ route('admin.departments.index') }}" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition-colors mr-4">Cancel</a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection