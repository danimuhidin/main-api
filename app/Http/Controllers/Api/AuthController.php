<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="loginUser",
     * tags={"Authentication"},
     * summary="Login Pengguna",
     * description="Mengautentikasi pengguna dengan email atau username, dan mengembalikan token API",
     * @OA\RequestBody(
     * required=true,
     * description="Kredensial login pengguna",
     * @OA\JsonContent(
     * required={"login", "password"},
     * @OA\Property(property="login", type="string", description="Bisa diisi dengan email atau username", example="admin"),
     * @OA\Property(property="password", type="string", format="password", example="administrator"),
     * ),
     * ),
     * @OA\Response(response=200, description="Login Berhasil"),
     * @OA\Response(response=401, description="Unauthorized"),
     * @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Kredensial salah.'], 401);
        }

        $user = User::where($loginField, $request->login)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
