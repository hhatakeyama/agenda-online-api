<?php

use App\Http\Controllers\MailController;
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

Route::get('/', function () {
    return "Hello World";
});

Route::prefix('mail')->group(function () {
    Route::get('schedule/{companyId}/{email}/{scheduleId}', [MailController::class, 'schedule']);
    Route::get('confirmation/email/{scheduleId}', [MailController::class, 'confirmationEmail']);
    Route::get('confirmation/sms/{scheduleId}', [MailController::class, 'confirmationSms']);
});
