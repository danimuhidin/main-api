<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\SpecificationItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecificationItemController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/specification-items",
     * operationId="getMotorinciSpecificationItemsList",
     * tags={"Motorinci Specification Items"},
     * summary="Mendapatkan daftar item spesifikasi motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = SpecificationItem::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $specificationItems = $query->with('specificationGroup')->get();

        return response()->json([
            'message' => 'Motorinci specification items retrieved successfully with pagination',
            'data' => $specificationItems,
            'pagination' => [
                'total' => SpecificationItem::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    

    /**
     * @OA\Post(
     * path="/api/motorinci/specification-items",
     * operationId="storeMotorinciSpecificationItem",
     * tags={"Motorinci Specification Items"},
     * summary="Membuat item spesifikasi motorinci baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * required={"specification_group_id", "name"},
     * @OA\Property(
     * property="specification_group_id",
     * type="integer",
     * example=1,
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Panjang x Lebar x Tinggi",
     * ),
     * @OA\Property(
     * property="unit",
     * type="string",
     * example="mm",
     * ),
     * @OA\Property(
     * property="desc",
     * type="string",
     * example="Ukuran dimensi total motor.",
     * ),
     * @OA\Property(
     * property="order",
     * type="integer",
     * example=1,
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
            'specification_group_id' => 'required|integer|exists:motorinci_specification_groups,id',
            'name' => 'required|string|unique:motorinci_specification_items,name',
            'unit' => 'nullable|string',
            'desc' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $specificationItem = SpecificationItem::create($request->all());

        return response()->json([
            'message' => 'Motorinci specification item created successfully',
            'data' => $specificationItem
        ], 201);
    }

    

    /**
     * @OA\Get(
     * path="/api/motorinci/specification-items/{id}",
     * operationId="getMotorinciSpecificationItemById",
     * tags={"Motorinci Specification Items"},
     * summary="Mendapatkan satu item spesifikasi motorinci berdasarkan ID",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci specification item ID",
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
        $specificationItem = SpecificationItem::with('specificationGroup')->find($id);
        if (!$specificationItem) {
            return response()->json([
                'message' => 'Motorinci specification item not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Motorinci specification item retrieved successfully',
            'data' => $specificationItem
        ], 200);
    }

    

    /**
     * @OA\Put(
     * path="/api/motorinci/specification-items/{id}",
     * operationId="updateMotorinciSpecificationItem",
     * tags={"Motorinci Specification Items"},
     * summary="Memperbarui item spesifikasi motorinci yang sudah ada",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci specification item ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * @OA\Property(property="name", type="string", example="Tinggi Tempat Duduk"),
     * @OA\Property(property="unit", type="string", example="mm"),
     * @OA\Property(property="desc", type="string", example="Jarak dari jok ke tanah."),
     * @OA\Property(property="order", type="integer", example=2)
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
        $specificationItem = SpecificationItem::find($id);
        if (!$specificationItem) {
            return response()->json([
                'message' => 'Motorinci specification item not found'
            ], 404);
        }

        $request->validate([
            'specification_group_id' => 'nullable|integer|exists:motorinci_specification_groups,id',
            'name' => [
                'required',
                'string',
                Rule::unique('motorinci_specification_items', 'name')->ignore($specificationItem->id),
            ],
            'unit' => 'nullable|string',
            'desc' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $specificationItem->update($request->all());

        return response()->json([
            'message' => 'Motorinci specification item updated successfully',
            'data' => $specificationItem
        ], 200);
    }

    

    /**
     * @OA\Delete(
     * path="/api/motorinci/specification-items/{id}",
     * operationId="deleteMotorinciSpecificationItem",
     * tags={"Motorinci Specification Items"},
     * summary="Menghapus item spesifikasi motorinci",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci specification item ID",
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
        $specificationItem = SpecificationItem::find($id);
        if (!$specificationItem) {
            return response()->json([
                'message' => 'Motorinci specification item not found'
            ], 404);
        }

        $specificationItem->delete();

        return response()->json([
            'message' => 'Motorinci specification item deleted successfully'
        ], 200);
    }
}