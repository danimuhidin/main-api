<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 * name="User Management",
 * description="Endpoints untuk mengelola pengguna"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/users",
     * operationId="getUsersList",
     * tags={"User Management"},
     * summary="Mendapatkan daftar pengguna",
     * security={{"sanctum":{}}},
     * @OA\Response(
     * response=200,
     * description="Operasi berhasil",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * )
     * )
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @OA\Post(
     * path="/api/users",
     * operationId="storeUser",
     * tags={"User Management"},
     * summary="Membuat pengguna baru",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password"},
     * @OA\Property(property="name", type="string", example="Jane Doe"),
     * @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Pengguna berhasil dibuat",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=2),
     * @OA\Property(property="name", type="string", example="Jane Doe"),
     * @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     * path="/api/users/{id}",
     * operationId="getUserById",
     * tags={"User Management"},
     * summary="Mendapatkan detail pengguna",
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Operasi berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Resource Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Not Found.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * )
     * )
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * @OA\Put(
     * path="/api/users/{id}",
     * operationId="updateUser",
     * tags={"User Management"},
     * summary="Memperbarui pengguna",
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email"},
     * @OA\Property(property="name", type="string", example="John Doe Updated"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe.updated@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="newpassword123", description="Opsional")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Pengguna berhasil diperbarui",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John Doe Updated"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe.updated@example.com")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Resource Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Not Found.")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * )
     * )
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json($user, 200);
    }

    /**
     * @OA\Delete(
     * path="/api/users/{id}",
     * operationId="deleteUser",
     * tags={"User Management"},
     * summary="Menghapus pengguna",
     * description="Menghapus data pengguna yang sudah ada",
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Pengguna berhasil dihapus",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User deleted successfully")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Resource Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Not Found.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * )
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
