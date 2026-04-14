<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\EmployeeController as AdminEmp;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController; 
use App\Http\Controllers\Admin\AttendanceController as AdminAtt;
use App\Http\Controllers\Admin\AdminFeedbackController;

// Employee Controllers
use App\Http\Controllers\Employee\DashboardController as EmpDash;
use App\Http\Controllers\Employee\AttendanceController as EmpAtt;
use App\Http\Controllers\Employee\EmployeeFeedbackController; 

/*
|--------------------------------------------------------------------------
| 1. Public & Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () { 
    if (auth()->check()) {
        return auth()->user()->role === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('employee.dashboard');
    }
    return view('index'); 
})->name('home');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. Shared Authenticated Routes (Chat)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('admin.messages.send');
    Route::post('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
});

/*
|--------------------------------------------------------------------------
| 3. Admin Command Center
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Core Dashboard & Intel
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');
    Route::get('/reports/shifts', [AdminDash::class, 'shiftIntel'])->name('reports.shifts');
    
    // ⭐ RESOURCE ROUTES (MISSING FIXED)
    Route::resource('employees', AdminEmp::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class); 

    // Pulse & Screenshots Monitoring
    Route::get('/employees/{id}/pulses', [AdminEmp::class, 'viewPulses'])->name('employees.pulses');
    Route::post('/employees/{id}/trigger-screenshot', [AdminEmp::class, 'triggerScreenshot'])->name('employees.trigger');
    Route::delete('/screenshots/{id}', [AdminDash::class, 'destroyScreenshot'])->name('screenshots.destroy');

    // Attendance & Admin Feedback
    Route::get('/fleet-attendance', [AdminAtt::class, 'index'])->name('attendance.index');
    Route::delete('/fleet-attendance/{id}', [AdminAtt::class, 'destroy'])->name('attendance.destroy');
    Route::post('/feedback/store', [AdminFeedbackController::class, 'store'])->name('feedback.store');
});

/*
|--------------------------------------------------------------------------
| 4. Employee Sector
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    
    // Core Terminal
    Route::get('/dashboard', [EmpDash::class, 'index'])->name('dashboard');

    // Telemetry & Real-time Sync (Fixed)
    Route::post('/sync-timer', [EmpDash::class, 'syncTimer'])->name('sync.timer');
    Route::get('/check-trigger', [EmpDash::class, 'checkTrigger'])->name('check.trigger');
    Route::post('/upload-screenshot', [EmpDash::class, 'uploadScreenshot'])->name('upload.screenshot');
    Route::post('/report-ai', [EmpDash::class, 'reportAiUsage'])->name('report.ai');
    Route::post('/session-stop', [EmpDash::class, 'stopSession'])->name('session.stop');

    // Task Actions
    Route::post('/tasks/{id}/forward', [EmpDash::class, 'forwardTask'])->name('tasks.forward');
    Route::post('/tasks/{id}/update-status', [EmpDash::class, 'updateStatus'])->name('tasks.updateStatus');

    // Attendance & Signal Monitoring
    Route::get('/attendance-history', [EmpAtt::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [EmpAtt::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [EmpAtt::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/report-idle', [EmpAtt::class, 'reportIdle'])->name('attendance.report-idle');

    // ⭐ EMPLOYEE FEEDBACK ROUTE (MISSING FIXED)
    Route::post('/feedback/store', [EmployeeFeedbackController::class, 'store'])->name('feedback.store');
});