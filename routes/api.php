<?php

use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('resetPassword', [UserController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-service', [ServiceController::class, 'create']);
    Route::get('/service-schema', [ServiceController::class, 'getSchema']);
    Route::post('/update-service/{id}', [ServiceController::class, 'update']);
});
