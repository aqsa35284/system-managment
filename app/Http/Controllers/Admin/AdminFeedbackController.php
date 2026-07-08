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
        // ⭐ FIX 1: 'task_id' mein se 'exists:tasks,id' hata diya taake '0' allow ho jaye
        $request->validate([
            'employee_id'  => 'required|exists:users,id',
            'task_id'      => 'required|integer', 
            'review_note'  => 'required|string',
            'rating_score' => 'nullable|numeric|min:1|max:10',
            'status'       => 'required|in:excellent,good,needs_improvement'
        ]);

        $company_id = Auth::user()->company_id;

        // Security Verification - Check if Employee belongs to THIS admin's company
        $validEmployee = User::where('id', $request->employee_id)
                             ->where('company_id', $company_id)
                             ->exists();

        // ⭐ FIX 2: Security Verification - Task check sirf tab chale jab task_id '0' na ho
        $validTask = true; 
        if ($request->task_id != 0) {
            $validTask = Task::where('id', $request->task_id)
                             ->where('company_id', $company_id)
                             ->exists();
        }

        // Agar kisi ne inspect element se ID change karne ki koshish ki toh system block kar dega
        if (!$validEmployee || !$validTask) {
            abort(403, 'CRITICAL SECURITY ALERT: Unauthorized Data Access Detected.');
        }

        // ⭐ FIX 3: Agar task_id '0' hai toh database mein 'null' bhejo warna crash ho jayega
        AdminFeedback::create([
            'admin_id'     => Auth::id(),
            'company_id'   => $company_id, // Always save company_id for global isolation
            'employee_id'  => $request->employee_id,
            'task_id'      => $request->task_id == 0 ? null : $request->task_id, 
            'review_note'  => $request->review_note,
            'rating_score' => $request->rating_score ?? 0,
            'status'       => $request->status,
        ]);

        return back()->with('success', 'Technical Review Transmitted to Employee!');
    }
}