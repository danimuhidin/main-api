<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Dokumentasi Main API",
 * description="Tulang punggung dari berbagai aplikasi."
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="sanctum",
 * type="http",
 * scheme="bearer",
 * description="Masukkan token Bearer Anda. Contoh: 'Bearer 1|abcdef...'"
 * )
 *
 * @OA\Components(
 * @OA\Schema(
 * schema="SuccessJsonResponse",
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Operasi berhasil"),
 * @OA\Property(property="data", type="object", nullable=true)
 * ),
 * @OA\Schema(
 * schema="ErrorJsonResponse",
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Terjadi kesalahan")
 * ),
 * @OA\Schema(
 * schema="ValidationErrorJsonResponse",
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Data yang diberikan tidak valid."),
 * @OA\Property(property="errors", type="object", example={"field_name": {"Pesan error."}})
 * ),
 *
 * @OA\Response(
 * response="200_Success",
 * description="Operasi berhasil dieksekusi.",
 * @OA\JsonContent(ref="#/components/schemas/SuccessJsonResponse")
 * ),
 * @OA\Response(
 * response="201_Created",
 * description="Sumber daya berhasil dibuat.",
 * @OA\JsonContent(ref="#/components/schemas/SuccessJsonResponse")
 * ),
 * @OA\Response(
 * response="204_NoContent",
 * description="Operasi berhasil tetapi tidak ada konten untuk dikembalikan."
 * ),
 * @OA\Response(
 * response="401_Unauthorized",
 * description="Autentikasi gagal. Token tidak valid atau tidak ada.",
 * @OA\JsonContent(ref="#/components/schemas/ErrorJsonResponse")
 * ),
 * @OA\Response(
 * response="403_Forbidden",
 * description="Akses ditolak. Anda tidak memiliki izin.",
 * @OA\JsonContent(ref="#/components/schemas/ErrorJsonResponse")
 * ),
 * @OA\Response(
 * response="404_NotFound",
 * description="Sumber daya yang diminta tidak ditemukan.",
 * @OA\JsonContent(ref="#/components/schemas/ErrorJsonResponse")
 * ),
 * @OA\Response(
 * response="422_UnprocessableContent",
 * description="Validasi gagal.",
 * @OA\JsonContent(ref="#/components/schemas/ValidationErrorJsonResponse")
 * ),
 * @OA\Response(
 * response="500_InternalServerError",
 * description="Terjadi error di sisi server.",
 * @OA\JsonContent(ref="#/components/schemas/ErrorJsonResponse")
 * )
 * )
 */

abstract class Controller
{
    //
}
