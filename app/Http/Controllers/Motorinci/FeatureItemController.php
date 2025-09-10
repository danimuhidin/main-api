<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\FeatureItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FeatureItemController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/features",
     * operationId="getMotorinciFeatureItemsList",
     * tags={"Motorinci Feature Items"},
     * summary="Mendapatkan daftar item fitur motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = FeatureItem::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $featureItems = $query->get();

        return response()->json([
            'message' => 'Motorinci feature items retrieved successfully with pagination',
            'data' => $featureItems,
            'pagination' => [
                'total' => FeatureItem::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

   

    /**
     * @OA\Post(
     * path="/api/motorinci/features",
     * operationId="storeMotorinciFeatureItem",
     * tags={"Motorinci Feature Items"},
     * summary="Membuat item fitur motorinci baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"name"},
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Hemat Bahan Bakar",
     * ),
     * @OA\Property(
     * property="desc",
     * type="string",
     * example="Sistem injeksi canggih yang membuat konsumsi bahan bakar lebih efisien.",
     * ),
     * @OA\Property(
     * property="icon",
     * type="string",
     * format="binary",
     * description="Icon file"
     * )
     * )
     * )
     * ),
     * @OA\Response(response=201, ref="#/components/responses/201_Created"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized"),
     * @OA\Response(response=403, ref="#/components/responses/403_Forbidden")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:motorinci_feature_items,name',
            'desc' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('motorinci/features/icons', 'public');
        }

        $featureItem = FeatureItem::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $iconPath,
        ]);

        return response()->json([
            'message' => 'Motorinci feature item created successfully',
            'data' => $featureItem
        ], 201);
    }

   

    /**
     * @OA\Get(
     * path="/api/motorinci/features/{id}",
     * operationId="getMotorinciFeatureItemById",
     * tags={"Motorinci Feature Items"},
     * summary="Mendapatkan satu item fitur motorinci berdasarkan ID",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci feature item ID",
     * required=true,
     * in="path",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function show($id)
    {
        $featureItem = FeatureItem::find($id);
        if (!$featureItem) {
            return response()->json([
                'message' => 'Motorinci feature item not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Motorinci feature item retrieved successfully',
            'data' => $featureItem
        ], 200);
    }

   

    /**
     * @OA\Post(
     * path="/api/motorinci/features/{id}",
     * operationId="updateMotorinciFeatureItem",
     * tags={"Motorinci Feature Items"},
     * summary="Memperbarui item fitur motorinci yang sudah ada",
     * description="Gunakan method POST dengan _method=PUT di dalam form-data untuk request PUT/PATCH.",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci feature item ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="_method", type="string", example="PUT"),
     * @OA\Property(property="name", type="string", example="Kunci Canggih"),
     * @OA\Property(property="desc", type="string", example="Sistem kunci pintar dengan alarm terintegrasi."),
     * @OA\Property(property="icon", type="string", format="binary", description="New icon file (optional)")
     * )
     * )
     * ),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized"),
     * @OA\Response(response=403, ref="#/components/responses/403_Forbidden")
     * )
     */
    public function update(Request $request, $id)
    {
        $featureItem = FeatureItem::find($id);
        if (!$featureItem) {
            return response()->json([
                'message' => 'Motorinci feature item not found'
            ], 404);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('motorinci_feature_items', 'name')->ignore($featureItem->id),
            ],
            'desc' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $iconPath = $featureItem->icon;
        if ($request->hasFile('icon')) {
            if ($featureItem->icon) {
                Storage::disk('public')->delete($featureItem->icon);
            }
            $iconPath = $request->file('icon')->store('motorinci/features/icons', 'public');
        }

        $featureItem->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $iconPath,
        ]);

        return response()->json([
            'message' => 'Motorinci feature item updated successfully',
            'data' => $featureItem
        ], 200);
    }

   

    /**
     * @OA\Delete(
     * path="/api/motorinci/features/{id}",
     * operationId="deleteMotorinciFeatureItem",
     * tags={"Motorinci Feature Items"},
     * summary="Menghapus item fitur motorinci",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci feature item ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=204, ref="#/components/responses/204_NoContent"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized"),
     * @OA\Response(response=403, ref="#/components/responses/403_Forbidden")
     * )
     */
    public function destroy($id)
    {
        $featureItem = FeatureItem::find($id);
        if (!$featureItem) {
            return response()->json([
                'message' => 'Motorinci feature item not found'
            ], 404);
        }

        if ($featureItem->icon) {
            Storage::disk('public')->delete($featureItem->icon);
        }

        $featureItem->delete();

        return response()->json([
            'message' => 'Motorinci feature item deleted successfully'
        ], 200);
    }
}