<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ⭐ Auth Facade add karna lazmi hai

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        // ⭐ FIX: Explicitly sir apni company ke departments call karein
        $departments = Department::where('company_id', Auth::user()->company_id)->get();
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
        $request->validate([
            'name' => [
                'required', 
                'max:255',
                // ⭐ UNIQUE RULE: Sirf is company_id ke andar name unique hona chahiye
                \Illuminate\Validation\Rule::unique('departments')->where(function ($query) {
                    return $query->where('company_id', Auth::user()->company_id);
                })
            ],
            'department_code' => 'nullable|string|max:50',
        ]);

        Department::create([
            'name'            => $request->name,
            'department_code' => $request->department_code,
            'company_id'      => Auth::user()->company_id, // ⭐ FIX: Explicitly assign
        ]);

        return redirect()->route('admin.departments.index')
                         ->with('success', 'New Department node initialized!');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit($id)
    {
        // ⭐ FIX: Check isolation before edit
        $department = Department::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, $id)
    {
        // ⭐ FIX: Check isolation before update
        $department = Department::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $request->validate([
            'name' => [
                'required', 
                'max:255',
                \Illuminate\Validation\Rule::unique('departments')->where(function ($query) {
                    return $query->where('company_id', Auth::user()->company_id);
                })->ignore($department->id)
            ],
            'department_code' => 'nullable|string|max:50',
        ]);

        $department->update([
            'name'            => $request->name,
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
        // ⭐ FIX: Check isolation before delete
        $department = Department::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        $department->delete();

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department node purged from system.');
    }
}