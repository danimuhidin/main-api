<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/motorinci/categories",
     * operationId="getMotorinciCategoriesList",
     * tags={"Motorinci Categories"},
     * summary="Mendapatkan daftar kategori motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */

    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = Category::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $categories = $query->get();

        return response()->json([
            'message' => 'Motorinci categories retrieved successfully with pagination',
            'data' => $categories,
            'pagination' => [
                'total' => Category::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/categories",
     * operationId="storeMotorinciCategory",
     * tags={"Motorinci Categories"},
     * summary="Create a new motorinci category",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"name", "desc"},
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Sport",
     * ),
     * @OA\Property(
     * property="desc",
     * type="string",
     * example="Category for sport bikes",
     * ),
     * @OA\Property(
     * property="image",
     * type="string",
     * format="binary",
     * description="Image file"
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
            'name' => 'required|string|unique:motorinci_categories,name',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('motorinci/kategori', 'public');
        }

        $category = Category::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Motorinci category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/motorinci/categories/{id}",
     * operationId="getMotorinciCategoryById",
     * tags={"Motorinci Categories"},
     * summary="Get a single motorinci category by ID",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci category ID",
     * required=true,
     * in="path",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized"),
     * )
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Motorinci category not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Motorinci category retrieved successfully',
            'data' => $category
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/categories/{id}",
     * operationId="updateMotorinciCategory",
     * tags={"Motorinci Categories"},
     * summary="Update an existing motorinci category",
     * description="Gunakan method POST dengan _method=PUT di dalam form-data untuk request PUT/PATCH.",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci category ID",
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
     * @OA\Property(property="name", type="string", example="Cruiser"),
     * @OA\Property(property="desc", type="string", example="Category for cruiser bikes"),
     * @OA\Property(property="image", type="string", format="binary", description="New image file (optional)")
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
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Motorinci category not found'
            ], 404);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('motorinci_categories', 'name')->ignore($category->id),
            ],
            'desc' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('motorinci/kategori', 'public');
        } else {
            $imagePath = $category->image;
        }

        $category->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Motorinci category updated successfully',
            'data' => $category
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/motorinci/categories/{id}",
     * operationId="deleteMotorinciCategory",
     * tags={"Motorinci Categories"},
     * summary="Delete a motorinci category",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci category ID",
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
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Motorinci category not found'
            ], 404);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'message' => 'Motorinci category deleted successfully'
        ], 200);
    }
}
