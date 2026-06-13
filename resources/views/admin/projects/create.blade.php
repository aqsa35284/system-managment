@extends('layouts.admin')

@section('content')
{{-- ⭐ Favicon Inject --}}
<link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">

<div class="min-h-screen bg-[#010717] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex items-center justify-between mb-10">
            <a href="{{ route('admin.projects.index') }}" class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#030712] border border-[#d2a63d]/30 text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] transition-all shadow-[0_0_15px_rgba(210,166,61,0.15)] group" title="Go Back">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            
            <div class="text-center flex-1">
                <h1 class="text-4xl font-bold text-[#d2a63d] mb-2">Create New Project</h1>
                <p class="text-[#eaeef2]/70">Define project parameters and assign team members</p>
            </div>
            
            <div class="w-12"></div> </div>

        <div class="bg-[#030712] shadow-2xl rounded-2xl overflow-hidden border border-[#d2a63d]/30">
            <div class="bg-[#d2a63d]/10 border-b border-[#d2a63d]/30 px-8 py-6">
                <h2 class="text-2xl font-bold text-[#d2a63d] flex items-center">
                    <i class="fas fa-project-diagram mr-3"></i>
                    Project Details
                </h2>
            </div>

            <form action="{{ route('admin.projects.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/20 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-[#d2a63d]"></i>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Project Name</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="e.g. E-Commerce Redesign" required>
                        </div>
                        <div>
                            <label for="client_name" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Client Name</label>
                            <input type="text" id="client_name" name="client_name" class="form-input" placeholder="e.g. Royal Family Mart">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/20 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-[#d2a63d]"></i>
                        Timeline
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-d') }}" class="form-input">
                        </div>
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Deadline</label>
                            <input type="date" id="deadline" name="deadline" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/20 flex items-center">
                        <i class="fas fa-tasks mr-2 text-[#d2a63d]"></i>
                        Project Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Project Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="active" selected>Active</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="budget" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Budget (PKR)</label>
                            <input type="number" id="budget" name="budget" class="form-input" placeholder="e.g. 150000">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/20 flex items-center">
                        <i class="fas fa-users mr-2 text-[#d2a63d]"></i>
                        Team Assignment
                    </h3>
                    <div class="mb-2">
                        <label for="employees" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Assign Team Members</label>
                        <div class="relative">
                            <select id="employees" name="employees[]" multiple class="form-multiselect" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->designation }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-[#eaeef2]/50 mt-1">Hold Ctrl (or Cmd) to select multiple members</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/20 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-[#d2a63d]"></i>
                        Project Description
                    </h3>
                    <div>
                        <label for="description" class="block text-sm font-medium text-[#eaeef2]/80 mb-2">Mission Brief</label>
                        <textarea id="description" name="description" rows="4" class="form-textarea" placeholder="Describe the project goals, objectives, and requirements..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-[#d2a63d] hover:bg-[#b8860b] text-[#010717] font-black uppercase tracking-widest rounded-lg shadow-[0_0_15px_rgba(210,166,61,0.3)] transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-rocket mr-2"></i>
                        Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Override CSS for Luxury Dark Theme */
    .form-input, 
    .form-select, 
    .form-textarea, 
    .form-multiselect {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid rgba(210, 166, 61, 0.3);
        border-radius: 0.5rem;
        font-size: 1rem !important;
        color: #eaeef2 !important;
        background-color: #010717 !important;
        transition: all 0.2s;
        -webkit-text-fill-color: #eaeef2 !important; /* For iOS/Safari */
    }
    
    /* Ensure input text is visible when typing */
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus,
    .form-multiselect:focus {
        outline: none;
        border-color: #d2a63d;
        box-shadow: 0 0 0 3px rgba(210, 166, 61, 0.15);
        background-color: #030712 !important;
        color: #eaeef2 !important;
    }
    
    /* Style for when input has value */
    .form-input:not(:placeholder-shown),
    .form-textarea:not(:placeholder-shown) {
        color: #eaeef2 !important;
        background-color: #030712 !important;
    }
    
    /* Special handling for multiselect */
    .form-multiselect {
        min-height: 150px;
    }
    
    /* Special handling for textarea */
    .form-textarea {
        min-height: 120px;
        resize: vertical;
    }
    
    /* Select dropdown options */
    select.form-select option,
    .form-multiselect option {
        color: #eaeef2 !important;
        background-color: #010717 !important;
        font-size: 1rem !important;
        padding: 8px;
    }
    
    /* Selected options in multiselect */
    .form-multiselect option:checked {
        background: rgba(210, 166, 61, 0.2) !important;
        color: #d2a63d !important;
    }
    
    /* Placeholder text */
    ::placeholder {
        color: rgba(234, 238, 242, 0.4) !important;
        opacity: 1;
    }
    
    /* For date inputs */
    input[type="date"] {
        color: #eaeef2 !important;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(1);
        opacity: 0.7;
    }
    
    /* For number inputs */
    input[type="number"] {
        color: #eaeef2 !important;
    }
    
    /* Ensure text is visible during typing */
    input, textarea, select {
        opacity: 1 !important;
        visibility: visible !important;
    }
</style>

<script>
    // Enhance multi-select with better UI
    document.addEventListener('DOMContentLoaded', function() {
        const multiSelect = document.getElementById('employees');
        
        if (multiSelect) {
            // Add visual feedback when options are selected
            multiSelect.addEventListener('change', function() {
                const options = this.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].selected) {
                        options[i].style.backgroundColor = 'rgba(210, 166, 61, 0.2)'; /* Gold tinted BG */
                        options[i].style.color = '#d2a63d'; /* Gold Text */
                    } else {
                        options[i].style.backgroundColor = '#010717'; /* Dark BG */
                        options[i].style.color = '#eaeef2'; /* Off-white Text */
                    }
                }
            });
        }
        
        // Debug: Log when inputs are focused
        const allInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
        allInputs.forEach(input => {
            input.addEventListener('focus', function() {
                console.log('Input focused:', this.id || this.name);
                this.style.color = '#eaeef2';
                this.style.backgroundColor = '#030712';
            });
            
            input.addEventListener('input', function() {
                console.log('Input value changed:', this.value);
            });
        });
    });
</script>
@endsection