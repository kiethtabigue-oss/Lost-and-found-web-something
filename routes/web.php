<?php

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
