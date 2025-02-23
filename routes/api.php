<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User Control Routes
// Route::get('news', [AuthController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login'])->name('login');
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/sendMail', [AuthController::class, 'sendResetLink']);
Route::post('/reset',[AuthController::class, 'reset'])->name('password.reset');

// Article Routes
Route::get('/articles',[ArticleController::class, 'index'])->middleware('auth:sanctum');
Route::get('/showArticle/{articleId}', [ArticleController::class, 'show'])->middleware('auth:sanctum');

// User Preference Routes
Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user-preferences/{userId}',[UserPreferenceController::class, 'show']);
    Route::post('/user-preferences', [UserPreferenceController::class, 'store']);
    Route::get('/personalized-feed/{userId}', [UserPreferenceController::class, 'personalizedFeed']);
});
