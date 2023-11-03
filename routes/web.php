<?php

use App\Http\Livewire\CreateForm;
use App\Http\Livewire\UpdateForm;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('form/update/{show:id}', UpdateForm::class);

Route::get('form/create', CreateForm::class);
