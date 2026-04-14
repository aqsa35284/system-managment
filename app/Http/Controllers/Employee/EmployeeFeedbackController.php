<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeFeedback;
use Illuminate\Support\Facades\Auth;

class EmployeeFeedbackController extends Controller
{
    /**
     * Store employee progress update
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'task_id'     => 'required',
            'update_note' => 'required',
        ]);

        // 2. Data Persistence
        EmployeeFeedback::create([
            'user_id'               => Auth::id(),
            'task_id'               => $request->task_id,
            'update_note'           => $request->update_note,
            'blockers'              => $request->blockers,
            'completion_percentage' => $request->completion_percentage ?? 0,
        ]);

        // 3. Response
        return back()->with('success', 'Progress Report Sent to Admin!');
    }
}