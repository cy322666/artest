<?php

use App\Http\Controllers\ShowController;
use Illuminate\Http\Request;
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

Route::post('showings/{lead_id}', [ShowController::class, 'create']);

Route::patch('showings/{lead_id}/{show:id}', [ShowController::class, 'update']);

Route::get('showings/{lead_id}', [ShowController::class, 'list']);
