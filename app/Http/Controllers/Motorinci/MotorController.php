<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\Motor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MotorController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/motors",
     * operationId="getMotorinciMotorsList",
     * tags={"Motorinci Motors"},
     * summary="Mendapatkan daftar motor",
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

        // Menggunakan eager loading untuk mengambil relasi brand dan category
        $query = Motor::with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup', 'reviews', 'availableColors.color']);

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $motors = $query->latest()->get();
        $total = Motor::count();

        return response()->json([
            'message' => 'Motorinci motors retrieved successfully with pagination',
            'data' => $motors,
            'pagination' => [
                'total' => $total,
                'limit' => $limit ? (int) $limit : $total,
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/motorinci/motors",
     * operationId="storeMotorinciMotor",
     * tags={"Motorinci Motors"},
     * summary="Membuat data motor baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"name", "brand_id", "category_id", "year_model", "engine_cc"},
     * @OA\Property(property="name", type="string", example="Vario 160"),
     * @OA\Property(property="brand_id", type="integer", example=1),
     * @OA\Property(property="category_id", type="integer", example=1),
     * @OA\Property(property="year_model", type="integer", example=2024),
     * @OA\Property(property="engine_cc", type="integer", example=160),
     * @OA\Property(property="low_price", type="integer", example=27000000),
     * @OA\Property(property="up_price", type="integer", example=29000000),
     * @OA\Property(property="desc", type="string", example="Skutik populer dari Honda."),
     * @OA\Property(property="brochure_url", type="string", format="url", example="https://www.example.com/brochure/vario-160.pdf"),
     * @OA\Property(property="sparepart_url", type="string", format="url", example="https://www.example.com/sparepart/vario-160"),
     * @OA\Property(property="is_active", type="boolean", example=true),
     * @OA\Property(property="is_featured", type="boolean", example=false),
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
            'name' => 'required|string|max:255|unique:motorinci_motors,name',
            'brand_id' => 'required|integer|exists:motorinci_brands,id',
            'category_id' => 'required|integer|exists:motorinci_categories,id',
            'year_model' => 'required|integer|digits:4',
            'engine_cc' => 'required|integer',
            'low_price' => 'nullable|integer',
            'up_price' => 'nullable|integer',
            'desc' => 'nullable|string',
            'brochure_url' => 'nullable|string',
            'sparepart_url' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        // Set published_at jika motor aktif
        $validated['published_at'] = ($request->boolean('is_active', true)) ? now() : null;

        $motor = Motor::create($validated);

        return response()->json([
            'message' => 'Motorinci motor created successfully',
            'data' => $motor
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/motorinci/motors/{id}",
     * operationId="getMotorinciMotorById",
     * tags={"Motorinci Motors"},
     * summary="Mendapatkan detail motor berdasarkan ID",
     * @OA\Parameter(name="id", description="Motor ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function show($id)
    {
        $motor = Motor::with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup', 'reviews', 'availableColors.color'])->find($id);

        if (!$motor) {
            return response()->json(['message' => 'Motorinci motor not found'], 404);
        }

        return response()->json([
            'message' => 'Motorinci motor retrieved successfully',
            'data' => $motor
        ], 200);
    }

    /**
     * @OA\Put(
     * path="/api/motorinci/motors/{id}",
     * operationId="updateMotorinciMotor",
     * tags={"Motorinci Motors"},
     * summary="Update data motor yang sudah ada",
     * @OA\Parameter(name="id", description="Motor ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="name", type="string", example="Vario 160 ABS"),
     * @OA\Property(property="brand_id", type="integer", example=1),
     * @OA\Property(property="category_id", type="integer", example=1),
     * @OA\Property(property="year_model", type="integer", example=2025),
     * @OA\Property(property="engine_cc", type="integer", example=160),
     * @OA\Property(property="brochure_url", type="string", format="url", example="https://www.new-url.com/brochure.pdf"),
     * @OA\Property(property="sparepart_url", type="string", format="url", example="https://www.new-url.com/sparepart"),
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
        $motor = Motor::find($id);
        if (!$motor) {
            return response()->json(['message' => 'Motorinci motor not found'], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => 'required|integer|exists:motorinci_brands,id',
            'category_id' => 'required|integer|exists:motorinci_categories,id',
            'year_model' => 'required|integer|digits:4',
            'engine_cc' => 'required|integer',
            'low_price' => 'nullable|integer',
            'up_price' => 'nullable|integer',
            'desc' => 'nullable|string',
            'brochure_url' => 'nullable|string',
            'sparepart_url' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);
        
        // Atur tanggal publikasi berdasarkan status is_active
        if ($request->has('is_active')) {
            $validated['published_at'] = $request->boolean('is_active') ? ($motor->published_at ?? now()) : null;
        }

        $motor->update($validated);

        return response()->json([
            'message' => 'Motorinci motor updated successfully',
            'data' => $motor->load(['brand', 'category']) // Muat ulang relasi
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/api/motorinci/motors/{id}",
     * operationId="deleteMotorinciMotor",
     * tags={"Motorinci Motors"},
     * summary="Menghapus data motor",
     * @OA\Parameter(name="id", description="Motor ID", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Delete successful"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function destroy($id)
    {
        $motor = Motor::find($id);
        if (!$motor) {
            return response()->json(['message' => 'Motorinci motor not found'], 404);
        }

        $motor->delete();

        return response()->json(['message' => 'Motorinci motor deleted successfully'], 200);
    }

    
    /**
     * @OA\Get(
     * path="/api/motorinci/search-motors",
     * operationId="searchMotorinciMotors",
     * tags={"Motorinci Motors"},
     * summary="Mencari motor berdasarkan nama",
     * @OA\Parameter(name="search", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */

    public function search(Request $request)
    {
        $search = $request->input('search');
        if (!$search) {
            return response()->json(['message' => 'Search query is required'], 400);
        }

        $motors = Motor::where('name', 'like', "%{$search}%")->with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup'])->get();

        return response()->json($motors);
    }

    // make random 
    /**
     * @OA\Get(
     * path="/api/motorinci/motors/random",
     * operationId="getRandomMotorinciMotors",
     * tags={"Motorinci Motors"},
     * summary="Mendapatkan daftar motor secara acak",
     * @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function random($limit = 20)
    {
        // $motors = Motor::inRandomOrder()->take($limit)->get();
        $motors = Motor::inRandomOrder()->with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup'])->take($limit)->get();
        return response()->json($motors);
    }

    // komparasi 
    /**
     * @OA\Get(
     * path="/api/motorinci/komparasi/{idsatu}/{iddua}",
     * operationId="compareTwoMotorinciMotors",
     * tags={"Motorinci Motors"},
     * summary="Membandingkan dua motor berdasarkan ID",
     * @OA\Parameter(name="idsatu", description="ID Motor Pertama", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Parameter(name="iddua", description="ID Motor Kedua", required=true, in="path", @OA\Schema(type="integer")),
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=404, ref="#/components/responses/404_NotFound")
     * )
     */
    public function komparasi($idsatu, $iddua)
    {
        $motor1 = Motor::with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup', 'reviews', 'availableColors.color'])->find($idsatu);
        $motor2 = Motor::with(['brand', 'category', 'features.featureItem', 'images', 'specifications.specificationItem.specificationGroup', 'reviews', 'availableColors.color'])->find($iddua);
        if (!$motor1 || !$motor2) {
            return response()->json(['message' => 'One or both Motorinci motors not found'], 404);
        }

        return response()->json([
            'message' => 'Motorinci motors retrieved successfully',
            'data' => [
                'motor1' => $motor1,
                'motor2' => $motor2
            ]
        ], 200);
    }
}