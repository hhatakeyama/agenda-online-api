<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceCategoryController;

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
        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyController::class, 'get']);
            Route::get('{company}', [CompanyController::class, 'getById']);
            Route::post('create', [CompanyController::class, 'create']);
            Route::put('update/{company}', [CompanyController::class, 'update']);
            Route::put('delete/{company}', [CompanyController::class, 'delete']);
        });
         Route::prefix('serviceCategories')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'get']);
            Route::get('{serviceCategory}', [ServiceCategoryController::class, 'getById']);
            Route::post('create', [ServiceCategoryController::class, 'create']);
            Route::put('update/{serviceCategory}', [ServiceCategoryController::class, 'update']);
            Route::put('delete/{serviceCategory}', [ServiceCategoryController::class, 'delete']);
        });
    });