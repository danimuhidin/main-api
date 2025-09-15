<?php

namespace App\Http\Controllers\Motorinci;

use App\Http\Controllers\Controller;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\DB;
use App\Models\Motorinci\AvailableColor;
use App\Models\Motorinci\Motor;
use App\Models\Motorinci\MotorFeature;
use App\Models\Motorinci\MotorSpecification;

class AiController extends Controller
{
    public function generate()
    {
        $type = DB::table('type')->where('status', 1)->first();

        try {
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
                return response()->json([
                    'error' => 'Tidak dapat menemukan format JSON yang valid dalam respons dari Gemini.',
                    'raw_response' => $responseText
                ], 500);
            }

            $jsonString = $matches[0];
            $data = json_decode($jsonString, true);
            if ($data !== null) {
                DB::table('type')->where('id', $type->id)->update(['status' => 0]);
                
                $motor = new Motor();
                $motor->name = $data['name'];
                $motor->year_model = $data['year_model'];
                $motor->brand_id = $data['brand_id'];
                $motor->category_id = $data['category_id'];
                $motor->engine_cc = $data['engine_cc'];
                $motor->low_price = $data['low_price'];
                $motor->up_price = $data['up_price'];
                $motor->desc = $data['desc'];
                $motor->save();
    
                if (isset($data['colors']) && is_array($data['colors'])) {
                    foreach ($data['colors'] as $colorId) {
                        $motorColors = new AvailableColor();
                        $motorColors->motor_id = $motor->id;
                        $motorColors->color_id = $colorId;
                        $motorColors->save();
                    }
                }
    
                if (isset($data['features']) && is_array($data['features'])) {
                    foreach ($data['features'] as $featureId) {
                        $motorFeatures = new MotorFeature();
                        $motorFeatures->motor_id = $motor->id;
                        $motorFeatures->feature_item_id = $featureId;
                        $motorFeatures->save();
                    }
                }
    
                if (isset($data['specifications']) && is_array($data['specifications'])) {
                    foreach ($data['specifications'] as $spec) {
                        $motorSpec = new MotorSpecification();
                        $motorSpec->motor_id = $motor->id;
                        $motorSpec->specification_item_id = $spec['spec_item_id'];
                        $motorSpec->value = $spec['value'];
                        $motorSpec->save();
                    }
                }
    
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'error' => 'Gagal mem-parsing JSON dari respons Gemini.',
                        'json_error_message' => json_last_error_msg(),
                        'raw_response' => $responseText
                    ], 500);
                }
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menghubungi Gemini API: ' . $e->getMessage()], 500);
        }
    }
}
