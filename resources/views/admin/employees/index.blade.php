@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 mb-1">Team Members</h1>
                <p class="text-slate-500">Manage your organization's personnel</p>
            </div>
            <a href="{{ route('admin.employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Member
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" placeholder="Search employees..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Designation</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($employees as $employee)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($employee->profile_image)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/'.$employee->profile_image) }}" alt="{{ $employee->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-medium">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ $employee->name }}</div>
                                        <div class="text-sm text-slate-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $employee->designation }}</div>
                                <div class="text-sm text-slate-500">Rs. {{ number_format($employee->salary ?? 0) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $employee->department->name ?? 'Unassigned' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($employee->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($employee->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-3">
                                    {{-- ⭐ ADDED: REVIEW BUTTON --}}
                                    <button onclick="openReviewModal({{ $employee->id }}, '{{ $employee->name }}')" 
                                            class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Submit Technical Review">
                                        <i class="fas fa-star-half-alt text-lg"></i>
                                    </button>

                                    <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-slate-400 hover:text-blue-600 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="Delete" onclick="return confirm('Are you sure you want to remove this employee?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ⭐ MODAL SECTION --}}
<div id="reviewModal" class="fixed inset-0 z-[10000] hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white max-w-lg w-full rounded-[2.5rem] border border-emerald-500/30 shadow-2xl overflow-hidden shadow-emerald-500/10">
        <div class="p-6 bg-emerald-900 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase italic tracking-widest text-emerald-400">
                Transmit Review: <span id="modal_user_name" class="text-white"></span>
            </h3>
            <button onclick="closeReviewModal()" class="text-emerald-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.feedback.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="employee_id" id="modal_user_id">
            <input type="hidden" name="task_id" value="0">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 ml-1">Performance (1-10)</label>
                    <input type="number" name="rating_score" min="1" max="10" required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 ml-1">Efficiency Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        <option value="excellent">Excellent Execution</option>
                        <option value="good">Standard Met</option>
                        <option value="needs_improvement">Needs Optimization</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 ml-1">Technical Review Note</label>
                <textarea name="review_note" rows="4" required
                          class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none transition-all"
                          placeholder="Log official analysis..."></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-900 hover:bg-black text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all shadow-lg active:scale-95">
                Confirm Transmission
            </button>
        </form>
    </div>
</div>

<script>
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
    /* Taake modal smooth lage */
    #reviewModal { transition: opacity 0.3s ease-in-out; }
</style>
@endsection