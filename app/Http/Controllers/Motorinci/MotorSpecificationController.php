<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\MotorSpecification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MotorSpecificationController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/motor-specifications",
     * operationId="getMotorinciMotorSpecificationsList",
     * tags={"Motorinci Motor Specifications"},
     * summary="Mendapatkan daftar spesifikasi motor",
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
        $query = MotorSpecification::with(['motor', 'specificationItem']);

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $motorSpecs = $query->latest()->get();
        $total = MotorSpecification::count();

        return response()->json([
            'message' => 'Motor specifications retrieved successfully with pagination',
            'data' => $motorSpecs,
            'pagination' => [
                'total' => $total,
                'limit' => $limit ? (int) $limit : $total,
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/motor-specifications",
     * operationId="storeMotorinciMotorSpecification",
     * tags={"Motorinci Motor Specifications"},
     * summary="Menambahkan spesifikasi baru ke sebuah motor",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"motor_id", "specification_item_id", "value"},
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="specification_item_id", type="integer", example=1),
     * @OA\Property(property="value", type="string", example="155cc")
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
            'specification_item_id' => [
                'required',
                'integer',
                'exists:motorinci_specification_items,id',
                // Pastikan kombinasi motor_id dan specification_item_id unik
                Rule::unique('motorinci_motor_specifications')->where(function ($query) use ($request) {
                    return $query->where('motor_id', $request->motor_id);
                }),
            ],
            'value' => 'required|string|max:255',
        ]);

        $motorSpec = MotorSpecification::create($validated);

        return response()->json([
            'message' => 'Motor specification created successfully',
            'data' => $motorSpec->load(['motor', 'specificationItem'])
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/motorinci/motor-specifications/{id}",
     * operationId="getMotorinciMotorSpecificationById",
     * tags={"Motorinci Motor Specifications"},
     * summary="Mendapatkan detail spesifikasi motor berdasarkan ID",
     * @OA\Parameter(name="id", description="Motor Specification ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function show($id)
    {
        $motorSpec = MotorSpecification::with(['motor', 'specificationItem'])->find($id);

        if (!$motorSpec) {
            return response()->json(['message' => 'Motor specification not found'], 404);
        }

        return response()->json([
            'message' => 'Motor specification retrieved successfully',
            'data' => $motorSpec
        ], 200);
    }

    /**
     * @OA\Put(
     * path="/api/motorinci/motor-specifications/{id}",
     * operationId="updateMotorinciMotorSpecification",
     * tags={"Motorinci Motor Specifications"},
     * summary="Update data spesifikasi motor",
     * @OA\Parameter(name="id", description="Motor Specification ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="specification_item_id", type="integer", example=2),
     * @OA\Property(property="value", type="string", example="160cc")
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
        $motorSpec = MotorSpecification::find($id);
        if (!$motorSpec) {
            return response()->json(['message' => 'Motor specification not found'], 404);
        }

        $validated = $request->validate([
            'motor_id' => 'required|integer|exists:motorinci_motors,id',
            'specification_item_id' => [
                'required',
                'integer',
                'exists:motorinci_specification_items,id',
                // Validasi unik, abaikan baris saat ini
                Rule::unique('motorinci_motor_specifications')->where(function ($query) use ($request) {
                    return $query->where('motor_id', $request->motor_id);
                })->ignore($motorSpec->id),
            ],
            'value' => 'required|string|max:255',
        ]);

        $motorSpec->update($validated);

        return response()->json([
            'message' => 'Motor specification updated successfully',
            'data' => $motorSpec->load(['motor', 'specificationItem'])
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/motorinci/motor-specifications/{id}",
     * operationId="deleteMotorinciMotorSpecification",
     * tags={"Motorinci Motor Specifications"},
     * summary="Menghapus data spesifikasi dari motor",
     * @OA\Parameter(name="id", description="Motor Specification ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Delete successful"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function destroy($id)
    {
        $motorSpec = MotorSpecification::find($id);
        if (!$motorSpec) {
            return response()->json(['message' => 'Motor specification not found'], 404);
        }

        $motorSpec->delete();

        return response()->json(['message' => 'Motor specification deleted successfully'], 200);
    }
}