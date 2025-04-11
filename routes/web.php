<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/docs', [\L5Swagger\Http\Controllers\SwaggerController::class, 'docs'])
    ->name('l5-swagger.default.docs')
    ->middleware('swagger.config');

Route::get('/', function () {
    return view('welcome');
});
