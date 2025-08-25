<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Motorinci\BrandController;
use App\Http\Controllers\Motorinci\CategoryController;
use App\Http\Controllers\Motorinci\ColorController;
use App\Http\Controllers\Motorinci\FeatureItemController;

Route::middleware('auth:sanctum')->prefix('motorinci')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('colors', ColorController::class);
    Route::apiResource('feature-items', FeatureItemController::class);
});