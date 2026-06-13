<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // 1. My Tasks List
    public function index()
    {
        // ⭐ FIX: Added company_id isolation + Removed unused Request import
        $tasks = Task::with('project')
            ->where('company_id', Auth::user()->company_id) // Tenant Isolation
            ->where('user_id', Auth::id())                 // Ownership Lock
            ->latest()
            ->get();
            
        return view('employee.tasks.index', compact('tasks'));
    }

    // 2. Single Task Detail
    public function show($id)
    {
        // ⭐ FIX: Security - Pehle company verify karein, phir task fetch karein
        $task = Task::where('company_id', Auth::user()->company_id)
            ->with('project')
            ->findOrFail($id);

        // Security: Ownership Check
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this operational asset.');
        }

        return view('employee.tasks.show', compact('task'));
    }
}