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
        Route::post('/', [ClientController::class, 'create']);
    });
    Route::prefix('companies')->group(function () {
        Route::get('{id}', [CompanyController::class, 'getAllDataFromCompany']);
    });
    Route::prefix('organizations')->group(function () {
        Route::get('{slug}', [OrganizationController::class, 'getCompaniesFromOrganization']);
    });
    Route::prefix('schedules')->group(function () {
        Route::get('unavailables', [ScheduleController::class, 'unavailables']);
        Route::post('/', [ScheduleController::class, 'create']);
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
            Route::patch('{client}', [ClientController::class, 'update']);
            Route::post('{client}/picture', [ClientController::class, 'updatePicture']);
        });
        Route::prefix('schedules')->group(function () {
            Route::get('client/{client_id}', [ScheduleController::class, 'getSheduleFromClient']);
            Route::patch('update', [ScheduleController::class, 'update']);
            Route::delete('{id}', [ScheduleController::class, 'delete']);
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
            Route::post('/', [AdminClientController::class, 'create']);
            Route::patch('{client}', [AdminClientController::class, 'update']);
            Route::post('{client}/picture', [AdminClientController::class, 'updatePicture']);
            Route::delete('{id}', [AdminClientController::class, 'delete']);
        });
        Route::prefix('companies')->group(function () {
            Route::get('/', [AdminCompanyController::class, 'get']);
            Route::get('{id}', [AdminCompanyController::class, 'getById']);
            Route::post('/', [AdminCompanyController::class, 'create']);
            Route::patch('{company}', [AdminCompanyController::class, 'update']);
            Route::post('{company}/services', [AdminCompanyController::class, 'createService']);
            Route::patch('{company}/services', [AdminCompanyController::class, 'updateService']);
            Route::post('{company}/thumb', [AdminCompanyController::class, 'updateThumb']);
            Route::delete('{id}', [AdminCompanyController::class, 'delete']);
            Route::delete('{company}/services/{id}', [AdminCompanyController::class, 'deleteService']);
        });
        Route::prefix('employees')->group(function () {
            Route::get('/', [AdminEmployeeController::class, 'get']);
            Route::get('{id}', [AdminEmployeeController::class, 'getById']);
            Route::post('/', [AdminEmployeeController::class, 'create']);
            Route::patch('{employee}', [AdminEmployeeController::class, 'update']);
            Route::post('{employee}/picture', [AdminEmployeeController::class, 'updatePicture']);
            Route::delete('{id}', [AdminEmployeeController::class, 'delete']);
        });
        Route::prefix('organizations')->group(function () {
            Route::get('/', [AdminOrganizationController::class, 'get']);
            Route::get('{id}', [AdminOrganizationController::class, 'getById']);
            Route::post('/', [AdminOrganizationController::class, 'create']);
            Route::patch('{organization}', [AdminOrganizationController::class, 'update']);
            Route::delete('{id}', [AdminOrganizationController::class, 'delete']);
        });
        Route::prefix('service-categories')->group(function () {
            Route::get('/', [AdminServiceCategoryController::class, 'get']);
            Route::get('{id}', [AdminServiceCategoryController::class, 'getById']);
            Route::post('/', [AdminServiceCategoryController::class, 'create']);
            Route::patch('{serviceCategory}', [AdminServiceCategoryController::class, 'update']);
            Route::delete('{id}', [AdminServiceCategoryController::class, 'delete']);
        });
        Route::prefix('schedules')->group(function () {
            Route::get('/', [AdminScheduleController::class, 'get']);
            Route::get('calendar', [AdminScheduleController::class, 'getCalendar']);
            Route::get('{id}', [AdminScheduleController::class, 'getById']);
            Route::post('/', [AdminScheduleController::class, 'create']);
            Route::patch('{schedule}', [AdminScheduleController::class, 'update']);
            Route::delete('delete/{id}', [AdminScheduleController::class, 'delete']);
        });
        Route::prefix('services')->group(function () {
            Route::get('/', [AdminServiceController::class, 'get']);
            Route::get('{id}', [AdminServiceController::class, 'getById']);
            Route::post('/', [AdminServiceController::class, 'create']);
            Route::patch('{service}', [AdminServiceController::class, 'update']);
            Route::delete('{id}', [AdminServiceController::class, 'delete']);
        });
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'get']);
            Route::get('{id}', [AdminUserController::class, 'getById']);
            Route::post('/', [AdminUserController::class, 'create']);
            Route::patch('{user}', [AdminUserController::class, 'update']);
            Route::post('{user}/picture', [AdminUserController::class, 'updatePicture']);
            Route::delete('{id}', [AdminUserController::class, 'delete']);
        });
    });
});
