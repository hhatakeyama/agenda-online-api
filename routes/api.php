<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\OrganizationController as AdminOrganizationController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceCategoryController as AdminServiceCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StateController;

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

Route::post('login-client', [AuthController::class, 'login']);

Route::prefix('states')->group(function () {
    Route::get('/', [StateController::class, 'get']);
    Route::get('/{state_id}/cities', [CityController::class, 'getByStateId']);
});
Route::get('cities', [CityController::class, 'get']);

Route::prefix('site')->group(function () {
    // Public routes
    Route::prefix('clients')->group(function () {
        Route::post('create', [ClientController::class, 'create']);
    });
    Route::prefix('companies')->group(function () {
        Route::get('{id}', [CompanyController::class, 'getAllDataFromCompany']);
    });
    Route::prefix('organizations')->group(function () {
        Route::get('{slug}', [OrganizationController::class, 'getCompaniesFromOrganization']);
    });
    Route::prefix('schedules')->group(function () {
        Route::get('unavailables', [ScheduleController::class, 'unavailables']);
        Route::post('create', [ScheduleController::class, 'create']);
        Route::get('employees', [ScheduleController::class, 'getSheduleFromEmployee']);
        Route::get('sendSms', [ScheduleController::class, 'sendMessage']);
        Route::get('responseSms', [ScheduleController::class, 'responseMessage']);
        Route::get('confirmShedule', [ScheduleController::class, 'confirmationScheudleMessage']);
    });
    Route::prefix('schedules-from-employee')->group(function () {
        Route::get('/', [ScheduleController::class, 'getSchedulesFromEmployeesBeginningToday']);
        Route::get('{employee_id}', [ScheduleController::class, 'getSchedulesFromEmployeeBeginningToday']);
    });

    // Authentication required routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('me', [ClientController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::prefix('clients')->group(function () {
            Route::patch('update/{client}', [ClientController::class, 'update']);
            Route::post('update/{client}/picture', [ClientController::class, 'updatePicture']);
        });
        Route::prefix('schedules')->group(function () {
            Route::get('client/{client_id}', [ScheduleController::class, 'getSheduleFromClient']);
            Route::patch('update', [ScheduleController::class, 'update']);
            Route::delete('delete/{id}', [ScheduleController::class, 'delete']);
        });
    });
});

// Admin routes
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    // Authentication required routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('me', [AdminUserController::class, 'me']);
        Route::post('logout', [AdminAuthController::class, 'logout']);

        Route::prefix('clients')->group(function () {
            Route::get('/', [AdminClientController::class, 'get']);
            Route::get('{id}', [AdminClientController::class, 'getById']);
            Route::post('create', [AdminClientController::class, 'create']);
            Route::patch('update/{client}', [AdminClientController::class, 'update']);
            Route::post('update/{client}/picture', [AdminClientController::class, 'updatePicture']);
            Route::delete('delete/{id}', [AdminClientController::class, 'delete']);
        });
        Route::prefix('companies')->group(function () {
            Route::get('/', [AdminCompanyController::class, 'get']);
            Route::get('{id}', [AdminCompanyController::class, 'getById']);
            Route::post('create', [AdminCompanyController::class, 'create']);
            Route::patch('update/{company}', [AdminCompanyController::class, 'update']);
            Route::delete('delete/{id}', [AdminCompanyController::class, 'delete']);
        });
        Route::prefix('employees')->group(function () {
            Route::get('/', [AdminEmployeeController::class, 'get']);
            Route::get('{id}', [AdminEmployeeController::class, 'getById']);
            Route::post('create', [AdminEmployeeController::class, 'create']);
            Route::patch('update/{employee}', [AdminEmployeeController::class, 'update']);
            Route::delete('delete/{id}', [AdminEmployeeController::class, 'delete']);
        });
        Route::prefix('service-categories')->group(function () {
            Route::get('/', [AdminServiceCategoryController::class, 'get']);
            Route::get('{id}', [AdminServiceCategoryController::class, 'getById']);
            Route::post('create', [AdminServiceCategoryController::class, 'create']);
            Route::patch('update/{serviceCategory}', [AdminServiceCategoryController::class, 'update']);
            Route::delete('delete/{id}', [AdminServiceCategoryController::class, 'delete']);
        });
        Route::prefix('schedules')->group(function () {
            Route::get('/', [AdminScheduleController::class, 'get']);
            Route::get('{id}', [AdminScheduleController::class, 'getById']);
            Route::post('create', [AdminScheduleController::class, 'create']);
            Route::patch('update/{service}', [AdminScheduleController::class, 'update']);
            Route::delete('delete/{id}', [AdminScheduleController::class, 'delete']);
        });
        Route::prefix('services')->group(function () {
            Route::get('/', [AdminServiceController::class, 'get']);
            Route::get('{id}', [AdminServiceController::class, 'getById']);
            Route::post('create', [AdminServiceController::class, 'create']);
            Route::patch('update/{service}', [AdminServiceController::class, 'update']);
            Route::delete('delete/{id}', [AdminServiceController::class, 'delete']);
        });
        Route::prefix('organizations')->group(function () {
            Route::get('/', [AdminOrganizationController::class, 'get']);
            Route::get('{id}', [AdminOrganizationController::class, 'getById']);
            Route::post('create', [AdminOrganizationController::class, 'create']);
            Route::patch('update/{organization}', [AdminOrganizationController::class, 'update']);
            Route::delete('delete/{id}', [AdminOrganizationController::class, 'delete']);
        });
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'get']);
            Route::get('{id}', [AdminUserController::class, 'getById']);
            Route::post('create', [AdminUserController::class, 'create']);
            Route::patch('update/{user}', [AdminUserController::class, 'update']);
            Route::delete('delete/{id}', [AdminUserController::class, 'delete']);
        });
    });
});
