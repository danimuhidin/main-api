<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\DB;
use App\Models\Motorinci\AvailableColor;
use App\Models\Motorinci\Motor;
use App\Models\Motorinci\MotorFeature;
use App\Models\Motorinci\MotorSpecification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class AiController extends Controller
{
    public function generate()
    {
        // 1. Menggunakan Query Builder sesuai permintaan
        $type = DB::table('type')->where('status', 1)->first();

        if (!$type) {
            return response()->json(['error' => 'Tidak ada data motor baru untuk diproses.'], 404);
        }

        try {
            // Memulai transaksi database
            $generatedData = DB::transaction(function () use ($type) {

                $prompt = "
                Anda adalah AI asisten data otomotif yang sangat akurat. Tugas Anda adalah memberikan data spesifikasi lengkap untuk model sepeda motor tertentu. Anda HARUS menghasilkan output dalam format JSON tunggal yang valid tanpa teks atau penjelasan tambahan.

                Gunakan data master di bawah ini untuk mengisi nilai brand_id, category_id, serta ID dalam array colors dan features. Untuk array specifications, gunakan id dari data specification_item sebagai nilai spec_item_id.

                Data Master Referensi:
                {
                \"data_brand\": [ { \"id\": 1, \"name\": \"Honda\" }, ... ],
                \"data_category\": [ { \"id\": 1, \"name\": \"Sport Bike\" }, ... ],
                \"data_color\": [ { \"id\": 1, \"name\": \"Merah\" }, ... ],
                \"data_feature\": [ { \"id\": 1, \"name\": \"Secure Key Shutter\" }, ... ],
                \"data_specification_item\": [ { \"id\": 2, \"name\": \"Tipe Mesin\" }, ... ]
                }

                Struktur JSON Output yang Diinginkan:
                {
                \"name\": \"Nama Motor Tanpa Brand\",
                \"brand_id\": 1,
                \"category_id\": 2,
                \"year_model\": 2012,
                \"engine_cc\": 200,
                \"low_price\": 28000000,
                \"up_price\": 30000000,
                \"desc\": \"Salah satu motor sport legendaris di Indonesia yang dikenal dengan julukan MACAN\",
                \"colors\": [ /* Array of integer ID dari data_color */ ],
                \"features\": [ /* Array of integer ID dari data_feature */ ],
                \"specifications\": [ { \"spec_item_id\": 2, \"value\": \"Nilai spesifikasi\" }, ... ]
                }

                Instruksi Penting:
                1. Isi semua field berdasarkan data yang paling akurat untuk model dan tahun yang ditentukan.
                2. Untuk name, hanya sertakan nama modelnya (contoh: \"Tiger Revo\"), bukan brand-nya.
                3. Jika sebuah item spesifikasi tidak berlaku, jangan sertakan item tersebut dalam array specifications.
                4. Jika warna atau fitur pabrikan tidak ada dalam daftar data master, jangan dimasukkan.
                5. Pastikan seluruh output Anda hanyalah kode JSON, dimulai dengan `{` dan diakhiri dengan `}`.

                Sekarang, proses permintaan untuk sepeda motor berikut:
                $type->name
                ";

                $result = Gemini::generativeModel('gemini-1.5-flash-latest')->generateContent($prompt);
                $responseText = $result->text();

                preg_match('/\{[\s\S]*\}/', $responseText, $matches);
                if (!isset($matches[0])) {
                    throw new Exception("Tidak dapat menemukan format JSON yang valid. Respons mentah: " . $responseText);
                }

                $jsonString = $matches[0];
                $data = json_decode($jsonString, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Gagal mem-parsing JSON. Error: " . json_last_error_msg());
                }

                // Validasi data dari AI tetap sangat penting
                $validator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'brand_id' => 'required|integer|exists:motorinci_brands,id',
                    'category_id' => 'required|integer|exists:motorinci_categories,id',
                    'year_model' => 'required|integer|min:1900',
                    'engine_cc' => 'required|integer',
                    'low_price' => 'required|numeric',
                    'up_price' => 'required|numeric',
                    'desc' => 'required|string',
                    'colors' => 'nullable|array',
                    'colors.*' => 'integer|exists:motorinci_colors,id',
                    'features' => 'nullable|array',
                    'features.*' => 'integer|exists:motorinci_feature_items,id',
                    'specifications' => 'nullable|array',
                    'specifications.*.spec_item_id' => 'required|integer|exists:motorinci_specification_items,id',
                    'specifications.*.value' => 'required|string',
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $validatedData = $validator->validated();

                // 2. Proses penyimpanan menggunakan new Model() dan save()
                $motor = new Motor();
                $motor->name = $validatedData['name'];
                $motor->year_model = $validatedData['year_model'];
                $motor->brand_id = $validatedData['brand_id'];
                $motor->category_id = $validatedData['category_id'];
                $motor->engine_cc = $validatedData['engine_cc'];
                $motor->low_price = $validatedData['low_price'];
                $motor->up_price = $validatedData['up_price'];
                $motor->desc = $validatedData['desc'];
                $motor->save();

                if (!empty($validatedData['colors'])) {
                    foreach ($validatedData['colors'] as $colorId) {
                        $motorColors = new AvailableColor();
                        $motorColors->motor_id = $motor->id;
                        $motorColors->color_id = $colorId;
                        $motorColors->save();
                    }
                }

                if (!empty($validatedData['features'])) {
                    foreach ($validatedData['features'] as $featureId) {
                        $motorFeatures = new MotorFeature();
                        $motorFeatures->motor_id = $motor->id;
                        $motorFeatures->feature_item_id = $featureId;
                        $motorFeatures->save();
                    }
                }

                if (!empty($validatedData['specifications'])) {
                    foreach ($validatedData['specifications'] as $spec) {
                        $motorSpec = new MotorSpecification();
                        $motorSpec->motor_id = $motor->id;
                        $motorSpec->specification_item_id = $spec['spec_item_id'];
                        $motorSpec->value = $spec['value'];
                        $motorSpec->save();
                    }
                }

                // Update status 'type' menggunakan Query Builder
                DB::table('type')->where('id', $type->id)->update(['status' => 0]);

                return $validatedData;
            });

            return response()->json([
                'message' => 'Data motor berhasil digenerate dan disimpan!',
                'data' => $generatedData
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Data yang dihasilkan oleh AI tidak valid.',
                'details' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
