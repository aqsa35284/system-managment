<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminFeedback;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class AdminFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:users,id',
            'task_id'      => 'required|exists:tasks,id',
            'review_note'  => 'required|string',
            'rating_score' => 'nullable|numeric|min:1|max:10',
            'status'       => 'required|in:excellent,good,needs_improvement'
        ]);

        $company_id = Auth::user()->company_id;

        // ⭐ FIX 1: Security Verification - Check if Employee belongs to THIS admin's company
        $validEmployee = User::where('id', $request->employee_id)
                             ->where('company_id', $company_id)
                             ->exists();

        // ⭐ FIX 2: Security Verification - Check if Task belongs to THIS admin's company
        $validTask = Task::where('id', $request->task_id)
                         ->where('company_id', $company_id)
                         ->exists();

        // Agar kisi ne inspect element se ID change karne ki koshish ki toh system block kar dega
        if (!$validEmployee || !$validTask) {
            abort(403, 'CRITICAL SECURITY ALERT: Unauthorized Data Access Detected.');
        }

        AdminFeedback::create([
            'admin_id'     => Auth::id(),
            'company_id'   => $company_id, // ⭐ Always save company_id for global isolation
            'employee_id'  => $request->employee_id,
            'task_id'      => $request->task_id,
            'review_note'  => $request->review_note,
            'rating_score' => $request->rating_score ?? 0,
            'status'       => $request->status,
        ]);

        return back()->with('success', 'Technical Review Transmitted to Employee!');
    }
}