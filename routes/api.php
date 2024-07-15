<?php

use App\Http\Controllers\DepositUpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::match(['get', 'post'], DepositUpdateController::class)->name('deposit-callback');