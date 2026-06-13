<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskForward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ⭐ Auth facade lazmi hai

class TaskController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // INDEX — All Tasks (Filtered by Company)
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        // ⭐ FIX: Sirf apni company ke tasks dikhayega
        $tasks = Task::where('company_id', Auth::user()->company_id)
            ->with(['project', 'user', 'forwards.fromUser', 'forwards.toUser'])
            ->latest()
            ->get();

        return view('admin.tasks.index', compact('tasks'));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE FORM (Filtered by Company)
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        // ⭐ FIX: Sirf apni company ke projects aur employees dikhayega
        $projects  = Project::where('company_id', Auth::user()->company_id)->get();
        
        $employees = User::where('company_id', Auth::user()->company_id)
                         ->where('role', 'employee')
                         ->orderBy('name')
                         ->get();

        return view('admin.tasks.create', compact('projects', 'employees'));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE NEW TASK
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'user_id'    => 'required|exists:users,id',
            'priority'   => 'required|in:low,medium,high,urgent',
            'due_date'   => 'nullable|date',
        ]);

        Task::create([
            'title'                 => $request->title,
            'description'           => $request->description,
            'project_id'            => $request->project_id,
            'user_id'               => $request->user_id,
            'company_id'            => Auth::user()->company_id, // ⭐ FIX: Company ID save ho rahi hai
            'priority'              => $request->priority,
            'due_date'              => $request->due_date,
            'status'                => 'to_do',
            'completion_percentage' => 0,
            'completion_stage'      => 'not_started',
            'days_worked'           => 0,
            'overdue_days'          => 0,
        ]);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deployed successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW SINGLE TASK 
    // ─────────────────────────────────────────────────────────────
    public function show($id)
    {
        // ⭐ FIX: Security check for company_id
        $task = Task::where('company_id', Auth::user()->company_id)
            ->with([
                'project',
                'user',
                'forwards.fromUser',
                'forwards.toUser',
                'employeeUpdates.user',
                'adminFeedbacks',
            ])->findOrFail($id);

        return view('admin.tasks.show', compact('task'));
    }

    // ─────────────────────────────────────────────────────────────
    // EDIT FORM
    // ─────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $task      = Task::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        $projects  = Project::where('company_id', Auth::user()->company_id)->get();
        $employees = User::where('company_id', Auth::user()->company_id)
                         ->where('role', 'employee')
                         ->orderBy('name')
                         ->get();

        return view('admin.tasks.edit', compact('task', 'projects', 'employees'));
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE TASK
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'user_id'    => 'required|exists:users,id',
            'priority'   => 'required|in:low,medium,high,urgent',
            'status'     => 'required|in:to_do,in_progress,review,completed',
            'due_date'   => 'nullable|date',
        ]);

        $task = Task::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $task->update([
            'title'       => $request->title,
            'description' => $request->description,
            'project_id'  => $request->project_id,
            'user_id'     => $request->user_id,
            'priority'    => $request->priority,
            'status'      => $request->status,
            'due_date'    => $request->due_date,
        ]);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE TASK
    // ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        Task::where('company_id', Auth::user()->company_id)->findOrFail($id)->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task purged from system.');
    }

    // ─────────────────────────────────────────────────────────────
    // FORWARD HISTORY 
    // ─────────────────────────────────────────────────────────────
    public function forwardHistory(Request $request)
    {
        $employees = User::where('company_id', Auth::user()->company_id)
                         ->where('role', 'employee')
                         ->orderBy('name')
                         ->get();

        // Join to task to ensure we only get forwards for THIS company's tasks
        $query = TaskForward::whereHas('task', function($q) {
                $q->where('company_id', Auth::user()->company_id);
            })
            ->with(['task', 'fromUser', 'toUser'])
            ->latest();

        if ($request->filled('user_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_user_id', $request->user_id)
                  ->orWhere('to_user_id', $request->user_id);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $forwards = $query->paginate(20)->withQueryString();

        return view('admin.tasks.forward-history', compact('forwards', 'employees'));
    }
}