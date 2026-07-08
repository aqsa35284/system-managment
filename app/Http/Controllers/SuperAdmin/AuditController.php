<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog; // 🟢 Updated to new tracking model
use App\Models\Company;
use App\Models\User;        // 🟢 Required for leaderboard metrics
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // 1. Base Query with Eager Loading (user aur subject/company data laye ga taake queries fast hon)
        $query = ActivityLog::with(['user', 'subject'])->latest();

        // 2. 🟢 COMPANY FILTER LOGIC: Kyunke hum polymorphic use kar rahe hain, isliye conditions aise lagengi
        if ($request->filled('company_id')) {
            $query->where('subject_type', Company::class)
                  ->where('subject_id', $request->company_id);
        }

        // Fetch logs (25 per page is optimal for UI)
        $logs = $query->paginate(25)->withQueryString();
        
        // 3. Dropdown filter ke liye list of companies
        $companies = Company::select('id', 'name')->orderBy('name', 'asc')->get();

        // 4. 🚀 RESTORED LEADERBOARD: Kis partner/employee ne kitni companies add ki
        $userPerformance = User::whereIn('role', ['partner', 'employee', 'admin'])
            ->withCount('companies')
            ->orderBy('companies_count', 'desc')
            ->get();

        // 5. 🚀 RESTORED STATS: Total actions aur top contributor
        $stats = [
            'total_actions' => ActivityLog::count(),
            'critical_updates' => ActivityLog::whereIn('action', ['updated', 'deleted'])->count(),
            'top_contributor' => $userPerformance->first()->name ?? 'N/A'
        ];

        return view('superadmin.audits.index', compact('logs', 'companies', 'userPerformance', 'stats'));
    }
}