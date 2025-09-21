<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/brands",
     * operationId="getMotorinciBrandsList",
     * tags={"Motorinci Brands"},
     * summary="Mendapatkan daftar brand motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = Brand::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $brands = $query->get();

        return response()->json([
            'message' => 'Motorinci brands retrieved successfully with pagination',
            'data' => $brands,
            'pagination' => [
                'total' => Brand::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    

    /**
     * @OA\Post(
     * path="/api/motorinci/brands",
     * operationId="storeMotorinciBrand",
     * tags={"Motorinci Brands"},
     * summary="Create a new motorinci brand",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"name"},
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Honda",
     * ),
     * @OA\Property(
     * property="desc",
     * type="string",
     * example="Produsen sepeda motor terbesar di dunia.",
     * ),
     * @OA\Property(
     * property="icon",
     * type="string",
     * format="binary",
     * description="Icon file"
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
            'name' => 'required|string|unique:motorinci_brands,name',
            'desc' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('motorinci/brands/icons', 'public');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('motorinci/brands/images', 'public');
        }

        $brand = Brand::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $iconPath,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Motorinci brand created successfully',
            'data' => $brand
        ], 201);
    }

    

    /**
     * @OA\Get(
     * path="/api/motorinci/brands/{id}",
     * operationId="getMotorinciBrandById",
     * tags={"Motorinci Brands"},
     * summary="Get a single motorinci brand by ID",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci brand ID",
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
        $brand = Brand::with('motors')->find($id);
        if (!$brand) {
            return response()->json([
                'message' => 'Motorinci brand not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Motorinci brand retrieved successfully',
            'data' => $brand
        ], 200);
    }

    

    /**
     * @OA\Post(
     * path="/api/motorinci/brands/{id}",
     * operationId="updateMotorinciBrand",
     * tags={"Motorinci Brands"},
     * summary="Update an existing motorinci brand",
     * description="Gunakan method POST dengan _method=PUT di dalam form-data untuk request PUT/PATCH.",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci brand ID",
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
     * @OA\Property(property="name", type="string", example="Yamaha"),
     * @OA\Property(property="desc", type="string", example="Pabrikan asal Jepang."),
     * @OA\Property(property="icon", type="string", format="binary", description="New icon file (optional)"),
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
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'message' => 'Motorinci brand not found'
            ], 404);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('motorinci_brands', 'name')->ignore($brand->id),
            ],
            'desc' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $iconPath = $brand->icon;
        if ($request->hasFile('icon')) {
            if ($brand->icon) {
                Storage::disk('public')->delete($brand->icon);
            }
            $iconPath = $request->file('icon')->store('motorinci/brands/icons', 'public');
        }

        $imagePath = $brand->image;
        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            $imagePath = $request->file('image')->store('motorinci/brands/images', 'public');
        }

        $brand->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $iconPath,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Motorinci brand updated successfully',
            'data' => $brand
        ], 200);
    }

    

    /**
     * @OA\Delete(
     * path="/api/motorinci/brands/{id}",
     * operationId="deleteMotorinciBrand",
     * tags={"Motorinci Brands"},
     * summary="Delete a motorinci brand",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci brand ID",
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
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'message' => 'Motorinci brand not found'
            ], 404);
        }

        if ($brand->icon) {
            Storage::disk('public')->delete($brand->icon);
        }
        if ($brand->image) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();

        return response()->json([
            'message' => 'Motorinci brand deleted successfully'
        ], 200);
    }
}
