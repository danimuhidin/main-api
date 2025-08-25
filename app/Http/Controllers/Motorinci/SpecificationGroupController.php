<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\SpecificationGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecificationGroupController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/specification-groups",
     * operationId="getMotorinciSpecificationGroupsList",
     * tags={"Motorinci Specification Groups"},
     * summary="Mendapatkan daftar grup spesifikasi motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = SpecificationGroup::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $specificationGroups = $query->get();

        return response()->json([
            'message' => 'Motorinci specification groups retrieved successfully with pagination',
            'data' => $specificationGroups,
            'pagination' => [
                'total' => SpecificationGroup::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    

    /**
     * @OA\Post(
     * path="/api/motorinci/specification-groups",
     * operationId="storeMotorinciSpecificationGroup",
     * tags={"Motorinci Specification Groups"},
     * summary="Membuat grup spesifikasi motorinci baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * required={"name"},
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Dimensi",
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
            'name' => 'required|string|unique:motorinci_specification_groups,name',
            'order' => 'nullable|integer',
        ]);

        $specificationGroup = SpecificationGroup::create($request->all());

        return response()->json([
            'message' => 'Motorinci specification group created successfully',
            'data' => $specificationGroup
        ], 201);
    }

    

    /**
     * @OA\Get(
     * path="/api/motorinci/specification-groups/{id}",
     * operationId="getMotorinciSpecificationGroupById",
     * tags={"Motorinci Specification Groups"},
     * summary="Mendapatkan satu grup spesifikasi motorinci berdasarkan ID",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci specification group ID",
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
        $specificationGroup = SpecificationGroup::find($id);
        if (!$specificationGroup) {
            return response()->json([
                'message' => 'Motorinci specification group not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Motorinci specification group retrieved successfully',
            'data' => $specificationGroup
        ], 200);
    }

    

    /**
     * @OA\Put(
     * path="/api/motorinci/specification-groups/{id}",
     * operationId="updateMotorinciSpecificationGroup",
     * tags={"Motorinci Specification Groups"},
     * summary="Memperbarui grup spesifikasi motorinci yang sudah ada",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci specification group ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * @OA\Property(property="name", type="string", example="Rangka & Kaki-kaki"),
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
        $specificationGroup = SpecificationGroup::find($id);
        if (!$specificationGroup) {
            return response()->json([
                'message' => 'Motorinci specification group not found'
            ], 404);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('motorinci_specification_groups', 'name')->ignore($specificationGroup->id),
            ],
            'order' => 'nullable|integer',
        ]);

        $specificationGroup->update($request->all());

        return response()->json([
            'message' => 'Motorinci specification group updated successfully',
            'data' => $specificationGroup
        ], 200);
    }

    

    /**
     * @OA\Delete(
     * path="/api/motorinci/specification-groups/{id}",
     * operationId="deleteMotorinciSpecificationGroup",
     * tags={"Motorinci Specification Groups"},
     * summary="Menghapus grup spesifikasi motorinci",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci specification group ID",
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
        $specificationGroup = SpecificationGroup::find($id);
        if (!$specificationGroup) {
            return response()->json([
                'message' => 'Motorinci specification group not found'
            ], 404);
        }

        $specificationGroup->delete();

        return response()->json([
            'message' => 'Motorinci specification group deleted successfully'
        ], 200);
    }
}