<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// ── Core Authentication & Base Controllers ────────────────────
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

// ── Admin Controllers ─────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\EmployeeController as AdminEmp;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\AttendanceController as AdminAtt;
use App\Http\Controllers\Admin\AdminFeedbackController;

// ── Employee Controllers ──────────────────────────────────────
use App\Http\Controllers\Employee\DashboardController as EmpDash;
use App\Http\Controllers\Employee\AttendanceController as EmpAtt;
use App\Http\Controllers\Employee\EmployeeFeedbackController;
use App\Http\Controllers\Employee\ProjectController as EmpProject;
use App\Http\Controllers\Employee\TaskController as EmpTask;

// ── Super Admin Controller ────────────────────────────────────
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDash;
use App\Http\Controllers\SuperAdmin\AuditController;


/* =============================================================
   1. PUBLIC ROUTES (Fixed for Super Admin Redirect)
============================================================= */
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        
        // 🚦 Traffic Police: Har role ko uske sahi dashboard pe bhejein
        if ($role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    }
    return view('index');
})->name('home');

// Extra safe route for redirection loops
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware('auth')->name('dashboard');


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
   4. ADMIN ROUTES (Added check_subscription)
============================================================= */
Route::prefix('admin')->middleware(['auth', 'admin', 'check_subscription'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

    // Live screenshots JSON endpoint
    Route::get('/live-screenshots', [AdminDash::class, 'liveScreenshots'])->name('live.screenshots');

    // Live employee online/offline status
    Route::get('/live-employee-status', [AdminDash::class, 'liveEmployeeStatus'])->name('live.employee.status');

    // AI Heatmap JSON endpoint
    Route::get('/ai-heatmap', [AdminDash::class, 'aiHeatmap'])->name('ai.heatmap');

    // Employee-specific AI activity JSON
    Route::get('/employees/{id}/ai-activity', [AdminDash::class, 'employeeAiActivity'])->name('employees.ai.activity');

    // Shift Reports
    Route::get('/reports/shifts', [AdminDash::class, 'shiftIntel'])->name('reports.shifts');

    // Screenshot delete
    Route::delete('/screenshots/{id}', [AdminDash::class, 'destroyScreenshot'])->name('screenshots.destroy');

    // Static routes BEFORE resource routes
    Route::get('/tasks/forward-history', [TaskController::class, 'forwardHistory'])->name('tasks.forward-history');

    // Resource routes
    Route::resource('employees',   AdminEmp::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('projects',    ProjectController::class);
    Route::resource('tasks',       TaskController::class)->except(['show']);

    // Tasks show
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
   5. EMPLOYEE ROUTES (Added check_subscription)
============================================================= */
Route::prefix('employee')->middleware(['auth', 'employee', 'check_subscription'])->name('employee.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [EmpDash::class, 'index'])->name('dashboard');

    // Core Resource Routes for Fleet System
    Route::resource('projects', EmpProject::class)->only(['index', 'show']);
    Route::resource('tasks', EmpTask::class)->only(['index', 'show']);

    // Real-time tracking system
    Route::post('/sync-timer',        [EmpDash::class, 'syncTimer'])->name('sync.timer');
    Route::get('/check-trigger',      [EmpDash::class, 'checkTrigger'])->name('check.trigger');
    Route::post('/upload-screenshot', [EmpDash::class, 'uploadScreenshot'])->name('upload.screenshot');
    Route::post('/report-ai',         [EmpDash::class, 'reportAiUsage'])->name('report.ai');
    Route::post('/session-stop',      [EmpDash::class, 'stopSession'])->name('session.stop');

    // Inactivity reporting
    Route::post('/report-idle', [EmpDash::class, 'reportIdle'])->name('report.idle.emp');

    // Task actions
    Route::post('/tasks/{id}/forward',       [EmpDash::class, 'forwardTask'])->name('tasks.forward');
    Route::post('/tasks/{id}/update-status', [EmpDash::class, 'updateStatus'])->name('tasks.updateStatus');

    // Attendance
    Route::get('/attendance-history',      [EmpAtt::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in',    [EmpAtt::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out',   [EmpAtt::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/report-idle', [EmpAtt::class, 'reportIdle'])->name('attendance.report-idle');

    // Feedback / Status update transmissions
    Route::post('/feedback/store', [EmployeeFeedbackController::class, 'store'])->name('feedback.store');
});


/* =============================================================
   6. EXTRA ROUTES
============================================================= */
// Chat system engine
Route::delete('/chat/message/{id}', [ChatController::class, 'deleteMessage']);
Route::get('/chat/download/{id}', [ChatController::class, 'downloadFile']);
Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);

// Command terminal line integration
Route::get('/run-command', function () {
    $command = request()->query('command');
    if ($command) {
        Artisan::call($command);
        return Artisan::output();
    }
    return "No command provided.";
});


/* =============================================================
   7. SUPER ADMIN ROUTES (Added shadow_mode)
============================================================= */
Route::middleware(['auth', 'super_admin', 'shadow_mode'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminDash::class, 'index'])->name('superadmin.dashboard');
    Route::post('/company/{id}/renew', [SuperAdminDash::class, 'renewSubscription'])->name('company.renew');
    Route::post('/company/{id}/toggle-status', [SuperAdminDash::class, 'toggleStatus'])->name('company.toggle');
});
/* =============================================================
   7. SUPER ADMIN ROUTES (FINAL CLEAN VERSION)
============================================================= */
// NOTE: Humne middleware mein 'shadow_audit' add kar diya hai 
// taake har action log ho sake (agar aapne middleware register kiya hai).
Route::middleware(['auth', 'super_admin', 'shadow_audit'])->prefix('super-admin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [SuperAdminDash::class, 'index'])->name('superadmin.dashboard');

    // Company Management (CRUD)
    Route::get('/company/create', [SuperAdminDash::class, 'create'])->name('company.create');
    Route::post('/company/store', [SuperAdminDash::class, 'store'])->name('company.store');
    Route::get('/company/{id}', [SuperAdminDash::class, 'show'])->name('company.show');
    Route::get('/company/{id}/edit', [SuperAdminDash::class, 'edit'])->name('company.edit');
    Route::put('/company/{id}', [SuperAdminDash::class, 'update'])->name('company.update');
    Route::delete('/company/{id}', [SuperAdminDash::class, 'destroy'])->name('company.destroy');
    
    // Company Actions
    Route::post('/company/{id}/toggle', [SuperAdminDash::class, 'toggleStatus'])->name('company.toggle');
    Route::post('/company/{id}/renew', [SuperAdminDash::class, 'renewSubscription'])->name('company.renew');
    
    // Impersonation (God Mode) & Audits
    Route::post('/impersonate/{id}', [SuperAdminDash::class, 'impersonate'])->name('superadmin.impersonate');
    Route::get('/exit-impersonation', [SuperAdminDash::class, 'exitImpersonation'])->name('superadmin.exit');
    
    // Audit Logs
    Route::get('/shadow-audits', [AuditController::class, 'index'])->name('superadmin.audits');
});
// routes/web.php

Route::post('/company/{id}/renew', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'renewSubscription'])
    ->name('company.renew')
    ->middleware('auth'); // Ensure superadmin middleware agar aapne lagaya hai
