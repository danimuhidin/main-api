<?php

use App\Http\Controllers\Api\GenerateController;
use App\Http\Controllers\Motorinci\AvailableColorController;
use App\Http\Controllers\Motorinci\BrandController;
use App\Http\Controllers\Motorinci\CategoryController;
use App\Http\Controllers\Motorinci\ColorController;
use App\Http\Controllers\Motorinci\FeatureItemController;
use App\Http\Controllers\Motorinci\FrontController;
use App\Http\Controllers\Motorinci\MotorController;
use App\Http\Controllers\Motorinci\MotorFeatureController;
use App\Http\Controllers\Motorinci\MotorImageController;
use App\Http\Controllers\Motorinci\MotorSpecificationController;
use App\Http\Controllers\Motorinci\ReviewController;
use App\Http\Controllers\Motorinci\SpecificationGroupController;
use App\Http\Controllers\Motorinci\SpecificationItemController;
use Illuminate\Support\Facades\Route;

Route::get('/motorinci-generate-image', [App\Http\Controllers\Motorinci\AiController::class, 'generateImage']);
Route::get('/motorinci-generate-imagw', [App\Http\Controllers\Motorinci\AiController::class, 'generateImagw']);
Route::get('/motorinci-generate', [App\Http\Controllers\Motorinci\AiController::class, 'generate']);
Route::get('/motorinci-gen/{id}', [App\Http\Controllers\Motorinci\AiController::class, 'gen']);

Route::middleware('auth:sanctum')->prefix('motorinci')->group(function () {
    Route::get('search-motors', [MotorController::class, 'search']);
    Route::get('motors/random', [MotorController::class, 'random']);
    Route::get('komparasi/{idsatu}/{iddua}', [MotorController::class, 'komparasi']);
    Route::get('front/home', [FrontController::class, 'home']);
    Route::get('brands/{id}/motors', [MotorController::class, 'getMotorsByBrand']);
    Route::get('categories/{id}/motors', [MotorController::class, 'getMotorsByCategory']);
    Route::post('ai', [App\Http\Controllers\Motorinci\AiController::class, 'ai']);

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
