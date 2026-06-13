@extends('layouts.admin')

@section('content')

{{-- ⭐ Favicon --}}
<link rel="icon" type="image/png" href="{{ asset('storage/logo/Favicon.png') }}">

<div class="min-h-screen bg-[#010717] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        {{-- ⭐ 1. BACK BUTTON (Jo aapne manga tha) --}}
        <div class="flex justify-start mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] text-[10px] font-black uppercase tracking-widest transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#eaeef2] mb-1">Team Members</h1>
                <p class="text-[#eaeef2]/50">Manage your organization's personnel</p>
            </div>
            
            {{-- ⭐ 2. ADD NEW MEMBER BUTTON --}}
            <a href="{{ route('admin.employees.create') }}" class="bg-[#d2a63d] hover:bg-[#b88e2c] text-[#010717] px-6 py-3 rounded-xl font-bold shadow-[0_0_15px_rgba(210,166,61,0.3)] transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Member
            </a>
        </div>

        {{-- Filters Section --}}
        <div class="bg-[#0a1225] border border-[#d2a63d]/20 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-[#d2a63d]/50"></i>
                        </div>
                        <input type="text" placeholder="Search employees..." class="w-full pl-11 pr-4 py-3 bg-[#010717] border border-[#d2a63d]/20 rounded-xl focus:ring-2 focus:ring-[#d2a63d]/50 focus:border-[#d2a63d]/50 text-[#eaeef2] placeholder-[#eaeef2]/30 outline-none transition-all">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-3 bg-[#010717] border border-[#d2a63d]/20 rounded-xl focus:ring-2 focus:ring-[#d2a63d]/50 focus:border-[#d2a63d]/50 text-[#eaeef2] outline-none transition-all appearance-none cursor-pointer">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-[#0a1225] border border-[#d2a63d]/20 rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#010717] border-b border-[#d2a63d]/20">
                        <tr>
                            <th class="px-6 py-5 text-left text-xs font-bold text-[#d2a63d] uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-[#d2a63d] uppercase tracking-wider">Designation</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-[#d2a63d] uppercase tracking-wider">Department</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-[#d2a63d] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-5 text-right text-xs font-bold text-[#d2a63d] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2a63d]/10">
                        @foreach($employees as $employee)
                        <tr class="hover:bg-[#d2a63d]/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($employee->profile_image)
                                            <img class="h-10 w-10 rounded-full border border-[#d2a63d]/30 object-cover" src="{{ asset('storage/'.$employee->profile_image) }}" alt="{{ $employee->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-[#010717] border border-[#d2a63d]/30 flex items-center justify-center">
                                                <span class="text-[#d2a63d] font-bold">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-[#eaeef2]">{{ $employee->name }}</div>
                                        <div class="text-xs text-[#eaeef2]/50">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#eaeef2]">{{ $employee->designation }}</div>
                                <div class="text-xs text-[#d2a63d]">Rs. {{ number_format($employee->salary ?? 0) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#eaeef2]/80">{{ $employee->department->name ?? 'Unassigned' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-[10px] font-bold uppercase tracking-wider rounded-full border {{ ($employee->status ?? 'active') === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20' }}">
                                    {{ ucfirst($employee->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    
                                    {{-- ⭐ 3. REVIEW BUTTON (Star) --}}
                                    <button onclick="openReviewModal({{ $employee->id }}, '{{ $employee->name }}')" 
                                            class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white border border-emerald-500/30 flex items-center justify-center transition-all" title="Submit Technical Review">
                                        <i class="fas fa-star-half-alt text-xs"></i>
                                    </button>

                                    {{-- ⭐ 4. VIEW DETAILS BUTTON (Eye) --}}
                                    <a href="{{ route('admin.employees.show', $employee->id) }}" class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white border border-blue-500/30 flex items-center justify-center transition-all" title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    {{-- ⭐ 5. EDIT BUTTON (Pencil) --}}
                                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="w-8 h-8 rounded-lg bg-[#d2a63d]/10 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] border border-[#d2a63d]/30 flex items-center justify-center transition-all" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    {{-- ⭐ 6. DELETE BUTTON (Trash) --}}
                                    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/30 flex items-center justify-center transition-all" title="Delete" onclick="return confirm('Are you sure you want to remove this employee?')">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($employees->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-[#eaeef2]/40 text-sm">
                                No personnel registered in your directory yet.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ⭐ MODAL SECTION --}}
<div id="reviewModal" class="fixed inset-0 z-[10000] hidden bg-[#010717]/90 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0a1225] max-w-lg w-full rounded-2xl border border-[#d2a63d]/30 shadow-2xl overflow-hidden">
        <div class="p-6 bg-[#010717] border-b border-[#d2a63d]/20 flex justify-between items-center">
            <h3 class="text-sm font-bold text-[#d2a63d] flex items-center">
                <i class="fas fa-broadcast-tower mr-2"></i> Transmit Review: <span id="modal_user_name" class="text-[#eaeef2] ml-2"></span>
            </h3>
            <button onclick="closeReviewModal()" class="text-[#eaeef2]/50 hover:text-[#d2a63d] transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.feedback.store') }}" method="POST" class="p-8 space-y-6" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Transmitting...';">
            @csrf
            <input type="hidden" name="employee_id" id="modal_user_id">
            <input type="hidden" name="task_id" value="0">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/50 block mb-2 ml-1">Performance (1-10)</label>
                    <input type="number" name="rating_score" min="1" max="10" required 
                           class="w-full bg-[#010717] border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm font-bold text-[#eaeef2] focus:ring-2 focus:ring-[#d2a63d]/50 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-[#eaeef2]/50 block mb-2 ml-1">Efficiency Status</label>
                    <select name="status" class="w-full bg-[#010717] border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm font-bold text-[#eaeef2] focus:ring-2 focus:ring-[#d2a63d]/50 outline-none transition-all appearance-none">
                        <option value="excellent">Excellent Execution</option>
                        <option value="good">Standard Met</option>
                        <option value="needs_improvement">Needs Optimization</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-[#eaeef2]/50 block mb-2 ml-1">Technical Review Note</label>
                <textarea name="review_note" rows="4" required
                          class="w-full bg-[#010717] border border-[#d2a63d]/20 rounded-xl px-4 py-3 text-sm text-[#eaeef2] placeholder-[#eaeef2]/20 focus:ring-2 focus:ring-[#d2a63d]/50 outline-none transition-all"
                          placeholder="Log official analysis..."></textarea>
            </div>

            <button type="submit" class="w-full bg-[#d2a63d] hover:bg-[#d2a63d]/80 text-[#010717] py-4 rounded-xl font-bold uppercase text-xs tracking-widest transition-all shadow-[0_0_15px_rgba(210,166,61,0.2)]">
                Confirm Transmission
            </button>
        </form>
    </div>
</div>

{{-- ⭐ SweetAlert2 & Popup Logic --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: "{{ session('success') }}",
                background: '#0a1225',
                color: '#eaeef2',
                iconColor: '#d2a63d'
            });
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: "{{ session('error') }}",
                background: '#0a1225',
                color: '#eaeef2'
            });
        });
    @endif

    function openReviewModal(id, name) {
        document.getElementById('modal_user_id').value = id;
        document.getElementById('modal_user_name').innerText = name;
        document.getElementById('reviewModal').classList.remove('hidden');
    }
    
    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }
</script>

<style>
    #reviewModal { transition: opacity 0.3s ease-in-out; }
</style>
@endsection