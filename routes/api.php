<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ScheduleController;

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

Route::post('login', [AuthController::class, 'login']);

Route::prefix('states')->group(function () {
    Route::get('/', [StateController::class, 'get']);
    Route::get('/{state_id}/cities', [CityController::class, 'getByStateId']);
});
Route::get('cities', [CityController::class, 'get']);
    
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('painel')->group(function () {
        Route::prefix('organizations')->group(function () {
            Route::get('/', [OrganizationController::class, 'get']);
            Route::get('{id}', [OrganizationController::class, 'getById']);
            Route::post('create', [OrganizationController::class, 'create']);
            Route::patch('update/{organization}', [OrganizationController::class, 'update']);
            Route::delete('delete/{id}', [OrganizationController::class, 'delete']);
        });
        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyController::class, 'get']);
            Route::get('{id}', [CompanyController::class, 'getById']);
            Route::post('create', [CompanyController::class, 'create']);
            Route::patch('update/{company}', [CompanyController::class, 'update']);
            Route::delete('delete/{id}', [CompanyController::class, 'delete']);
        });
        Route::prefix('service-categories')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'get']);
            Route::get('{id}', [ServiceCategoryController::class, 'getById']);
            Route::post('create', [ServiceCategoryController::class, 'create']);
            Route::patch('update/{serviceCategory}', [ServiceCategoryController::class, 'update']);
            Route::delete('delete/{id}', [ServiceCategoryController::class, 'delete']);
        });
        Route::prefix('services')->group(function () {
            Route::get('/', [ServiceController::class, 'get']);
            Route::get('{id}', [ServiceController::class, 'getById']);
            Route::post('create', [ServiceController::class, 'create']);
            Route::patch('update/{service}', [ServiceController::class, 'update']);
            Route::delete('delete/{id}', [ServiceController::class, 'delete']);
        });
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'get']);
            Route::get('{id}', [EmployeeController::class, 'getById']);
            Route::post('create', [EmployeeController::class, 'create']);
            Route::patch('update/{employee}', [EmployeeController::class, 'update']);
            Route::delete('delete/{id}', [EmployeeController::class, 'delete']);
        });
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'get']);
            Route::get('me', [UserController::class, 'me']);
            Route::get('{id}', [UserController::class, 'getById']);
            Route::post('create', [UserController::class, 'create']);
            Route::patch('update/{user}', [UserController::class, 'update']);
            Route::delete('delete/{id}', [UserController::class, 'delete']);
        });
        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'get']);
            Route::get('{id}', [ClientController::class, 'getById']);
            Route::post('create', [ClientController::class, 'create']);
            Route::patch('update/{client}', [ClientController::class, 'update']);
            Route::delete('delete/{id}', [ClientController::class, 'delete']);
        });
        Route::prefix('schedules')->group(function () {
            Route::get('employee/{employee_id}', [ScheduleController::class, 'getSheduleFromEmployee']);
            Route::post('client//{client_id}', [ScheduleController::class, 'getSheduleFromCliente']);
            Route::patch('create', [ScheduleController::class, 'create']);
            Route::patch('update/{client}', [ScheduleController::class, 'update']);
            Route::delete('delete/{id}', [ScheduleController::class, 'delete']);
        });
    });
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('site')->group(function () {
    Route::prefix('organizations')->group(function () {
        Route::get('{slug}', [OrganizationController::class, 'getCompaniesFromOrganization']);
    });
    Route::prefix('companies')->group(function () {
        Route::get('{id}', [CompanyController::class, 'getAllDataFromCompany']);
    });
    Route::prefix('schedulesFromEmployee')->group(function () {
        Route::get('{employee_id}', [ScheduleController::class, 'getSchedulesFromEmployeesBeginningToday']);
    });
    Route::prefix('schedules')->group(function () {
        Route::get('sendSms/{recipient}', [ScheduleController::class, 'sendMessage']);
        Route::get('confirmShedule', [ScheduleController::class, 'confirmationScheudleMessage']);
    });
});
