<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ModController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/docs', [SwaggerController::class, 'docs'])
    ->name('l5-swagger.default.docs')
    ->middleware('swagger.config');

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return response()->json(['message' => 'Welcome on board, authorised user']);
});

Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::post('register/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth:sanctum'])->name('verification.send');

Route::get('register/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::get('games', [GameController::class, 'browse']);
Route::get('games/{game}', [GameController::class, 'read']);
Route::middleware(['mod.belongs.to.game'])->prefix('games/{game}')->group(function () {
    Route::get('mods', [ModController::class, 'browse']);
    Route::get('mods/{mod}', [ModController::class, 'read']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('user', [UserController::class, 'read'])->name('user.read');
    Route::delete('user', [UserController::class, 'delete'])->name('user.delete');

    Route::post('games', [GameController::class, 'create']);
    Route::put('games/{game}', [GameController::class, 'update']);
    Route::delete('games/{game}', [GameController::class, 'delete']);

    Route::middleware(['mod.belongs.to.game'])->prefix('games/{game}')->group(function () {
        Route::post('mods', [ModController::class, 'create']);
        Route::put('mods/{mod}', [ModController::class, 'update']);
        Route::delete('mods/{mod}', [ModController::class, 'delete']);
    });
});
