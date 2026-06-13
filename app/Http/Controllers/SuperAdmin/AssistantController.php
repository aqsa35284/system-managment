<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{User, Company};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AssistantController extends Controller
{
    // List all assistants
    public function index()
    {
        $assistants = User::where('role', 'assistant_admin')->with('managedCompanies')->get();
        return view('superadmin.assistants.index', compact('assistants'));
    }

    // Show form to create assistant
    public function create()
    {
        $companies = Company::all();
        return view('superadmin.assistants.create', compact('companies'));
    }

    // Save assistant to DB
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'company_ids' => 'array', // Array of companies assigned to this assistant
            'company_ids.*' => 'exists:companies,id'
        ]);

        $assistant = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'assistant_admin',
            'status' => 'active'
        ]);

        if ($request->has('company_ids')) {
            $assistant->managedCompanies()->sync($request->company_ids);
        }

        return redirect()->route('superadmin.assistants.index')->with('success', 'Assistant Admin Created Successfully.');
    }
}