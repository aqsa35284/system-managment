<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // 1. Projects ki list (Isolated by Company)
    public function index()
    {
        // ⭐ FIX: Sirf apni company ke projects fetch honge
        $projects = Auth::user()->projects()
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->get();

        return view('employee.project.index', compact('projects'));
    }

    // 2. Single project detail (Isolated)
    public function show($id)
    {
        // ⭐ FIX: Pehle Company Isolation check, phir Ownership check
        $project = Project::where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        // Security: Check if employee is assigned to this project
        if (!$project->employees->contains(Auth::id())) {
            abort(403, 'Unauthorized access to this operational asset.');
        }

        // Sirf is project ke wo tasks jo is user ko assign hain
        $myTasks = $project->tasks()
            ->where('user_id', Auth::id())
            ->where('company_id', Auth::user()->company_id)
            ->latest()
            ->get();

        return view('employee.project.show', compact('project', 'myTasks'));
    }
}