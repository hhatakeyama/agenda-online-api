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
    // Send
    Route::get('schedule/{companyId}/{email}/{scheduleId}', [MailController::class, 'schedule']);
    Route::get('confirmation/email/{scheduleId}', [MailController::class, 'confirmationEmail']);
    Route::get('confirmation/sms/{scheduleId}', [MailController::class, 'confirmationSms']);
    Route::get('test/twilio/whatsapp/{scheduleId}', [MailController::class, 'twilioWhatsapp']);

    // Previews
    Route::get('preview/client-registration', [MailController::class, 'previewClientRegistration']);
    Route::get('preview/user-registration', [MailController::class, 'previewUserRegistration']);
    Route::get('preview/employee-registration', [MailController::class, 'previewEmployeeRegistration']);
    Route::get('preview/schedule-registration', [MailController::class, 'previewScheduleRegistration']);
    Route::get('preview/schedule-confirmation', [MailController::class, 'previewScheduleConfirmation']);
    Route::get('preview/schedule-reminder', [MailController::class, 'previewScheduleReminder']);
    Route::get('preview/schedule-cancelation', [MailController::class, 'previewScheduleCancelation']);
    Route::get('preview/reschedule', [MailController::class, 'previewReschedule']);
});
