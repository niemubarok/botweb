<?php

use App\Http\Controllers\agController;
use App\Http\Controllers\regController;
use App\Http\Controllers\tesController;
use App\Http\Controllers\twilioController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('amio', [amioController::class, '__invoke']);
// Route::post('bomsms', [smsController::class, '__invoke']);
// Route::post('mb', [mbController::class, '__invoke']);
Route::post('reg', [regController::class, '__invoke']);
Route::post('tes', [tesController::class, '__invoke']);
Route::post('twilio', [twilioController::class, 'responseMessage']);
Route::post('totwilio', [twilioController::class, 'createMessage']);
Route::post('ag', [agController::class, 'createMessage']);
// Route::post('ibm', [ibmController::class, '__invoke']);
