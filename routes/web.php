<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

// ── Admin Controllers ─────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController  as AdminDash;
use App\Http\Controllers\Admin\EmployeeController   as AdminEmp;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\AttendanceController as AdminAtt;
use App\Http\Controllers\Admin\AdminFeedbackController;

// ── Employee Controllers ──────────────────────────────────────
use App\Http\Controllers\Employee\DashboardController       as EmpDash;
use App\Http\Controllers\Employee\AttendanceController      as EmpAtt;
use App\Http\Controllers\Employee\EmployeeFeedbackController;


/* =============================================================
   1. PUBLIC ROUTES
============================================================= */
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard');
    }
    return view('index');
})->name('home');


/* =============================================================
   2. AUTH ROUTES
============================================================= */
Route::middleware(['guest'])->group(function () {
    Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/* =============================================================
   3. SHARED AUTH ROUTES
============================================================= */
Route::middleware(['auth'])->group(function () {
    Route::get('/chat',        [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send',  [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
});


/* =============================================================
   4. ADMIN ROUTES
============================================================= */
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

    // Live screenshots JSON endpoint (polled every 15s by admin dashboard JS)
    Route::get('/live-screenshots', [AdminDash::class, 'liveScreenshots'])->name('live.screenshots');

    // ⭐ NEW: Live employee online/offline status (polled every 30s)
    Route::get('/live-employee-status', [AdminDash::class, 'liveEmployeeStatus'])->name('live.employee.status');

    // AI Heatmap JSON endpoint
    Route::get('/ai-heatmap', [AdminDash::class, 'aiHeatmap'])->name('ai.heatmap');

    // Employee-specific AI activity JSON
    Route::get('/employees/{id}/ai-activity', [AdminDash::class, 'employeeAiActivity'])->name('employees.ai.activity');

    // ⭐ FIX: Route name updated to match the dashboard view
    // Shift Reports
    Route::get('/reports/shifts', [AdminDash::class, 'shiftIntel'])->name('reports.shifts');

    // Screenshot delete
    Route::delete('/screenshots/{id}', [AdminDash::class, 'destroyScreenshot'])->name('screenshots.destroy');

    // ──────────────────────────────────────────────────────────
    // IMPORTANT: Named/static routes BEFORE resource routes
    // ──────────────────────────────────────────────────────────
    Route::get('/tasks/forward-history', [TaskController::class, 'forwardHistory'])
        ->name('tasks.forward-history');

    // Resource routes
    Route::resource('employees',   AdminEmp::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('projects',    ProjectController::class);
    Route::resource('tasks',       TaskController::class)->except(['show']);

    // Tasks show (needed for feedback form)
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    // Monitoring extras
    Route::get('/employees/{id}/pulses',              [AdminEmp::class, 'viewPulses'])->name('employees.pulses');
    Route::post('/employees/{id}/trigger-screenshot', [AdminEmp::class, 'triggerScreenshot'])->name('employees.trigger');
    Route::get('/employees/{id}/latest-trigger-shot', [AdminEmp::class, 'latestTriggerShot'])->name('employees.latest.trigger.shot');

    // Attendance
    Route::get('/fleet-attendance',         [AdminAtt::class, 'index'])->name('attendance.index');
    Route::delete('/fleet-attendance/{id}', [AdminAtt::class, 'destroy'])->name('attendance.destroy');

    // Feedback
    Route::post('/feedback/store', [AdminFeedbackController::class, 'store'])->name('feedback.store');
});


/* =============================================================
   5. EMPLOYEE ROUTES
============================================================= */
Route::prefix('employee')->middleware(['auth', 'employee'])->name('employee.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [EmpDash::class, 'index'])->name('dashboard');

    // ── Real-time tracking system ─────────────────────────────
    Route::post('/sync-timer',        [EmpDash::class, 'syncTimer'])->name('sync.timer');
    Route::get('/check-trigger',      [EmpDash::class, 'checkTrigger'])->name('check.trigger');
    Route::post('/upload-screenshot', [EmpDash::class, 'uploadScreenshot'])->name('upload.screenshot');
    Route::post('/report-ai',         [EmpDash::class, 'reportAiUsage'])->name('report.ai');
    Route::post('/session-stop',      [EmpDash::class, 'stopSession'])->name('session.stop');

    // ── Inactivity reporting ──────────────────────────────────
    Route::post('/report-idle', [EmpDash::class, 'reportIdle'])->name('report.idle.emp');

    // ── Task actions ──────────────────────────────────────────
    Route::post('/tasks/{id}/forward',       [EmpDash::class, 'forwardTask'])->name('tasks.forward');
    Route::post('/tasks/{id}/update-status', [EmpDash::class, 'updateStatus'])->name('tasks.updateStatus');

    // ── Attendance ────────────────────────────────────────────
    Route::get('/attendance-history',      [EmpAtt::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in',    [EmpAtt::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out',   [EmpAtt::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/report-idle', [EmpAtt::class, 'reportIdle'])->name('attendance.report-idle');
    // employee attendence
   

    // ── Feedback ──────────────────────────────────────────────
    Route::post('/feedback/store', [EmployeeFeedbackController::class, 'store'])->name('feedback.store');
});
// chat box 
Route::delete('/chat/message/{id}', [App\Http\Controllers\ChatController::class, 'deleteMessage']);
Route::get('/chat/download/{id}', [App\Http\Controllers\ChatController::class, 'downloadFile']);
Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount']);