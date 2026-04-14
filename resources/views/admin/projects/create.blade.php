@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-slate-800 mb-2">Create New Project</h1>
            <p class="text-slate-500">Define project parameters and assign team members</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-project-diagram mr-3"></i>
                    Project Details
                </h2>
            </div>

            <form action="{{ route('admin.projects.store') }}" method="POST" class="p-8">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Project Name</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="e.g. E-Commerce Redesign" required>
                        </div>
                        <div>
                            <label for="client_name" class="block text-sm font-medium text-slate-700 mb-2">Client Name</label>
                            <input type="text" id="client_name" name="client_name" class="form-input" placeholder="e.g. Royal Family Mart">
                        </div>
                    </div>
                </div>

                <!-- Timeline Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                        Timeline
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-d') }}" class="form-input">
                        </div>
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-slate-700 mb-2">Deadline</label>
                            <input type="date" id="deadline" name="deadline" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Project Details Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-tasks mr-2 text-blue-600"></i>
                        Project Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Project Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="active" selected>Active</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label for="budget" class="block text-sm font-medium text-slate-700 mb-2">Budget (PKR)</label>
                            <input type="number" id="budget" name="budget" class="form-input" placeholder="e.g. 150000">
                        </div>
                    </div>
                </div>

                <!-- Team Assignment Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-users mr-2 text-blue-600"></i>
                        Team Assignment
                    </h3>
                    <div class="mb-2">
                        <label for="employees" class="block text-sm font-medium text-slate-700 mb-2">Assign Team Members</label>
                        <div class="relative">
                            <select id="employees" name="employees[]" multiple class="form-multiselect" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->designation }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Hold Ctrl (or Cmd) to select multiple members</p>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                        Project Description
                    </h3>
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Mission Brief</label>
                        <textarea id="description" name="description" rows="4" class="form-textarea" placeholder="Describe the project goals, objectives, and requirements..."></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-rocket mr-2"></i>
                        Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Override any conflicting CSS */
    .form-input, 
    .form-select, 
    .form-textarea, 
    .form-multiselect {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 1rem !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        transition: all 0.2s;
        -webkit-text-fill-color: #1e293b !important; /* For iOS/Safari */
    }
    
    /* Ensure input text is visible when typing */
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus,
    .form-multiselect:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background-color: #ffffff !important;
        color: #1e293b !important;
    }
    
    /* Style for when input has value */
    .form-input:not(:placeholder-shown),
    .form-textarea:not(:placeholder-shown) {
        color: #1e293b !important;
        background-color: #ffffff !important;
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
        color: #1e293b !important;
        background-color: #ffffff !important;
        font-size: 1rem !important;
        padding: 8px;
    }
    
    /* Selected options in multiselect */
    .form-multiselect option:checked {
        background: #eff6ff !important;
        color: #1e40af !important;
    }
    
    /* Placeholder text */
    ::placeholder {
        color: #94a3b8 !important;
        opacity: 1;
    }
    
    /* For date inputs */
    input[type="date"] {
        color: #1e293b !important;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(0.5);
    }
    
    /* For number inputs */
    input[type="number"] {
        color: #1e293b !important;
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
                        options[i].style.backgroundColor = '#eff6ff';
                        options[i].style.color = '#1e40af';
                    } else {
                        options[i].style.backgroundColor = '#ffffff';
                        options[i].style.color = '#1e293b';
                    }
                }
            });
        }
        
        // Debug: Log when inputs are focused
        const allInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
        allInputs.forEach(input => {
            input.addEventListener('focus', function() {
                console.log('Input focused:', this.id || this.name);
                this.style.color = '#1e293b';
                this.style.backgroundColor = '#ffffff';
            });
            
            input.addEventListener('input', function() {
                console.log('Input value changed:', this.value);
            });
        });
    });
</script>
@endsection