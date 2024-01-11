<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationController;

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
    Route::prefix('painel')->group(function () {
        Route::prefix('organizations')->group(function () {
            Route::get('/', [OrganizationController::class, 'get']);
            Route::get('{organization}', [OrganizationController::class, 'getById']);
            Route::post('create', [OrganizationController::class, 'create']);
            Route::put('update/{organization}', [OrganizationController::class, 'update']);
            Route::put('delete/{organization}', [OrganizationController::class, 'delete']);
        });
    });