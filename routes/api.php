<?php

use App\Http\Controllers\Email_otp\Email_otpController;
use App\Http\Controllers\Email_otp\Resend_otpController;
use App\Http\Controllers\Email_otp\Verify_otpController;
use App\Http\Controllers\Language\LanguageController;
use App\Http\Controllers\Map\ConsiDetailsController;
use App\Http\Controllers\Map\ConstituenciesController;
use App\Http\Controllers\Map\MapController;
use App\Http\Controllers\User\UserController;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/change-language',[LanguageController::class,'changeLanguage']);
Route::post('/Email_otp',[Email_otpController::class,'Email_otp']);
Route::post('/Verify_otp',[Verify_otpController::class,'Verify_otp']);
Route::post('/Resend_otp',[Resend_otpController::class,'Resend_otp']);
Route::post('/User_name',[UserController::class,'User']);
Route::post('/profile',[ProfileController::class,'profile']);
Route::post('/Map',[MapController::class,'map']);
Route::post('/Search',[ConstituenciesController::class,'search']);
Route::post('/considetails',[ConsiDetailsController::class,'constituencyDetails']);
