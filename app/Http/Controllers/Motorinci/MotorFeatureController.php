<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\MotorFeature;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MotorFeatureController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/motor-features",
     * operationId="getMotorinciMotorFeaturesList",
     * tags={"Motorinci Motor Features"},
     * summary="Mendapatkan daftar fitur yang terpasang di motor",
     * @OA\Parameter(name="offset", in="query", required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        // Eager load relasi untuk data yang lebih informatif
        $query = MotorFeature::with(['motor', 'featureItem']);

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $motorFeatures = $query->latest()->get();
        $total = MotorFeature::count();

        return response()->json([
            'message' => 'Motor features retrieved successfully with pagination',
            'data' => $motorFeatures,
            'pagination' => [
                'total' => $total,
                'limit' => $limit ? (int) $limit : $total,
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/motor-features",
     * operationId="storeMotorinciMotorFeature",
     * tags={"Motorinci Motor Features"},
     * summary="Menambahkan fitur baru ke sebuah motor",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"motor_id", "feature_item_id"},
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="feature_item_id", type="integer", example=1)
     * )
     * )
     * ),
     * @OA\Response(response=201, ref="#/components/responses/201_Created"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'motor_id' => 'required|integer|exists:motorinci_motors,id',
            'feature_item_id' => [
                'required',
                'integer',
                'exists:motorinci_feature_items,id',
                // Pastikan kombinasi motor_id dan feature_item_id unik
                Rule::unique('motorinci_motor_feature')->where(function ($query) use ($request) {
                    return $query->where('motor_id', $request->motor_id);
                }),
            ],
        ]);

        $motorFeature = MotorFeature::create($validated);

        return response()->json([
            'message' => 'Motor feature created successfully',
            'data' => $motorFeature->load(['motor', 'featureItem'])
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/motorinci/motor-features/{id}",
     * operationId="getMotorinciMotorFeatureById",
     * tags={"Motorinci Motor Features"},
     * summary="Mendapatkan detail fitur motor berdasarkan ID",
     * @OA\Parameter(name="id", description="Motor Feature ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function show($id)
    {
        $motorFeature = MotorFeature::with(['motor', 'featureItem'])->find($id);

        if (!$motorFeature) {
            return response()->json(['message' => 'Motor feature not found'], 404);
        }

        return response()->json([
            'message' => 'Motor feature retrieved successfully',
            'data' => $motorFeature
        ], 200);
    }

    /**
     * @OA\Put(
     * path="/api/motorinci/motor-features/{id}",
     * operationId="updateMotorinciMotorFeature",
     * tags={"Motorinci Motor Features"},
     * summary="Update data fitur motor",
     * @OA\Parameter(name="id", description="Motor Feature ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="feature_item_id", type="integer", example=2)
     * )
     * )
     * ),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent")
     * )
     */
    public function update(Request $request, $id)
    {
        $motorFeature = MotorFeature::find($id);
        if (!$motorFeature) {
            return response()->json(['message' => 'Motor feature not found'], 404);
        }

        $validated = $request->validate([
            'motor_id' => 'required|integer|exists:motorinci_motors,id',
            'feature_item_id' => [
                'required',
                'integer',
                'exists:motorinci_feature_items,id',
                // Validasi unik, abaikan baris saat ini
                Rule::unique('motorinci_motor_feature')->where(function ($query) use ($request) {
                    return $query->where('motor_id', $request->motor_id);
                })->ignore($motorFeature->id),
            ],
        ]);

        $motorFeature->update($validated);

        return response()->json([
            'message' => 'Motor feature updated successfully',
            'data' => $motorFeature->load(['motor', 'featureItem'])
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/motorinci/motor-features/{id}",
     * operationId="deleteMotorinciMotorFeature",
     * tags={"Motorinci Motor Features"},
     * summary="Menghapus data fitur dari motor",
     * @OA\Parameter(name="id", description="Motor Feature ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Delete successful"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function destroy($id)
    {
        $motorFeature = MotorFeature::find($id);
        if (!$motorFeature) {
            return response()->json(['message' => 'Motor feature not found'], 404);
        }

        $motorFeature->delete();

        return response()->json(['message' => 'Motor feature deleted successfully'], 200);
    }
}