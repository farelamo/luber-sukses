<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrochureController;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::resource('product', ProductController::class);
Route::resource('brand', BrandController::class);
Route::resource('brochure', BrochureController::class);
// Route::resource('career', CareerController::class);
// Route::resource('service', ServiceController::class);