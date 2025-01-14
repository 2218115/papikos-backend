<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\UlasanController;

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
    Route::post('', [KosController::class, 'create_kos'])->middleware('auth:sanctum');
    Route::get('', [KosController::class, 'get_all_kos']);
    Route::get('analitik', [KosController::class, 'get_analitik_data'])->middleware('auth:sanctum,hasRole:ADMIN');
    Route::get('analitik-pemilik', [KosController::class, 'get_analitik_data_pemilik'])->middleware('auth:sanctum,hasRole:PEMILIK_KOS');
    Route::post('{id}/approve', [KosController::class, 'approve'])->middleware('auth:sanctum')->middleware('hasRole:ADMIN');
    Route::post('{id}/reject', [KosController::class, 'reject'])->middleware('auth:sanctum')->middleware('hasRole:ADMIN');
    Route::post('{id}/suspend', [KosController::class, 'suspend'])->middleware('auth:sanctum')->middleware('hasRole:ADMIN');
    Route::get('form/init', [KosController::class, 'get_form_init']);
    Route::get('{id}', [KosController::class, 'get_detail_kos_data']);
});

Route::prefix("booking")->group(function () {
    Route::get("", [BookingController::class, 'get_all_my_booking'])->middleware('auth:sanctum');
    Route::get("pemilik", [BookingController::class, 'get_all_my_kos_booking'])->middleware('auth:sanctum');
    Route::get("get-list-init", [BookingController::class, 'get_list_init'])->middleware('auth:sanctum');
    // Route::get("", [BookingController::class, 'get_booking_by_kos']);
    Route::post("", [BookingController::class, 'add_booking'])->middleware('auth:sanctum');
    Route::post("/approve/{id}", [BookingController::class, 'approve_booking'])->middleware('auth:sanctum');
    Route::post("/reject/{id}", [BookingController::class, 'reject_booking'])->middleware('auth:sanctum');
    Route::post("/done/{id}", [BookingController::class, 'done_booking'])->middleware('auth:sanctum');
    Route::post("/cancel/{id}", [BookingController::class, 'cancel_booking'])->middleware('auth:sanctum');
    Route::get("{id}", [BookingController::class, 'get_booking_detail'])->middleware('auth:sanctum');
});

Route::prefix("ulasan")->group(function () {
    Route::post('', [UlasanController::class, 'add_ulasan'])->middleware('auth:sanctum');
    Route::get('', [UlasanController::class, 'get_all_ulasan']);
});

// storage
Route::get('/private/{path}/{filename}', [FileController::class, 'get_private_file'])->middleware('auth:sanctum');
