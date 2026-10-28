<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BarangayAccountController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\CommunityZoneController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Resident\DashboardController as ResidentDashboardController;
use App\Http\Controllers\Resident\ReportController as ResidentReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.landing');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (Super Admin + Barangay Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super_admin,barangay_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Report Management
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pending', [AdminReportController::class, 'pending'])->name('reports.pending');
        Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/approve', [AdminReportController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/reject', [AdminReportController::class, 'reject'])->name('reports.reject');
        Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.update-status');

        // Community Barangay & Accounts Management (Admins)
        Route::middleware('role:super_admin,barangay_admin')->group(function () {
            // Barangay Management
            Route::resource('barangays', BarangayController::class);
            Route::patch('barangays/{barangay}/toggle-status', [BarangayController::class, 'toggleStatus'])->name('barangays.toggle-status');

            // Community Zones
            Route::post('barangays/{barangay}/zones', [CommunityZoneController::class, 'store'])->name('barangays.zones.store');
            Route::put('zones/{zone}', [CommunityZoneController::class, 'update'])->name('zones.update');
            Route::patch('zones/{zone}/toggle-status', [CommunityZoneController::class, 'toggleStatus'])->name('zones.toggle-status');
            Route::delete('zones/{zone}', [CommunityZoneController::class, 'destroy'])->name('zones.destroy');

            // Barangay Accounts Management
            Route::resource('accounts', BarangayAccountController::class);
            Route::patch('accounts/{account}/toggle-status', [BarangayAccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
            Route::post('accounts/{account}/reset-password', [BarangayAccountController::class, 'resetPassword'])->name('accounts.reset-password');

            // Audit Logs
            Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        });
    });

/*
|--------------------------------------------------------------------------
| Resident Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:resident'])
    ->prefix('resident')
    ->name('resident.')
    ->group(function () {
        Route::get('/dashboard', [ResidentDashboardController::class, 'index'])->name('dashboard');

        // Report CRUD
        Route::resource('reports', ResidentReportController::class)->except(['destroy']);
        Route::delete('/reports/{report}/images/{image}', [ResidentReportController::class, 'destroyImage'])->name('reports.destroy-image');
    });
