<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Motorinci\BrandController;
use App\Http\Controllers\Motorinci\CategoryController;


Route::middleware('auth:sanctum')->prefix('motorinci')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
});