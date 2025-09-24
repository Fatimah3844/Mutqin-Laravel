<?php
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SheikhController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ReportController;



Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware([
    'web', // 
    EnsureFrontendRequestsAreStateful::class,
    'auth:sanctum'
])->group(function () {
    Route::get('/user', function () {
        return auth()->user();
    });
});

//email/passowrd
Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');

// Google OAuth
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);


Route::middleware(['auth:sanctum', 'role:student'])->prefix('students')->group(function () {
    // Profile
    Route::put('/profile', [StudentController::class, 'updateProfile']);
    Route::delete('/profile', [StudentController::class, 'deleteProfile']);
    Route::get('/profile/{username}', [StudentController::class, 'viewProfile']);
    Route::get('/search', [StudentController::class, 'searchProfiles']);

    // Progress
    Route::get('/progress', [StudentController::class, 'progress']);
    Route::get('/progress/all', [StudentController::class, 'progressAll']);

    // Sessions
    Route::post('/sessions/book', [SessionController::class, 'bookSession']);
    Route::post('/sessions/attend', [SessionController::class, 'attendSession']);
    Route::get('/sessions', [SessionController::class, 'listSessions']);
    Route::delete('/sessions/cancel', [SessionController::class, 'cancelSession']);
});


Route::middleware(['auth:sanctum', 'role:sheikh'])->prefix('sheikhs')->group(function (){
    // Sessions
    Route::post('/sessions', [SheikhController::class, 'createSession']);
    Route::get('/sessions/{session_id}', [SheikhController::class, 'getSession']);
    Route::get('/sessions/sheikh/{sheikh_id}', [SheikhController::class, 'getSheikhSessions']); //

    // Students
    Route::get('/students/{sheikh_id}', [StudentController::class, 'getStudents']);
    Route::get('/sheikhs/students/{student_id}/progress', [StudentController::class, 'getStudentProgress']);
    Route::post('/students/{student_id}/points', [StudentController::class, 'assignPoints']);
    Route::put('/students/{student_id}/points', [StudentController::class, 'updatePoints']);
    Route::post('/students/{student_id}/pages', [StudentController::class, 'recordPages']);

    // Badges
    Route::get('/badges', [BadgeController::class, 'getAllBadges']);
    Route::post('/badges', [BadgeController::class, 'createBadge']);
    Route::get('/badges/{badge_id}', [BadgeController::class, 'getBadge']);
    Route::post('/badges/assign', [BadgeController::class, 'assignBadge']);

    // Reports
    Route::post('/reports', [ReportController::class, 'sendReport']);

   

});