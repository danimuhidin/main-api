<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\AvailableColor;
use App\Models\Motorinci\Motor;
use App\Models\Motorinci\MotorFeature;
use App\Models\Motorinci\MotorSpecification;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/generate",
     * operationId="getMotorinciGenerate",
     * tags={"Motorinci Generate"},
     * summary="Mendapatkan daftar generate motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */

    public function index()
    {
        

        return response()->json([
            'message' => 'Generate data retrieved successfully',
            'data' => 'data',
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/generate",
     * operationId="storeMotorinciGenerate",
     * tags={"Motorinci Generate"},
     * summary="Menyimpan data generate motorinci baru",
     * description="Membuat entri data generate motorinci baru berdasarkan JSON yang dikirim.",
     * @OA\RequestBody(
     * required=true,
     * description="Payload data motor",
     * @OA\JsonContent(
     * required={"name", "brand_id", "category_id", "year_model", "engine_cc", "low_price", "up_price", "specifications"},
     * @OA\Property(property="name", type="string", example="Tiger Revo"),
     * @OA\Property(property="brand_id", type="integer", example=1),
     * @OA\Property(property="category_id", type="integer", example=2),
     * @OA\Property(property="year_model", type="integer", example=2012),
     * @OA\Property(property="engine_cc", type="string", example="200cc"),
     * @OA\Property(property="low_price", type="integer", format="int64", example=25000000),
     * @OA\Property(property="up_price", type="integer", format="int64", example=26500000),
     * @OA\Property(property="desc", type="string", example="Salah satu motor sport touring legendaris..."),
     * @OA\Property(property="colors", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     * @OA\Property(property="features", type="array", @OA\Items(type="integer"), example={1, 9}),
     * @OA\Property(
     * property="specifications",
     * type="array",
     * @OA\Items(
     * type="object",
     * required={"spec_item_id", "value"},
     * @OA\Property(property="spec_item_id", type="integer", example=2),
     * @OA\Property(property="value", type="string", example="4 Langkah, OHC, Silinder Tunggal")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Operasi berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Data motor berhasil diterima!"),
     * @OA\Property(property="data", type="object")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Data tidak valid"
     * )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->all();

        $motor = new Motor();
        $motor->name = $validatedData['name'];
        $motor->year_model = $validatedData['year_model'];
        $motor->brand_id = $validatedData['brand_id'];
        $motor->category_id = $validatedData['category_id'];
        $motor->engine_cc = $validatedData['engine_cc'];
        $motor->low_price = $validatedData['low_price'];
        $motor->up_price = $validatedData['up_price'];
        $motor->desc = $validatedData['desc'];
        $motor->save();

        if (isset($validatedData['colors']) && is_array($validatedData['colors'])) {
            foreach ($validatedData['colors'] as $colorId) {
                $motorColors = new AvailableColor();
                $motorColors->motor_id = $motor->id;
                $motorColors->color_id = $colorId;
                $motorColors->save();
            }
        }

        if (isset($validatedData['features']) && is_array($validatedData['features'])) {
            foreach ($validatedData['features'] as $featureId) {
                $motorFeatures = new MotorFeature();
                $motorFeatures->motor_id = $motor->id;
                $motorFeatures->feature_item_id = $featureId;
                $motorFeatures->save();
            }
        }

        if (isset($validatedData['specifications']) && is_array($validatedData['specifications'])) {
            foreach ($validatedData['specifications'] as $spec) {
                $motorSpec = new MotorSpecification();
                $motorSpec->motor_id = $motor->id;
                $motorSpec->specification_item_id = $spec['spec_item_id'];
                $motorSpec->value = $spec['value'];
                $motorSpec->save();
            }
        }

        return response()->json([
            'message' => 'Data motor berhasil diterima!',
            'data' => $motor
        ], 201);
    }
}
