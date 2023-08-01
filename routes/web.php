<?php

use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\FcmController;
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
    return view('welcome');
});

Route::post('send', [MailController::class, 'send']);
Route::post('template', [MailController::class, 'addTemplate']);

Route::post('upload-file', [GoogleDriveController::class, 'uploadFile'])->name('upload.file');
Route::get('fcm',[FcmController::class,'index']);
