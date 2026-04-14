<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
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
        
        // Default status agar form se nahi aa raha
        if (!$request->has('status')) {
            $data['status'] = 'to_do'; 
        }

        Task::create($data);

        return redirect()->route('admin.tasks.index')
                         ->with('success', 'Task Deployed Successfully!');
    }

    // ⭐ Yeh function missing tha jiski wajah se error aa rahi thi
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

        return redirect()->route('admin.tasks.index')->with('success', 'Task Updated Successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Task Deleted Successfully.');
    }
}