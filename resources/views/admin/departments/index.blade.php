@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 mb-1">Organization Nodes</h1>
                <p class="text-slate-500">Manage your company's departments and structural nodes</p>
            </div>
            <a href="{{ route('admin.departments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center">
                <i class="fas fa-plus mr-2"></i> Initialize New Node
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Node / Department Name</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Code ID</th>
                            <th class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($departments as $dept)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 font-black border border-blue-100">
                                        <i class="fas fa-sitemap"></i>
                                    </div>
                                    <span class="font-bold text-slate-700 text-sm">{{ $dept->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 font-mono text-xs font-bold rounded-md border border-slate-200">
                                    {{ $dept->department_code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-4">
                                    <a href="{{ route('admin.departments.edit', $dept->id) }}" class="text-slate-400 hover:text-blue-600 transition-colors" title="Edit Configuration">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-500 transition-colors" title="Purge Node" onclick="return confirm('Are you sure you want to permanently delete this department node?')">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-building text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">No departments found. Initialize a new node to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ⭐ SweetAlert2 Popup Logic --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                toast: true, position: 'top-end', showConfirmButton: false,
                timer: 4000, timerProgressBar: true, icon: 'success',
                title: "{{ session('success') }}",
                background: '#ffffff', color: '#1e293b'
            });
        });
    @endif
</script>
@endsection