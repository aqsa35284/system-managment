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
use App\Http\Controllers\SuperAdmin\PartnerController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\InvoiceController;

// ── Partner Controllers ───────────────────────────────────────
use App\Http\Controllers\Partner\DashboardController as PartnerDash;
use App\Http\Controllers\Partner\AuditController as PartnerAuditController;

/* =============================================================
   1. PUBLIC ROUTES
============================================================= */
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        
        // 🚦 Traffic Police
        if ($role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'partner') { 
            return redirect()->route('partner.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    }
    return view('index'); 
})->name('home');

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
   3. SHARED AUTH ROUTES (CHAT)
============================================================= */
Route::middleware(['auth'])->group(function () {
    Route::get('/chat',        [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send',  [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::delete('/chat/message/{id}', [ChatController::class, 'deleteMessage']);
    Route::get('/chat/download/{id}', [ChatController::class, 'downloadFile']);
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);
});

/* =============================================================
   4. ADMIN ROUTES 
============================================================= */
Route::prefix('admin')->middleware(['auth', 'admin', 'check_subscription'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');
    Route::get('/live-screenshots', [AdminDash::class, 'liveScreenshots'])->name('live.screenshots');
    Route::get('/live-employee-status', [AdminDash::class, 'liveEmployeeStatus'])->name('live.employee.status');
    Route::get('/ai-heatmap', [AdminDash::class, 'aiHeatmap'])->name('ai.heatmap');
    Route::get('/employees/{id}/ai-activity', [AdminDash::class, 'employeeAiActivity'])->name('employees.ai.activity');
    Route::get('/reports/shifts', [AdminDash::class, 'shiftIntel'])->name('reports.shifts');
    Route::delete('/screenshots/{id}', [AdminDash::class, 'destroyScreenshot'])->name('screenshots.destroy');
    Route::get('/tasks/forward-history', [TaskController::class, 'forwardHistory'])->name('tasks.forward-history');

    Route::resource('employees',   AdminEmp::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('projects',    ProjectController::class);
    Route::resource('tasks',       TaskController::class)->except(['show']);

    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/employees/{id}/pulses',              [AdminEmp::class, 'viewPulses'])->name('employees.pulses');
    Route::post('/employees/{id}/trigger-screenshot', [AdminEmp::class, 'triggerScreenshot'])->name('employees.trigger');
    Route::get('/employees/{id}/latest-trigger-shot', [AdminEmp::class, 'latestTriggerShot'])->name('employees.latest.trigger.shot');

    Route::get('/fleet-attendance',         [AdminAtt::class, 'index'])->name('attendance.index');
    Route::delete('/fleet-attendance/{id}', [AdminAtt::class, 'destroy'])->name('attendance.destroy');
    Route::post('/feedback/store', [AdminFeedbackController::class, 'store'])->name('feedback.store');
});

/* =============================================================
   5. EMPLOYEE ROUTES
============================================================= */
Route::prefix('employee')->middleware(['auth', 'employee', 'check_subscription'])->name('employee.')->group(function () {
    Route::get('/dashboard', [EmpDash::class, 'index'])->name('dashboard');
    Route::resource('projects', EmpProject::class)->only(['index', 'show']);
    Route::resource('tasks', EmpTask::class)->only(['index', 'show']);

    Route::post('/sync-timer',        [EmpDash::class, 'syncTimer'])->name('sync.timer');
    Route::get('/check-trigger',      [EmpDash::class, 'checkTrigger'])->name('check.trigger');
    Route::post('/upload-screenshot', [EmpDash::class, 'uploadScreenshot'])->name('upload.screenshot');
    Route::post('/report-ai',         [EmpDash::class, 'reportAiUsage'])->name('report.ai');
    Route::post('/session-stop',      [EmpDash::class, 'stopSession'])->name('session.stop');
    Route::post('/report-idle', [EmpDash::class, 'reportIdle'])->name('report.idle.emp');

    Route::post('/tasks/{id}/forward',       [EmpDash::class, 'forwardTask'])->name('tasks.forward');
    Route::post('/tasks/{id}/update-status', [EmpDash::class, 'updateStatus'])->name('tasks.updateStatus');

    Route::get('/attendance-history',      [EmpAtt::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in',    [EmpAtt::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out',   [EmpAtt::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/report-idle', [EmpAtt::class, 'reportIdle'])->name('attendance.report-idle');
    Route::post('/feedback/store', [EmployeeFeedbackController::class, 'store'])->name('feedback.store');
});

/* =============================================================
   6. EXTRA ROUTES (Terminal)
============================================================= */
Route::get('/run-command', function () {
    $command = request()->query('command');
    if ($command) {
        Artisan::call($command);
        return Artisan::output();
    }
    return "No command provided.";
});

/* =============================================================
   7. SUPER ADMIN ROUTES (Master Control)
============================================================= */
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDash::class, 'index'])->name('dashboard');
    Route::get('/tenants', [SuperAdminDash::class, 'index'])->name('tenants.index'); 
    
    // Company/Tenant Management
    Route::get('/company/create', [SuperAdminDash::class, 'create'])->name('company.create');
    Route::post('/company/store', [SuperAdminDash::class, 'store'])->name('company.store');
    Route::get('/company/{id}', [SuperAdminDash::class, 'show'])->name('company.show');
    Route::get('/company/{id}/edit', [SuperAdminDash::class, 'edit'])->name('company.edit');
    Route::put('/company/{id}', [SuperAdminDash::class, 'update'])->name('company.update');
    Route::delete('/company/{id}', [SuperAdminDash::class, 'destroy'])->name('company.destroy');
    Route::post('/company/{id}/toggle', [SuperAdminDash::class, 'toggleStatus'])->name('company.toggle');
    Route::post('/company/{id}/renew', [SuperAdminDash::class, 'renewSubscription'])->name('company.renew');

    // Super Admin -> Tenant God Mode
    Route::post('/impersonate/{id}', [SuperAdminDash::class, 'impersonate'])->name('impersonate');
    Route::get('/exit-impersonation', [SuperAdminDash::class, 'exitImpersonation'])->name('exit');
    
    // Shadow Audits
    Route::get('/shadow-audits', [AuditController::class, 'index'])->name('audits.index');

    // Partner Management
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::get('/partners/create', [PartnerController::class, 'create'])->name('partners.create');
    Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
    Route::get('/partners/{id}', [PartnerController::class, 'show'])->name('partners.show');
    Route::get('/partners/{id}/edit', [PartnerController::class, 'edit'])->name('partners.edit');
    Route::put('/partners/{id}', [PartnerController::class, 'update'])->name('partners.update');
    Route::post('/partners/{id}/toggle', [PartnerController::class, 'toggle'])->name('partners.toggle');
    Route::delete('/partners/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');
    
    // ⚡ MISSING ROUTE FIXED: Super Admin -> Partner God Mode
    Route::post('/partners/{id}/impersonate', [PartnerController::class, 'impersonate'])->name('partners.impersonate');

    // Billing & Plans
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{id}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{id}', [PlanController::class, 'update'])->name('plans.update');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{id}/pay', [InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
});
/* =============================================================
   8. BUSINESS PARTNER ROUTES
============================================================= */
Route::middleware(['auth', 'partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('/dashboard', [PartnerDash::class, 'index'])->name('dashboard');
    Route::get('/tenants', [PartnerDash::class, 'tenants'])->name('tenants.index'); 
    
    // Tenant Management for Partners
    Route::get('/company/create', [PartnerDash::class, 'createCompany'])->name('company.create');
    Route::post('/company/store', [PartnerDash::class, 'storeCompany'])->name('company.store');
    Route::get('/company/{id}', [PartnerDash::class, 'show'])->name('company.show');
    Route::get('/company/{id}/edit', [PartnerDash::class, 'edit'])->name('company.edit');
    Route::put('/company/{id}', [PartnerDash::class, 'update'])->name('company.update');
    Route::post('/company/{id}/toggle', [PartnerDash::class, 'toggleStatus'])->name('company.toggle');
    Route::delete('/company/{id}/destroy', [PartnerDash::class, 'destroyCompany'])->name('company.destroy');
    Route::post('/company/{id}/renew', [PartnerDash::class, 'renewSubscription'])->name('company.renew');
    
    // Impersonation (God Mode)
    Route::post('/impersonate/{companyId}', [PartnerDash::class, 'impersonate'])->name('impersonate');
    
    // Activity Monitor / Audits
    Route::get('/activity-monitor', [PartnerAuditController::class, 'index'])->name('audits.index');
    
    // Financial Engine — view plans only (matrix edits: Super Admin)
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{id}/pay', [InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
});

/* =============================================================
   9. GLOBAL EXIT ROUTES (For God Mode)
============================================================= */

// 1. Partner returning from Admin Account
Route::get('/exit-impersonation', [PartnerDash::class, 'exitImpersonation'])
    ->name('partner.exit')
    ->withoutMiddleware(['auth', 'partner', 'super_admin']);

// 2. Super Admin returning from Partner Account
Route::get('/exit-partner-mode', [PartnerController::class, 'exitImpersonation'])
    ->name('superadmin.exit_partner')
    ->withoutMiddleware(['auth', 'super_admin', 'partner']);