<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskForward;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     *  Admin: Sab Forward History dekhe — Filters ke saath
     */
    public function forwardHistory(Request $request)
    {
        $query = TaskForward::with(['task', 'fromUser', 'toUser'])
                            ->latest();

        // Filter by employee
        if ($request->filled('user_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_user_id', $request->user_id)
                  ->orWhere('to_user_id', $request->user_id);
            });
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $forwards  = $query->paginate(20)->withQueryString();
        $employees = User::where('role', 'employee')->get();

        return view('admin.tasks.forward-history', compact('forwards', 'employees'));
    }

    public function index()
    {
        $tasks = Task::with(['project', 'user'])->latest()->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::all();
        $employees = User::where('role', 'employee')->get();
        return view('admin.tasks.create', compact('projects', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'project_id'  => 'required|exists:projects,id',
            'user_id'     => 'required|exists:users,id',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();

        if (!$request->has('status')) {
            $data['status'] = 'to_do';
        }

        Task::create($data);

        return redirect()->route('admin.tasks.index')
                         ->with('success', 'Task Deployed Successfully!');
    }

    /**
     *  show() method — Task detail page (admin ke liye)
     * Resource route ke liye zaroori hai
     */
    public function show(Task $task)
    {
        $task->load(['project', 'user', 'adminFeedbacks', 'employeeUpdates.user', 'forwards.fromUser', 'forwards.toUser']);
        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::all();
        $employees = User::where('role', 'employee')->get();

        return view('admin.tasks.edit', compact('task', 'projects', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'project_id'  => 'required|exists:projects,id',
            'user_id'     => 'required|exists:users,id',
            'priority'    => 'required',
            'status'      => 'required|in:to_do,in_progress,review,completed',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $task->update($request->all());

        return redirect()->route('admin.tasks.index')
                         ->with('success', 'Task Updated Successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')
                         ->with('success', 'Task Deleted Successfully.');
    }
}
