<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/colors",
     * operationId="motorincigetColorsList",
     * tags={"Motorinci Colors"},
     * summary="Mendapatkan daftar warna",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = Color::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $colors = $query->get();

        return response()->json([
            'message' => 'Colors retrieved successfully with pagination',
            'data' => $colors,
            'pagination' => [
                'total' => Color::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

   

    /**
     * @OA\Post(
     * path="/api/motorinci/colors",
     * operationId="motorincistoreColor",
     * tags={"Motorinci Colors"},
     * summary="Membuat warna baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * required={"name", "hex"},
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Merah",
     * ),
     * @OA\Property(
     * property="hex",
     * type="string",
     * example="#FF0000",
     * )
     * )
     * )
     * ),
     * @OA\Response(response=201, ref="#/components/responses/201_Created"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:motorinci_colors,name',
            'hex' => 'required|string|regex:/^#[a-f0-9]{6}$/i',
        ]);

        $color = Color::create([
            'name' => $request->name,
            'hex' => $request->hex,
        ]);

        return response()->json([
            'message' => 'Color created successfully',
            'data' => $color
        ], 201);
    }

   

    /**
     * @OA\Get(
     * path="/api/motorinci/colors/{id}",
     * operationId="motorincigetColorById",
     * tags={"Motorinci Colors"},
     * summary="Mendapatkan satu warna berdasarkan ID",
     * @OA\Parameter(
     * name="id",
     * description="Color ID",
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
        $color = Color::find($id);
        if (!$color) {
            return response()->json([
                'message' => 'Color not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Color retrieved successfully',
            'data' => $color
        ], 200);
    }

   

    /**
     * @OA\Put(
     * path="/api/motorinci/colors/{id}",
     * operationId="motorinciupdateColor",
     * tags={"Motorinci Colors"},
     * summary="Memperbarui warna yang ada",
     * @OA\Parameter(
     * name="id",
     * description="Color ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * @OA\Property(property="name", type="string", example="Biru Muda"),
     * @OA\Property(property="hex", type="string", example="#87CEEB")
     * )
     * )
     * ),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=422, ref="#/components/responses/422_UnprocessableContent"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function update(Request $request, $id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json([
                'message' => 'Color not found'
            ], 404);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('motorinci_colors', 'name')->ignore($color->id),
            ],
            'hex' => 'required|string|regex:/^#[a-f0-9]{6}$/i',
        ]);

        $color->update([
            'name' => $request->name,
            'hex' => $request->hex,
        ]);

        return response()->json([
            'message' => 'Color updated successfully',
            'data' => $color
        ], 200);
    }

   

    /**
     * @OA\Delete(
     * path="/api/motorinci/colors/{id}",
     * operationId="motorincideleteColor",
     * tags={"Motorinci Colors"},
     * summary="Menghapus warna",
     * @OA\Parameter(
     * name="id",
     * description="Color ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=204, ref="#/components/responses/204_NoContent"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function destroy($id)
    {
        $color = Color::find($id);
        if (!$color) {
            return response()->json([
                'message' => 'Color not found'
            ], 404);
        }

        $color->delete();

        return response()->json([
            'message' => 'Color deleted successfully'
        ], 200);
    }
}
