<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ⭐ Auth facade lazmi hai

class ProjectController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // INDEX — All Projects
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        // ⭐ FIX: Sirf login admin ki company ke projects dikhayega
        $projects = Project::where('company_id', Auth::user()->company_id)
                           ->with('employees')
                           ->latest()
                           ->get();
                           
        return view('admin.projects.index', compact('projects'));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE FORM
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        // ⭐ FIX: Sirf apni company ke 'employees' ko list karega
        $employees = User::where('company_id', Auth::user()->company_id)
                         ->where('role', 'employee')
                         ->orderBy('name')
                         ->get(); 
                         
        return view('admin.projects.create', compact('employees'));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE NEW PROJECT
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'status'    => 'required',
            'employees' => 'required|array', 
        ]);

        $data = $request->except('employees');
        $data['company_id'] = Auth::user()->company_id; // ⭐ FIX: Company ID lazmi assign hogi

        // Project create karein
        $project = Project::create($data);

        // Pivot Table mein Employees attach karein
        if ($request->has('employees')) {
            $project->employees()->attach($request->employees);
        }

        return redirect()->route('admin.projects.index')->with('success', 'New Project Launched!');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW SINGLE PROJECT
    // ─────────────────────────────────────────────────────────────
    public function show($id)
    {
        // ⭐ FIX: Security check ke koi aur admin isey na dekh sake
        $project = Project::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        return view('admin.projects.show', compact('project'));
    }

    // ─────────────────────────────────────────────────────────────
    // EDIT FORM
    // ─────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $project = Project::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        // ⭐ FIX: Sirf apni company ke employees edit form mein dikhein
        $employees = User::where('company_id', Auth::user()->company_id)
                         ->where('role', 'employee')
                         ->orderBy('name')
                         ->get();
                         
        // Load existing employees to show in edit form
        $project->load('employees');
        
        return view('admin.projects.edit', compact('project', 'employees'));
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE PROJECT
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'status'    => 'required',
            'employees' => 'required|array',
        ]);

        // ⭐ FIX: Security check for update
        $project = Project::where('company_id', Auth::user()->company_id)->findOrFail($id);

        // Update basic info
        $project->update($request->except('employees'));

        // sync() purane links delete karke naye links banata hai
        if($request->has('employees')) {
            $project->employees()->sync($request->employees);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project Updated.');
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE PROJECT
    // ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        // ⭐ FIX: Security check before deletion
        $project = Project::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        // Pivot table records (project_user) cascade ki wajah se khud delete ho jayenge
        $project->delete(); 
        
        return redirect()->route('admin.projects.index')->with('success', 'Project Removed.');
    }
}