<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
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
use Modules\Roles\Controllers\RoleController;
use Modules\Roles\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return match(true) {
            $user->isAdmin() => redirect()->route('admin.dashboard'),
            $user->isInstructor() => redirect()->route('instructor.dashboard'),
            $user->isStudent() => redirect()->route('student.dashboard'),
            default => redirect()->route('profile.show'),
        };
    }
    return redirect()->route('login');
})->name('welcome');

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
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Non-Course Related)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::middleware('permission:dashboard.admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });
    
    // Users Management
    Route::middleware('permission:users.manage')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    });
    
    // Roles and Permissions
    Route::middleware('permission:roles.manage')->group(function () {
        Route::resource('roles', RoleController::class);
    });
    Route::middleware('permission:permissions.manage')->group(function () {
        Route::resource('permissions', PermissionController::class)->except(['create', 'edit', 'show']);
    });
    
    // Settings
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    });
    Route::middleware('permission:settings.edit')->group(function () {
        Route::match(['put', 'post'], '/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    });
    
    // Programs Management
    Route::middleware('permission:programs.manage')->group(function () {
        Route::resource('programs', AdminProgramController::class);
    });
    
    // Tracks Management
    Route::middleware('permission:tracks.manage')->group(function () {
        Route::post('tracks/{track}/courses', [AdminTrackController::class, 'addCourses'])->name('tracks.add-courses');
        Route::delete('tracks/{track}/courses/{course}', [AdminTrackController::class, 'removeCourse'])->name('tracks.remove-course');
        Route::resource('tracks', AdminTrackController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Instructor Routes (Non-Course Related)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::middleware('permission:dashboard.instructor')->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    });
});

/*
|--------------------------------------------------------------------------
| Student Routes (Non-Course Related)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::middleware('permission:dashboard.student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    });
});

/*
|--------------------------------------------------------------------------
| Load Module Routes
|--------------------------------------------------------------------------
*/

require base_path('Modules/Courses/routes.php');
