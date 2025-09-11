<?php
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SessionController;



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


Route::middleware('auth:sanctum')->prefix('students')->group(function () {
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