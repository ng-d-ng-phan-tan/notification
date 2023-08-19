<?php

use App\Http\Controllers\FcmController;
use App\Http\Controllers\GoogleDriveController;
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
Route::middleware(['cors'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/test', function () {
        return "Notification OK";
    });
    Route::post('send', [MailController::class, 'send']);
    Route::post('template', [MailController::class, 'addTemplate']);
    Route::post('upload-file', [GoogleDriveController::class, 'uploadFile'])->name('upload.file');
    Route::get('fcm',[FcmController::class,'index']);
    Route::post('save-token', [FcmController::class, 'saveToken'])->name('save-token');
    Route::post('send-notification', [FcmController::class, 'sendNotification'])->name('send.notification');
    Route::get('notification/{user_id}', [FcmController::class, 'getNotification'])->name('get.notification');
    Route::get('total-unread-notification/{user_id}', [FcmController::class, 'totalUnreadNotification'])->name('total.unread.notification');
    Route::put('read-notification/{user_id}', [FcmController::class, 'readNotification'])->name('read.notification');
});
