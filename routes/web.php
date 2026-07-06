<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CounselorDashboardController;
use App\Http\Controllers\HomeroomDashboardController;
use App\Http\Controllers\AdminConfigController;
use App\Http\Controllers\AdminClassController;

// Halaman Awal - Redirection otomatis sesuai autentikasi
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// Rute Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Autentikasi Umum (Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // Fallback get logout
    
    // --- 1. RUTE SISWA (STUDENT) ---
    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/chat', [ChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/chat/reset', [ChatController::class, 'reset'])->name('chat.reset');
    });

    // --- 2. RUTE GURU BK (COUNSELOR) ---
    Route::prefix('bk')->name('bk.')->middleware('role:guru_bk')->group(function () {
        Route::get('/dashboard', [CounselorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/student/{id}', [CounselorDashboardController::class, 'showStudentDetail'])->name('student.detail');
        Route::post('/student/{id}/note', [CounselorDashboardController::class, 'addNote'])->name('student.note');
        Route::post('/student/{id}/recommendation/generate', [CounselorDashboardController::class, 'generateAIRecommendation'])->name('student.recommendation.generate');
        Route::post('/student/{id}/report/trigger', [CounselorDashboardController::class, 'triggerReport'])->name('student.report.trigger');
        Route::post('/student/{id}/reset-session', [CounselorDashboardController::class, 'resetSession'])->name('student.reset-session');
    });

    // --- 3. RUTE WALI KELAS (HOMEROOM) ---
    Route::prefix('wali')->name('wali.')->middleware('role:wali_kelas')->group(function () {
        Route::get('/dashboard', [HomeroomDashboardController::class, 'index'])->name('dashboard');
        Route::get('/student/{id}/report', [HomeroomDashboardController::class, 'viewStudentReport'])->name('student.report');
    });

    // --- 4. RUTE ADMINISTRATOR ---
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminConfigController::class, 'index'])->name('dashboard');
        
        // AI Provider
        Route::post('/ai-provider/switch', [AdminConfigController::class, 'switchProvider'])->name('ai-provider.switch');
        Route::post('/ai-provider/{id}/update', [AdminConfigController::class, 'updateAIProvider'])->name('ai-provider.update');
        Route::post('/ai-provider/{id}/test-connection', [AdminConfigController::class, 'testConnection'])->name('ai-provider.test-connection');
        
        // Knowledge Base
        Route::get('/kb', [AdminConfigController::class, 'listKB'])->name('kb');
        Route::post('/kb/store', [AdminConfigController::class, 'storeKB'])->name('kb.store');
        Route::post('/kb/{id}/delete', [AdminConfigController::class, 'destroyKB'])->name('kb.delete');
        
        // Rules
        Route::get('/rules', [AdminConfigController::class, 'listRules'])->name('rules');
        Route::post('/rules/store', [AdminConfigController::class, 'storeRule'])->name('rules.store');
        Route::post('/rules/{id}/toggle', [AdminConfigController::class, 'toggleRule'])->name('rules.toggle');
        Route::post('/rules/{id}/update', [AdminConfigController::class, 'updateRule'])->name('rules.update');
        Route::post('/rules/{id}/delete', [AdminConfigController::class, 'destroyRule'])->name('rules.delete');
        
        // Users Management
        Route::get('/users', [AdminConfigController::class, 'listUsers'])->name('users');
        Route::get('/users/import-template', [AdminConfigController::class, 'downloadImportTemplate'])->name('users.import-template');
        Route::post('/users/import', [AdminConfigController::class, 'importUsers'])->name('users.import');
        Route::post('/users/store', [AdminConfigController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{id}/update', [AdminConfigController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{id}/reset-password', [AdminConfigController::class, 'resetPasswordUser'])->name('users.reset-password');
        Route::post('/users/{id}/delete', [AdminConfigController::class, 'destroyUser'])->name('users.delete');
        Route::post('/users/{id}/reset-session', [AdminConfigController::class, 'resetStudentSession'])->name('users.reset-session');

        // Class & Plotting Management
        Route::get('/classes', [AdminClassController::class, 'index'])->name('classes.index');
        Route::post('/classes/store', [AdminClassController::class, 'store'])->name('classes.store');
        Route::post('/classes/{id}/update', [AdminClassController::class, 'update'])->name('classes.update');
        Route::post('/classes/{id}/delete', [AdminClassController::class, 'destroy'])->name('classes.delete');
        Route::post('/classes/{id}/plot-homeroom', [AdminClassController::class, 'plotHomeroom'])->name('classes.plot-homeroom');
        Route::post('/classes/plot-student', [AdminClassController::class, 'plotStudent'])->name('classes.plot-student');
        
        // Major Management
        Route::post('/majors/store', [AdminClassController::class, 'storeMajor'])->name('majors.store');
        Route::post('/majors/{id}/update', [AdminClassController::class, 'updateMajor'])->name('majors.update');
        Route::post('/majors/{id}/delete', [AdminClassController::class, 'destroyMajor'])->name('majors.delete');
        
        // Settings & Audits
        Route::get('/settings', [AdminConfigController::class, 'settings'])->name('settings');
        
        // Backups
        Route::post('/backup', [AdminConfigController::class, 'triggerBackup'])->name('backup');
        Route::post('/restore', [AdminConfigController::class, 'triggerRestore'])->name('restore');
    });
});
