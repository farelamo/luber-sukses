<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ServiceController;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::resource('product', ProductController::class);
Route::resource('career', CareerController::class);
Route::resource('service', ServiceController::class);