<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\InspectorController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Inspector\DashboardController as InspectorDashboardController;
use App\Http\Controllers\Inspector\ScheduleController as InspectorScheduleReportController;
use App\Http\Controllers\Inspector\HistoryController as InspectorHistoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;

// LOGIN & LOGOUT
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.send');
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify-otp');
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verify'])->name('verify.email');
Route::post('/send-verification', [EmailVerificationController::class, 'sendWhatsappMessage'])->name('send.verification');

// AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/download/document/{id}', [DocumentController::class, 'download'])->name('document.download');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Redirect user ke dashboard sesuai role
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        return match ($role) {
            'admin' => view('admin.dashboard'),
            'inspector' => view('inspector.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');


    Route::middleware('can:isAdmin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/chart/inspection', [AdminDashboardController::class, 'inspectionChart']);
        Route::get('/chart/distribution', [AdminDashboardController::class, 'distributionChart']);

        Route::get('/get-inspector', [ScheduleController::class, 'getAutoInspector'])->name('get-inspector');
        Route::post('/change-request/update', [ScheduleController::class, 'updateChangeInspector'])->name('change_request.update');
        Route::post('/update-inspector', [ScheduleController::class, 'updateInspector'])->name('updateInspector');

        Route::resource('jadwal', ScheduleController::class);
        Route::put('/jadwal/validasi/{id}', [ScheduleController::class, 'validasi'])->name('jadwal.validasi');

        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan');
        Route::put('/laporan/{id}/status', [ReportController::class, 'updateStatus'])->name('laporan.validasi');
        Route::get('/laporan/{id}', [ReportController::class, 'show'])->name('laporan.show');

        Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat');
        Route::get('/riwayat/{id}', [HistoryController::class, 'show'])->name('riwayat.show');

        Route::post('/petugas/import', [InspectorController::class, 'import'])->name('petugas.import');
        Route::resource('petugas', InspectorController::class);

        Route::get('/pengaturan', [AdminController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan/tambah', [AdminController::class, 'store'])->name('pengaturan.store');
        Route::delete('/pengaturan/{id}', [AdminController::class, 'destroy'])->name('pengaturan.destroy');
        Route::get('/pengaturan/{nip}', [AdminController::class, 'show'])->name('pengaturan.show');
        Route::put('/pengaturan/{nip}', [AdminController::class, 'update'])->name('pengaturan.update');
    });

    // INSPECTOR ROUTES
    Route::middleware('can:isInspector')->prefix('inspector')->name('inspector.')->group(function () {
        Route::get('/dashboard', [InspectorDashboardController::class, 'index'])->name('dashboard');
        Route::post('/change-request', [InspectorDashboardController::class, 'requestChangeInspector'])->name('change-request');

        Route::resource('/jadwal', InspectorScheduleReportController::class);
        Route::put('/jadwal/{id}/validasi', [InspectorScheduleReportController::class, 'updateStatus'])->name('jadwal.validasi');

        Route::get('/riwayat', [InspectorHistoryController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{id}', [InspectorHistoryController::class, 'show'])->name('riwayat.show');
    });
});
