<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PreferencesController;

use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'userProfile']);
Route::get('/news', [NewsController::class, 'getNews']);

Route::post('/preferences', [PreferencesController::class, 'savePreferences']);
Route::get('/preferences', [PreferencesController::class, 'getPreferences']);
Route::get('/personalized-news', [NewsController::class, 'getPersonalizedNews']);
});
