<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeFeedback;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeFeedbackController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // ⭐ STORE — FIXED: Task days_worked + overdue_days bhi update
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // ⭐ FIX 1: Proper validation with messages
        $request->validate([
            'task_id'               => 'required|exists:tasks,id',
            'update_note'           => 'required|string|max:1000',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'blockers'              => 'nullable|string|max:500',
        ], [
            'task_id.required'     => 'Task selection is required.',
            'update_note.required' => 'Work description is required.',
        ]);

        // ⭐ FIX 2: Task bhi update karo (days_worked, overdue_days, completion_percentage)
        $task = Task::where('id', $request->task_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $today    = Carbon::today('Asia/Karachi');
        $timezone = 'Asia/Karachi';

        $task->days_worked = (int) ($task->days_worked ?? 0) + 1;

        if ($task->due_date && $today->gt(Carbon::parse($task->due_date))) {
            $task->overdue_days = (int) $today->diffInDays(Carbon::parse($task->due_date));
        } else {
            $task->overdue_days = 0;
        }

        if ($request->filled('completion_percentage')) {
            $task->completion_percentage = (int) $request->completion_percentage;
        }

        if ((int) $request->completion_percentage === 100) {
            $task->status           = 'completed';
            $task->completion_stage = 'completed';
        } elseif ($task->completion_percentage > 0) {
            $task->status = 'in_progress';
        }

        $task->save();

        // ⭐ FIX 3: EmployeeFeedback save
        EmployeeFeedback::create([
            'user_id'               => Auth::id(),
            'task_id'               => $request->task_id,
            'update_note'           => $request->update_note,
            'blockers'              => $request->blockers ?? null,
            'completion_percentage' => (int) ($request->completion_percentage ?? 0),
        ]);

        return back()->with('success', 'Progress Report Sent to Admin!');
    }
}