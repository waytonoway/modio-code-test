<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'read'])->name('user.read');
    Route::delete('user', [UserController::class, 'delete'])->name('user.delete');

    Route::post('games', [GameController::class, 'create']);
    Route::put('games/{game}', [GameController::class, 'update']);
    Route::delete('games/{game}', [GameController::class, 'delete']);
    Route::get('games', [GameController::class, 'browse']);
    Route::get('games/{game}', [GameController::class, 'read']);
});
