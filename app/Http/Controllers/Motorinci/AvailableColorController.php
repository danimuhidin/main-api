<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\AvailableColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AvailableColorController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/available-colors",
     * operationId="getMotorinciAvailableColorsList",
     * tags={"Motorinci Available Colors"},
     * summary="Mendapatkan daftar warna yang tersedia untuk motor",
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

        // Eager load relasi motor dan color untuk efisiensi
        $query = AvailableColor::with(['motor', 'color']);

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $availableColors = $query->latest()->get();
        $total = AvailableColor::count();

        return response()->json([
            'message' => 'Available colors retrieved successfully with pagination',
            'data' => $availableColors,
            'pagination' => [
                'total' => $total,
                'limit' => $limit ? (int) $limit : $total,
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/available-colors",
     * operationId="storeMotorinciAvailableColor",
     * tags={"Motorinci Available Colors"},
     * summary="Menambahkan warna baru untuk sebuah motor",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"motor_id", "color_id"},
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="color_id", type="integer", example=1),
     * @OA\Property(
     * property="image",
     * type="string",
     * format="binary",
     * description="Gambar motor dengan warna terkait"
     * )
     * )
     * )
     * ),
     * @OA\Response(response=201, ref="#/components/responses/201_Created"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'motor_id' => 'required|integer|exists:motorinci_motors,id',
            'color_id' => [
                'required',
                'integer',
                'exists:motorinci_colors,id',
                // Pastikan kombinasi motor_id dan color_id unik
                Rule::unique('motorinci_available_colors')->where(function ($query) use ($request) {
                    return $query->where('motor_id', $request->motor_id);
                }),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('motorinci/colors/images', 'public');
        }

        $availableColor = AvailableColor::create([
            'motor_id' => $request->motor_id,
            'color_id' => $request->color_id,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Available color created successfully',
            'data' => $availableColor->load(['motor', 'color'])
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/motorinci/available-colors/{id}",
     * operationId="getMotorinciAvailableColorById",
     * tags={"Motorinci Available Colors"},
     * summary="Mendapatkan detail warna tersedia berdasarkan ID",
     * @OA\Parameter(name="id", description="Available Color ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function show($id)
    {
        $availableColor = AvailableColor::with(['motor', 'color'])->find($id);

        if (!$availableColor) {
            return response()->json(['message' => 'Available color not found'], 404);
        }

        return response()->json([
            'message' => 'Available color retrieved successfully',
            'data' => $availableColor
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/available-colors/{id}",
     * operationId="updateMotorinciAvailableColor",
     * tags={"Motorinci Available Colors"},
     * summary="Update data warna tersedia",
     * description="Gunakan method POST dengan _method=PUT di dalam form-data untuk request PUT/PATCH.",
     * @OA\Parameter(name="id", description="Available Color ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="_method", type="string", example="PUT"),
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="color_id", type="integer", example=2),
     * @OA\Property(property="image", type="string", format="binary", description="Gambar baru (opsional)")
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
        $availableColor = AvailableColor::find($id);
        if (!$availableColor) {
            return response()->json(['message' => 'Available color not found'], 404);
        }

        $request->validate([
            'motor_id' => 'required|integer|exists:motorinci_motors,id',
            'color_id' => [
                'required',
                'integer',
                'exists:motorinci_colors,id',
                // Validasi unik, abaikan baris saat ini
                Rule::unique('motorinci_available_colors')->where(function ($query) use ($request) {
                    return $query->where('motor_id', $request->motor_id);
                })->ignore($availableColor->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $availableColor->image;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($availableColor->image) {
                Storage::disk('public')->delete($availableColor->image);
            }
            $imagePath = $request->file('image')->store('motorinci/colors/images', 'public');
        }

        $availableColor->update([
            'motor_id' => $request->motor_id,
            'color_id' => $request->color_id,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Available color updated successfully',
            'data' => $availableColor->load(['motor', 'color'])
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/motorinci/available-colors/{id}",
     * operationId="deleteMotorinciAvailableColor",
     * tags={"Motorinci Available Colors"},
     * summary="Menghapus data warna tersedia",
     * @OA\Parameter(name="id", description="Available Color ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Delete successful"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function destroy($id)
    {
        $availableColor = AvailableColor::find($id);
        if (!$availableColor) {
            return response()->json(['message' => 'Available color not found'], 404);
        }

        // Hapus file gambar dari storage sebelum menghapus record database
        if ($availableColor->image) {
            Storage::disk('public')->delete($availableColor->image);
        }

        $availableColor->delete();

        return response()->json(['message' => 'Available color deleted successfully'], 200);
    }
}