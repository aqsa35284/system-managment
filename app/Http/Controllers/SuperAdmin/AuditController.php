<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{AuditLog, Company};
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // ⭐ Eager load 'admin' aur 'company' taake loop mein query na lage
        $query = AuditLog::with(['admin', 'company'])->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $logs = $query->paginate(50)->withQueryString();
        
        // ⭐ Dropdown ke liye companies bheji
        $companies = Company::select('id', 'name')->get();

        return view('superadmin.audits.index', compact('logs', 'companies'));
    }
}