@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen flex justify-center items-start">
    <div class="max-w-4xl w-full">
        <div class="mb-10">
            <h2 class="text-3xl font-bold text-slate-800 mb-2">Create New Task</h2>
            <p class="text-slate-500">Assign a new objective to your team members</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-rocket text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Task Details</h3>
                        <p class="text-blue-100 text-sm">Fill in the information below to create a new task</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.tasks.store') }}" method="POST" class="p-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Task Title</label>
                        <div class="relative">
                            <input type="text" name="title" class="w-full p-4 pr-12 bg-white border-2 border-slate-200 rounded-xl font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="e.g. Logic Synchronization" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-heading text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Project Mission</label>
                        <div class="relative">
                            <select name="project_id" class="w-full p-4 pr-12 bg-white border-2 border-slate-200 rounded-xl font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none appearance-none transition-all" required>
                                <option value="" disabled selected>Select Mission</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Assigned To</label>
                        <div class="relative">
                            <select name="user_id" class="w-full p-4 pr-12 bg-white border-2 border-slate-200 rounded-xl font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none appearance-none transition-all" required>
                                <option value="" disabled selected>Assign to team member</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-user text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Priority Level</label>
                        <div class="relative">
                            <select name="priority" class="w-full p-4 pr-12 bg-white border-2 border-slate-200 rounded-xl font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none appearance-none transition-all">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-flag text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Deadline</label>
                        <div class="relative">
                            <input type="date" name="due_date" class="w-full p-4 pr-12 bg-white border-2 border-slate-200 rounded-xl font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-calendar text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Task Description</label>
                        <div class="relative">
                            <textarea name="description" rows="4" class="w-full p-4 bg-white border-2 border-slate-200 rounded-xl font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none resize-none transition-all" placeholder="Provide detailed information about the task..."></textarea>
                            <div class="absolute bottom-4 right-4 text-slate-400 text-sm">
                                <span id="char-count">0</span> / 500
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        The assigned team member will receive a notification about this task. You can track the progress from the dashboard.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('admin.tasks.index') }}" class="flex-1 bg-slate-100 text-slate-700 py-4 rounded-xl font-medium hover:bg-slate-200 transition-all text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-4 rounded-xl font-medium hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Create Task <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Character counter for description
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        if (this.value.length > 500) {
            this.value = this.value.substring(0, 500);
            charCount.textContent = 500;
        }
    });

    // Form submission feedback
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating Task...';
        submitBtn.disabled = true;
    });
</script>
@endsection