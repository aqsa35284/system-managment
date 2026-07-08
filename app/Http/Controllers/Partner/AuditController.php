<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $partnerId = Auth::id();
        
        // 1. Sirf is partner ki companies ki IDs nikalen
        $companyIds = Company::where('partner_id', $partnerId)->pluck('id');

        // 2. Optimized Query: Filters ko ek hi "whereHas" mein combine kar diya gaya hai
        $query = ActivityLog::with(['user', 'subject'])
            ->whereHas('user', function($q) use ($companyIds, $request) {
                
                // Rule A: User unhi companies ka hissa ho aur role sirf admin/employee ho (Stealth Mode)
                $q->whereIn('company_id', $companyIds)
                  ->whereIn('role', ['admin', 'employee']); 
                
                // Rule B: Agar dropdown se filter lagaya jaye, toh yahan apply hoga
                if ($request->filled('company_id')) {
                    $q->where('company_id', $request->company_id);
                }
                
            })
            ->latest();

        // 3. Paginate the optimized results
        $logs = $query->paginate(20)->withQueryString();
        
        // 4. Dropdown ke liye partner ki apni companies ki list
        $companies = Company::where('partner_id', $partnerId)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return view('partner.audits.index', compact('logs', 'companies'));
    }
}