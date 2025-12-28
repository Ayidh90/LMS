<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\TrackController as AdminTrackController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\RoleSelectionController;
use Modules\Roles\Controllers\RoleController;
use Modules\Roles\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/')
    ->middleware(\App\Http\Middleware\RedirectToDashboard::class)
    ->name('welcome');

Route::post('/direction', [DirectionController::class, 'setDirection'])->name('direction.set');
Route::post('/locale', [LocaleController::class, 'setLocale'])->name('locale.set');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    // Password Reset Routes
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Role Selection (for users with multiple roles)
    Route::get('/role-selection', [RoleSelectionController::class, 'index'])->name('role-selection');
    Route::post('/role-selection', [RoleSelectionController::class, 'store'])->name('role-selection.store');
    Route::get('/role-selection/switch', [RoleSelectionController::class, 'switch'])->name('role-selection.switch');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
| Note: All admin routes require:
|   - Super admin: always allowed
|   - Regular admin: user->is_admin == 1 AND role->is_admin == 1
|--------------------------------------------------------------------------
*/

// Stop impersonating - must be outside role:admin middleware to allow access when impersonating
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/users/stop-impersonating', [AdminUserController::class, 'stopImpersonating'])->name('users.stop-impersonating');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::middleware('permission:dashboard.admin')
        ->get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');
    
    // Users Management
    Route::middleware('permission:users.manage')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::post('/users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');
        Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    });
    
    // Roles and Permissions
    Route::middleware('permission:roles.manage')
        ->resource('roles', RoleController::class);
    
    Route::middleware('permission:permissions.manage')
        ->resource('permissions', PermissionController::class)
        ->except(['create', 'edit', 'show']);
    
    // Settings
    Route::middleware('permission:settings.view')
        ->get('/settings', [AdminSettingsController::class, 'index'])
        ->name('settings.index');
    
    Route::middleware('permission:settings.edit')
        ->match(['put', 'post'], '/settings', [AdminSettingsController::class, 'update'])
        ->name('settings.update');
    
    // Programs Management
    Route::middleware('permission:programs.manage')
        ->resource('programs', AdminProgramController::class);
    
    // Tracks Management
    Route::middleware('permission:tracks.manage')->group(function () {
        Route::post('tracks/{track}/courses', [AdminTrackController::class, 'addCourses'])
            ->name('tracks.add-courses');
        Route::delete('tracks/{track}/courses/{course}', [AdminTrackController::class, 'removeCourse'])
            ->name('tracks.remove-course');
        Route::resource('tracks', AdminTrackController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::middleware('permission:dashboard.instructor')
        ->get('/dashboard', [InstructorDashboardController::class, 'index'])
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::middleware('permission:dashboard.student')
        ->get('/dashboard', [StudentDashboardController::class, 'index'])
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Load Module Routes
|--------------------------------------------------------------------------
*/

require base_path('Modules/Courses/routes.php');
