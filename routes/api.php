<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\KosController;

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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'user_login']);
    Route::post('register', [AuthController::class, 'user_register']);
    Route::post('register/pemilik-kos', [AuthController::class, 'pemilik_kos_register']);
    Route::get('me', [AuthController::class, 'current_user'])->middleware('auth:sanctum');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('kos')->group(function () {
    Route::get('/analitik', [KosController::class, 'get_analitik_data'])->middleware('auth:sanctum');
    Route::post('', [KosController::class, 'create_kos'])->middleware('auth:sanctum');
    Route::get('', [KosController::class, 'get_all_kos']);
    Route::get('{id}', [KosController::class, 'get_detail_kos_data']);
    Route::get('{id}/approve', [KosController::class, 'approve'])->middleware('hasRole:ADMIN');
    Route::get('{id}/reject', [KosController::class, 'reject'])->middleware('hasRole:ADMIN');
});


// storage
Route::get('/private/{path}/{filename}', [FileController::class, 'get_private_file'])->middleware('auth:sanctum');
