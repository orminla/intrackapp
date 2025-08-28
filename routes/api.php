<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InspectorController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\HistoryController as AdminHistoryController;
use App\Http\Controllers\Admin\ChangeRequestController as AdminChangeRequestController;

// Inspector Controllers
use App\Http\Controllers\Inspector\DashboardController as InspectorDashboardController;
use App\Http\Controllers\Inspector\ScheduleController as InspectorScheduleController;
use App\Http\Controllers\Inspector\HistoryController as InspectorHistoryController;

// TESTING TOKEN
Route::get('/token-test', function () {
    $user = \App\Models\User::first();
    return $user->createToken('test')->plainTextToken;
});

// 🔐 AUTHENTICATION
Route::post('/login', [AuthController::class, 'authenticate']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);

// 🔐 PROTECTED ROUTES (Semua di bawah ini butuh token)
Route::middleware('auth:sanctum')->group(function () {
    // profile
    // Route::get('/profil', [ProfileController::class, 'show']);
    // Route::post('/profil/update', [ProfileController::class, 'update'])->name('profile.update');

    // admin
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/summary', [AdminDashboardController::class, 'summary']);
        Route::get('/dashboard/upcoming', [AdminDashboardController::class, 'upcoming']);
        Route::get('/chart/inspection', [AdminDashboardController::class, 'inspectionChart']);
        Route::get('/chart/distribution', [AdminDashboardController::class, 'distributionChart']);

        // Jadwal
        Route::get('/schedules', [AdminScheduleController::class, 'index']);
        Route::post('/schedules', [AdminScheduleController::class, 'store']);
        Route::put('/schedules/{id}', [AdminScheduleController::class, 'update']);
        Route::get('/auto-inspector', [AdminScheduleController::class, 'getAutoInspector']);

        // Petugas (Inspector)
        Route::post('/inspectors', [InspectorController::class, 'store']);
        Route::put('/inspectors/{nip}', [InspectorController::class, 'update']);
        Route::delete('/inspectors/{nip}', [InspectorController::class, 'destroy']);

        // Validasi Change Request dari Petugas
        Route::get('/change-request', [AdminScheduleController::class, 'changereq']);
        Route::post('/change-request/update', [AdminChangeRequestController::class, 'update']);

        Route::post('/admin', [AdminController::class, 'store']);

        // Laporan dan Riwayat
        Route::get('/reports', [ReportController::class, 'index']);
        Route::get('/history', [AdminHistoryController::class, 'index']);
    });

    // inspector
    Route::prefix('inspector')->group(function () {
        // Dashboard
        Route::get('/dashboard', [InspectorDashboardController::class, 'index'])->name('dashboard');
        Route::post('/change-request', [InspectorDashboardController::class, 'requestChangeInspector'])->name('change-request');

        // Penjadwalan Inspeksi (jadwal petugas)
        Route::get('/jadwal', [InspectorScheduleController::class, 'index']);
        Route::post('/jadwal', [InspectorScheduleController::class, 'store']);

        // Riwayat Inspeksi
        Route::get('/history', [InspectorHistoryController::class, 'index']);

        // Permintaan Ganti Petugas
        Route::post('/change-request/store', [InspectorChangeRequestController::class, 'store']);
    });
});
