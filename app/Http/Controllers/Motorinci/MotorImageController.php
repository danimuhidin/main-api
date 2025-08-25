<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\MotorImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MotorImageController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/motor-images",
     * operationId="getMotorinciMotorImagesList",
     * tags={"Motorinci Motor Images"},
     * summary="Mendapatkan daftar gambar motor",
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

        // Eager load relasi motor dan urutkan berdasarkan 'order'
        $query = MotorImage::with('motor')->orderBy('order', 'asc');

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $motorImages = $query->get();
        $total = MotorImage::count();

        return response()->json([
            'message' => 'Motor images retrieved successfully with pagination',
            'data' => $motorImages,
            'pagination' => [
                'total' => $total,
                'limit' => $limit ? (int) $limit : $total,
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/motor-images",
     * operationId="storeMotorinciMotorImage",
     * tags={"Motorinci Motor Images"},
     * summary="Menambahkan gambar baru untuk sebuah motor",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"motor_id", "image"},
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="image", type="string", format="binary", description="File gambar motor"),
     * @OA\Property(property="desc", type="string", example="Tampak depan"),
     * @OA\Property(property="order", type="integer", example=1)
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
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'desc' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $imagePath = $request->file('image')->store('motorinci/motors/gallery', 'public');

        $motorImage = MotorImage::create([
            'motor_id' => $request->motor_id,
            'image' => $imagePath,
            'desc' => $request->desc,
            'order' => $request->order ?? 0,
        ]);

        return response()->json([
            'message' => 'Motor image created successfully',
            'data' => $motorImage->load('motor')
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/motorinci/motor-images/{id}",
     * operationId="getMotorinciMotorImageById",
     * tags={"Motorinci Motor Images"},
     * summary="Mendapatkan detail gambar motor berdasarkan ID",
     * @OA\Parameter(name="id", description="Motor Image ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function show($id)
    {
        $motorImage = MotorImage::with('motor')->find($id);

        if (!$motorImage) {
            return response()->json(['message' => 'Motor image not found'], 404);
        }

        return response()->json([
            'message' => 'Motor image retrieved successfully',
            'data' => $motorImage
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/motor-images/{id}",
     * operationId="updateMotorinciMotorImage",
     * tags={"Motorinci Motor Images"},
     * summary="Update data gambar motor",
     * description="Gunakan method POST dengan _method=PUT di dalam form-data untuk request PUT/PATCH.",
     * @OA\Parameter(name="id", description="Motor Image ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="_method", type="string", example="PUT"),
     * @OA\Property(property="motor_id", type="integer", example=1),
     * @OA\Property(property="image", type="string", format="binary", description="File gambar baru (opsional)"),
     * @OA\Property(property="desc", type="string", example="Tampak samping kiri"),
     * @OA\Property(property="order", type="integer", example=2)
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
        $motorImage = MotorImage::find($id);
        if (!$motorImage) {
            return response()->json(['message' => 'Motor image not found'], 404);
        }

        $request->validate([
            'motor_id' => 'required|integer|exists:motorinci_motors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'desc' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $imagePath = $motorImage->image;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($motorImage->image) {
                Storage::disk('public')->delete($motorImage->image);
            }
            $imagePath = $request->file('image')->store('motorinci/motors/gallery', 'public');
        }

        $motorImage->update([
            'motor_id' => $request->motor_id,
            'image' => $imagePath,
            'desc' => $request->desc,
            'order' => $request->order,
        ]);

        return response()->json([
            'message' => 'Motor image updated successfully',
            'data' => $motorImage->load('motor')
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/motorinci/motor-images/{id}",
     * operationId="deleteMotorinciMotorImage",
     * tags={"Motorinci Motor Images"},
     * summary="Menghapus data gambar motor",
     * @OA\Parameter(name="id", description="Motor Image ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Delete successful"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function destroy($id)
    {
        $motorImage = MotorImage::find($id);
        if (!$motorImage) {
            return response()->json(['message' => 'Motor image not found'], 404);
        }

        // Hapus file gambar dari storage sebelum menghapus record database
        if ($motorImage->image) {
            Storage::disk('public')->delete($motorImage->image);
        }

        $motorImage->delete();

        return response()->json(['message' => 'Motor image deleted successfully'], 200);
    }
}