<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;

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
    
    Route::get('states', [StateController::class, 'get']);
    Route::get('citys', [CityController::class, 'get']);

    Route::prefix('painel')->group(function () {
        Route::prefix('organizations')->group(function () {
            Route::get('/', [OrganizationController::class, 'get']);
            Route::get('{id}', [OrganizationController::class, 'getById']);
            Route::post('create', [OrganizationController::class, 'create']);
            Route::put('update/{organization}', [OrganizationController::class, 'update']);
            Route::put('delete/{id}', [OrganizationController::class, 'delete']);
        });
        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyController::class, 'get']);
            Route::get('{id}', [CompanyController::class, 'getById']);
            Route::post('create', [CompanyController::class, 'create']);
            Route::put('update/{company}', [CompanyController::class, 'update']);
            Route::put('delete/{id}', [CompanyController::class, 'delete']);
        });
        Route::prefix('serviceCategories')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'get']);
            Route::get('{id}', [ServiceCategoryController::class, 'getById']);
            Route::post('create', [ServiceCategoryController::class, 'create']);
            Route::put('update/{serviceCategory}', [ServiceCategoryController::class, 'update']);
            Route::put('delete/{id}', [ServiceCategoryController::class, 'delete']);
        });
        Route::prefix('services')->group(function () {
            Route::get('/', [ServiceController::class, 'get']);
            Route::get('{id}', [ServiceController::class, 'getById']);
            Route::post('create', [ServiceController::class, 'create']);
            Route::put('update/{service}', [ServiceController::class, 'update']);
            Route::put('delete/{id}', [ServiceController::class, 'delete']);
        });
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'get']);
            Route::get('{id}', [EmployeeController::class, 'getById']);
            Route::post('create', [EmployeeController::class, 'create']);
            Route::put('update/{employee}', [EmployeeController::class, 'update']);
            Route::put('delete/{id}', [EmployeeController::class, 'delete']);
        });
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'get']);
            Route::get('{id}', [UserController::class, 'getById']);
            Route::post('create', [UserController::class, 'create']);
            Route::put('update/{user}', [UserController::class, 'update']);
            Route::put('delete/{id}', [UserController::class, 'delete']);
        });
        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'get']);
            Route::get('{id}', [ClientController::class, 'getById']);
            Route::post('create', [ClientController::class, 'create']);
            Route::put('update/{client}', [ClientController::class, 'update']);
            Route::put('delete/{id}', [ClientController::class, 'delete']);
        });
    });