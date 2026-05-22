<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// ----------------------
// Guest-only auth routes (login / register / forgot password)
// ----------------------
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login',  [LoginController::class, 'login'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('admin.authenticate');

    // Register
    Route::get('/register',  [LoginController::class, 'register'])->name('admin.register');
    Route::post('/register', [LoginController::class, 'authenticate_register'])->name('admin.register.authenticate');

    // Forgot password — request a reset link
    Route::get('/forgot-password',  [LoginController::class, 'showForgotPasswordForm'])->name('admin.password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('admin.password.email');

    // Reset password — clicked from email
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password',         [LoginController::class, 'resetPassword'])->name('admin.password.update');

    // Backward-compat alias: the old "forgot password" link still works
    Route::get('/change-password', [LoginController::class, 'showForgotPasswordForm'])->name('admin.changepassword');
});

// ----------------------
// Authenticated routes
// ----------------------
Route::middleware('auth')->group(function () {
    Route::get('/',          [LoginController::class, 'dashboard'])->name('admin.home');
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');

    // Change password (authenticated users)
    Route::post('/update-password', [LoginController::class, 'updatePassword'])->name('admin.updatepassword');

    // Logout
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('admin.logout');
});
