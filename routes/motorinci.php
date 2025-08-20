<?php

use App\Http\Controllers\Motorinci\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->prefix('motorinci')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});