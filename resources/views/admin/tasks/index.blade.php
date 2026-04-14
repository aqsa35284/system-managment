@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 mb-2">Task Board</h2>
            <p class="text-slate-500">Manage and track all team tasks</p>
        </div>
        <a href="{{ route('admin.tasks.create') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl font-medium shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Create Task
        </a>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" id="search-input" placeholder="Search tasks..." class="w-full pl-10 pr-4 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
            </div>
            <div class="flex gap-2">
                <select id="status-filter" class="px-4 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    <option value="">All Status</option>
                    <option value="to_do">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="completed">Completed</option>
                </select>
                <select id="priority-filter" class="px-4 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Task Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tasks as $task)
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-all task-card" data-status="{{ $task->status }}" data-priority="{{ $task->priority }}" data-title="{{ $task->title }}">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-slate-800 mb-1">{{ $task->title }}</h3>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($task->user->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="text-sm text-slate-500">{{ $task->user->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <a href="{{ route('admin.tasks.edit', $task->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="inline-block text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                        {{ $task->project->name ?? 'Standalone' }}
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-slate-600 line-clamp-2">{{ $task->description ?? 'No description provided.' }}</p>
                </div>

                <div class="flex items-center justify-between mb-4">
                    @php
                        $pColors = [
                            'low' => 'bg-slate-100 text-slate-600',
                            'medium' => 'bg-blue-100 text-blue-600',
                            'high' => 'bg-amber-100 text-amber-600',
                            'urgent' => 'bg-red-100 text-red-600'
                        ];
                        $pIcons = [
                            'low' => 'fa-arrow-down',
                            'medium' => 'fa-minus',
                            'high' => 'fa-arrow-up',
                            'urgent' => 'fa-exclamation-triangle'
                        ];
                    @endphp
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg {{ $pColors[$task->priority] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center">
                            <i class="fas {{ $pIcons[$task->priority] ?? 'fa-minus' }} text-xs"></i>
                        </div>
                        <span class="text-xs font-medium text-slate-500">{{ ucfirst($task->priority) }}</span>
                    </div>

                    @if($task->due_date)
                    <div class="flex items-center gap-1 text-xs text-slate-500">
                        <i class="far fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::parse($task->due_date)->format('M j') }}</span>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium text-slate-500">Progress</span>
                        <span class="text-xs font-medium text-slate-700">
                            @php
                                $progress = [
                                    'to_do' => 0,
                                    'in_progress' => 50,
                                    'review' => 75,
                                    'completed' => 100
                                ];
                            @endphp
                            {{ $progress[$task->status] ?? 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600" style="width: {{ $progress[$task->status] ?? 0 }}%"></div>
                    </div>
                </div>

                @php
                    $sColors = [
                        'to_do' => 'bg-slate-100 text-slate-600',
                        'in_progress' => 'bg-blue-100 text-blue-600',
                        'review' => 'bg-purple-100 text-purple-600',
                        'completed' => 'bg-green-100 text-green-600'
                    ];
                    $sIcons = [
                        'to_do' => 'fa-clock',
                        'in_progress' => 'fa-spinner',
                        'review' => 'fa-eye',
                        'completed' => 'fa-check-circle'
                    ];
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg {{ $sColors[$task->status] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center">
                            <i class="fas {{ $sIcons[$task->status] ?? 'fa-clock' }} text-xs"></i>
                        </div>
                        <span class="text-xs font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                    </div>
                    <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700">View Details</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-xl shadow-sm border border-slate-100 p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tasks text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">No tasks found</h3>
            <p class="text-slate-500 mb-6">Get started by creating your first task</p>
            <a href="{{ route('admin.tasks.create') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-600 transition-all">
                Create Task
            </a>
        </div>
        @endforelse
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('search-input').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const cards = document.querySelectorAll('.task-card');
        
        cards.forEach(card => {
            const title = card.getAttribute('data-title').toLowerCase();
            if (title.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Status filter
    document.getElementById('status-filter').addEventListener('change', function() {
        const status = this.value;
        const cards = document.querySelectorAll('.task-card');
        
        cards.forEach(card => {
            if (!status || card.getAttribute('data-status') === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Priority filter
    document.getElementById('priority-filter').addEventListener('change', function() {
        const priority = this.value;
        const cards = document.querySelectorAll('.task-card');
        
        cards.forEach(card => {
            if (!priority || card.getAttribute('data-priority') === priority) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection