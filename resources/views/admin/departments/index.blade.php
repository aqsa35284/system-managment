@extends('layouts.admin')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8" style="background-color: #010717;">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-1" style="color: #eaeef2;">Organization Nodes</h1>
                <p style="color: #eaeef2; opacity: 0.55;">Manage your company's departments and structural nodes</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Back Button --}}
                <a href="{{ url()->previous() }}" class="flex items-center px-5 py-3 rounded-lg font-medium transition-all border" style="background-color: transparent; border-color: #d2a63d; color: #d2a63d;" onmouseover="this.style.backgroundColor='#d2a63d'; this.style.color='#010717';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#d2a63d';">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <a href="{{ route('admin.departments.create') }}" class="flex items-center px-6 py-3 rounded-lg font-medium shadow-md transition-all" style="background-color: #d2a63d; color: #010717;" onmouseover="this.style.opacity='0.88';" onmouseout="this.style.opacity='1';">
                    <i class="fas fa-plus mr-2"></i> Initialize New Node
                </a>
            </div>
        </div>

        <div class="rounded-xl shadow-sm overflow-hidden border" style="background-color: #0d1b2e; border-color: #d2a63d22;">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b" style="background-color: #010717; border-color: #d2a63d33;">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold uppercase tracking-wider" style="color: #d2a63d;">Node / Department Name</th>
                            <th class="px-8 py-5 text-left text-xs font-bold uppercase tracking-wider" style="color: #d2a63d;">Code ID</th>
                            <th class="px-8 py-5 text-right text-xs font-bold uppercase tracking-wider" style="color: #d2a63d;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="background-color: #0d1b2e; border-color: #d2a63d11;">
                        @forelse($departments as $dept)
                        <tr class="transition-colors" style="border-bottom: 1px solid #d2a63d11;" onmouseover="this.style.backgroundColor='#010717';" onmouseout="this.style.backgroundColor='transparent';">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black border" style="background-color: #d2a63d15; border-color: #d2a63d40; color: #d2a63d;">
                                        <i class="fas fa-sitemap"></i>
                                    </div>
                                    <span class="font-bold text-sm" style="color: #eaeef2;">{{ $dept->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span class="px-3 py-1 font-mono text-xs font-bold rounded-md border" style="background-color: #d2a63d10; color: #d2a63d; border-color: #d2a63d30;">
                                    {{ $dept->department_code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-4">
                                    <a href="{{ route('admin.departments.edit', $dept->id) }}" class="transition-colors" style="color: #eaeef2; opacity: 0.5;" title="Edit Configuration" onmouseover="this.style.color='#d2a63d'; this.style.opacity='1';" onmouseout="this.style.color='#eaeef2'; this.style.opacity='0.5';">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="transition-colors" style="background: none; border: none; color: #eaeef2; opacity: 0.5; cursor: pointer;" title="Purge Node" onmouseover="this.style.color='#ef4444'; this.style.opacity='1';" onmouseout="this.style.color='#eaeef2'; this.style.opacity='0.5';" onclick="return confirm('Are you sure you want to permanently delete this department node?')">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #d2a63d15;">
                                    <i class="fas fa-building text-2xl" style="color: #d2a63d; opacity: 0.5;"></i>
                                </div>
                                <p class="text-sm font-medium" style="color: #eaeef2; opacity: 0.45;">No departments found. Initialize a new node to get started.</p>
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
                background: '#0d1b2e', color: '#eaeef2'
            });
        });
    @endif
</script>
@endsection