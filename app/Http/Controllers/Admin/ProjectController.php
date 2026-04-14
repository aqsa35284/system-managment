<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('employees')->latest()->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $employees = User::all(); 
        return view('admin.projects.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'status'    => 'required',
            'employees' => 'required|array', 
        ]);

        // FIX: 'employees' ko alag kar dein taaki create() query mein error na aaye
        $project = Project::create($request->except('employees'));

        // Pivot Table mein Employees attach karein
        if ($request->has('employees')) {
            $project->employees()->attach($request->employees);
        }

        return redirect()->route('admin.projects.index')->with('success', 'New Project Launched!');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $employees = User::all();
        // Load existing employees to show in edit form
        $project->load('employees');
        return view('admin.projects.edit', compact('project', 'employees'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'status'    => 'required',
            'employees' => 'required|array',
        ]);

        // FIX: update() mein bhi 'employees' ko ignore karein
        $project->update($request->except('employees'));

        // sync() purane links delete karke naye links banata hai
        if($request->has('employees')) {
            $project->employees()->sync($request->employees);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project Updated.');
    }

    public function destroy(Project $project)
    {
        // Pivot table records (project_user) cascade ki wajah se khud delete ho jayenge
        $project->delete(); 
        return redirect()->route('admin.projects.index')->with('success', 'Project Removed.');
    }
}