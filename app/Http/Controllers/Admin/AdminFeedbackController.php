<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminFeedback;
use Illuminate\Support\Facades\Auth;

class AdminFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'task_id'     => 'required',
            'review_note' => 'required',
            'status'      => 'required|in:excellent,good,needs_improvement'
        ]);

        AdminFeedback::create([
            'admin_id'     => Auth::id(),
            'employee_id'  => $request->employee_id,
            'task_id'      => $request->task_id,
            'review_note'  => $request->review_note,
            'rating_score' => $request->rating_score ?? 0,
            'status'       => $request->status,
        ]);

        return back()->with('success', 'Technical Review Transmitted to Employee!');
    }
}