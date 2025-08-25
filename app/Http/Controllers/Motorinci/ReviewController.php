<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use App\Models\Motorinci\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/motorinci/reviews",
     * operationId="getMotorinciReviewsList",
     * tags={"Motorinci Reviews"},
     * summary="Mendapatkan daftar ulasan motorinci",
     * @OA\Response(response=200, ref="#/components/responses/200_Success"),
     * @OA\Response(response=401, ref="#/components/responses/401_Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');

        $query = Review::query();

        if ($offset) {
            $query->skip($offset);
        }

        if ($limit) {
            $query->take($limit);
        }

        $reviews = $query->get();

        return response()->json([
            'message' => 'Motorinci reviews retrieved successfully with pagination',
            'data' => $reviews,
            'pagination' => [
                'total' => Review::count(),
                'limit' => $limit ? (int) $limit : 'unlimited',
                'offset' => (int) $offset,
            ]
        ], 200);
    }

    

    /**
     * @OA\Post(
     * path="/api/motorinci/reviews",
     * operationId="storeMotorinciReview",
     * tags={"Motorinci Reviews"},
     * summary="Membuat ulasan motorinci baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * required={"motor_id", "reviewer_name", "reviewer_email", "rating"},
     * @OA\Property(
     * property="motor_id",
     * type="integer",
     * example=1,
     * ),
     * @OA\Property(
     * property="reviewer_name",
     * type="string",
     * example="John Doe",
     * ),
     * @OA\Property(
     * property="reviewer_email",
     * type="string",
     * format="email",
     * example="john.doe@example.com",
     * ),
     * @OA\Property(
     * property="rating",
     * type="integer",
     * example=5,
     * description="Nilai rating dari 1 sampai 5"
     * ),
     * @OA\Property(
     * property="comment",
     * type="string",
     * example="Motor ini sangat bagus dan hemat bahan bakar.",
     * ),
     * @OA\Property(
     * property="is_approved",
     * type="boolean",
     * example=true,
     * description="Status persetujuan ulasan"
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
            'motor_id' => 'required|integer|exists:motors,id',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_approved' => 'nullable|boolean',
        ]);

        $review = Review::create($request->all());

        return response()->json([
            'message' => 'Motorinci review created successfully',
            'data' => $review
        ], 201);
    }

    

    /**
     * @OA\Get(
     * path="/api/motorinci/reviews/{id}",
     * operationId="getMotorinciReviewById",
     * tags={"Motorinci Reviews"},
     * summary="Mendapatkan satu ulasan motorinci berdasarkan ID",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci review ID",
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
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Motorinci review not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Motorinci review retrieved successfully',
            'data' => $review
        ], 200);
    }

    

    /**
     * @OA\Put(
     * path="/api/motorinci/reviews/{id}",
     * operationId="updateMotorinciReview",
     * tags={"Motorinci Reviews"},
     * summary="Memperbarui ulasan motorinci yang sudah ada",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci review ID",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/x-www-form-urlencoded",
     * @OA\Schema(
     * @OA\Property(property="reviewer_name", type="string", example="Jane Doe"),
     * @OA\Property(property="reviewer_email", type="string", format="email", example="jane.doe@example.com"),
     * @OA\Property(property="rating", type="integer", example=4),
     * @OA\Property(property="comment", type="string", example="Cukup baik, tapi kurang responsif."),
     * @OA\Property(property="is_approved", type="boolean", example=false)
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
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Motorinci review not found'
            ], 404);
        }

        $request->validate([
            'reviewer_name' => 'nullable|string|max:255',
            'reviewer_email' => 'nullable|email|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_approved' => 'nullable|boolean',
        ]);

        $review->update($request->all());

        return response()->json([
            'message' => 'Motorinci review updated successfully',
            'data' => $review
        ], 200);
    }

    

    /**
     * @OA\Delete(
     * path="/api/motorinci/reviews/{id}",
     * operationId="deleteMotorinciReview",
     * tags={"Motorinci Reviews"},
     * summary="Menghapus ulasan motorinci",
     * @OA\Parameter(
     * name="id",
     * description="Motorinci review ID",
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
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Motorinci review not found'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'message' => 'Motorinci review deleted successfully'
        ], 200);
    }
}