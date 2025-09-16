<?php

use App\Http\Controllers\Api\GenerateController;
use App\Http\Controllers\Motorinci\AvailableColorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Motorinci\BrandController;
use App\Http\Controllers\Motorinci\CategoryController;
use App\Http\Controllers\Motorinci\ColorController;
use App\Http\Controllers\Motorinci\FeatureItemController;
use App\Http\Controllers\Motorinci\MotorController;
use App\Http\Controllers\Motorinci\MotorFeatureController;
use App\Http\Controllers\Motorinci\MotorImageController;
use App\Http\Controllers\Motorinci\MotorSpecificationController;
use App\Http\Controllers\Motorinci\ReviewController;
use App\Http\Controllers\Motorinci\SpecificationGroupController;
use App\Http\Controllers\Motorinci\SpecificationItemController;

Route::get('/motorinci-generate', [App\Http\Controllers\Motorinci\AiController::class, 'generate']);
Route::get('/motorinci-gen/{id}', [App\Http\Controllers\Motorinci\AiController::class, 'gen']);

Route::middleware('auth:sanctum')->prefix('motorinci')->group(function () {
    Route::apiResource('generate', GenerateController::class);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('colors', ColorController::class);
    Route::apiResource('features', FeatureItemController::class);
    Route::apiResource('specification-groups', SpecificationGroupController::class);
    Route::apiResource('specification-items', SpecificationItemController::class);
    Route::apiResource('available-colors', AvailableColorController::class);
    Route::apiResource('motors', MotorController::class);
    Route::apiResource('motor-features', MotorFeatureController::class);
    Route::apiResource('motor-images', MotorImageController::class);
    Route::apiResource('motor-specifications', MotorSpecificationController::class);
    Route::apiResource('reviews', ReviewController::class);
});