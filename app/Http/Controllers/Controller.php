<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Dokumentasi Main API",
 * description="Tulang punggung dari berbagai aplikasi."
 * )
 * @OA\SecurityScheme(
 * securityScheme="sanctum",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * )
 */

abstract class Controller
{
    //
}
