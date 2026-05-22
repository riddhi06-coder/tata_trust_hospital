<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PreventBackHistoryMiddleware;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\PermissionController;

use App\Http\Controllers\Backend\HomeBannerController;
use App\Http\Controllers\Backend\ShortIntroductionController;
use App\Http\Controllers\Backend\HomeSpecialitiesController;
use App\Http\Controllers\Backend\HomeFacilitiesController;
use App\Http\Controllers\Backend\HomeTeamController;
use App\Http\Controllers\Backend\HomeTestimonialsController;


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

    // Backward-compat alias
    Route::get('/change-password', [LoginController::class, 'showForgotPasswordForm'])->name('admin.changepassword');
});

// ----------------------
// Authenticated routes
// ----------------------
Route::middleware('auth')->group(function () {
    Route::get('/',          [LoginController::class, 'dashboard'])->name('admin.home');
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');

    Route::post('/update-password', [LoginController::class, 'updatePassword'])->name('admin.updatepassword');

    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('admin.logout');

    // ---- Roles ----
    Route::get('roles',                [RoleController::class, 'index'])->middleware('permission:roles.view')->name('admin.roles.index');
    Route::get('roles/create',         [RoleController::class, 'create'])->middleware('permission:roles.create')->name('admin.roles.create');
    Route::post('roles',               [RoleController::class, 'store'])->middleware('permission:roles.create')->name('admin.roles.store');
    Route::get('roles/{role}/edit',    [RoleController::class, 'edit'])->middleware('permission:roles.edit')->name('admin.roles.edit');
    Route::put('roles/{role}',         [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('admin.roles.update');
    Route::delete('roles/{role}',      [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('admin.roles.destroy');

    // ---- Users ----
    Route::get('users',                [UserController::class, 'index'])->middleware('permission:users.view')->name('admin.users.index');
    Route::get('users/create',         [UserController::class, 'create'])->middleware('permission:users.create')->name('admin.users.create');
    Route::post('users',               [UserController::class, 'store'])->middleware('permission:users.create')->name('admin.users.store');
    Route::get('users/{user}/edit',    [UserController::class, 'edit'])->middleware('permission:users.edit')->name('admin.users.edit');
    Route::put('users/{user}',         [UserController::class, 'update'])->middleware('permission:users.edit')->name('admin.users.update');
    Route::delete('users/{user}',      [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('admin.users.destroy');

    // ---- Permissions (per-role matrix) ----
    Route::get('permissions',                  [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('admin.permissions.index');
    Route::get('permissions/{role}/edit',      [PermissionController::class, 'edit'])->middleware('permission:permissions.assign')->name('admin.permissions.edit');
    Route::put('permissions/{role}',           [PermissionController::class, 'update'])->middleware('permission:permissions.assign')->name('admin.permissions.update');

    // ---- Permission catalog (add new permissions when new tabs appear) ----
    Route::get('permissions-catalog',                          [PermissionController::class, 'manage'])->middleware('permission:permissions.assign')->name('admin.permissions.manage');
    Route::get('permissions-catalog/create',                   [PermissionController::class, 'createPermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.create');
    Route::post('permissions-catalog',                         [PermissionController::class, 'storePermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.store');
    Route::get('permissions-catalog/{permission}/edit',        [PermissionController::class, 'editPermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.edit');
    Route::put('permissions-catalog/{permission}',             [PermissionController::class, 'updatePermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.update');
    Route::delete('permissions-catalog/{permission}',          [PermissionController::class, 'destroyPermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.destroy');
});



// ----------------------
// 🔹 Backend (Admin Panel) Routes
// ----------------------
Route::prefix('')
    ->middleware(['auth:web', PreventBackHistoryMiddleware::class])
    ->group(function () {


            // Home slider
            Route::resource('banner-details', HomeBannerController::class);
            Route::resource('short-introduction', ShortIntroductionController::class);
            Route::resource('home-specialities', HomeSpecialitiesController::class);
            Route::resource('manage-facilities', HomeFacilitiesController::class);
            Route::resource('home-team', HomeTeamController::class);
            Route::resource('manage-testimonials', HomeTestimonialsController::class);

    
    });