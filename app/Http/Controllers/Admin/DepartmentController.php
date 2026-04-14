<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        // Saare departments database se le kar aao
        $departments = Department::all();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        // Data validate karein
        $request->validate([
            'name' => 'required|unique:departments,name|max:255',
            'department_code' => 'nullable|string|max:50',
        ]);

        // Database mein save karein
        Department::create([
            'name' => $request->name,
            'department_code' => $request->department_code,
        ]);

        return redirect()->route('admin.departments.index')
                         ->with('success', 'New Department node initialized!');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:departments,name,' . $id,
            'department_code' => 'nullable|string|max:50',
        ]);

        $department->update([
            'name' => $request->name,
            'department_code' => $request->department_code,
        ]);

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department configuration updated.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department node purged from system.');
    }
}