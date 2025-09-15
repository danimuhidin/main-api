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
                \"data_brand\": [
                { \"id\": 1, \"name\": \"Honda\" },
                { \"id\": 2, \"name\": \"Yamaha\" },
                { \"id\": 3, \"name\": \"Kawasaki\" },
                { \"id\": 4, \"name\": \"Suzuki\" },
                { \"id\": 5, \"name\": \"Harley-Davidson\" },
                { \"id\": 6, \"name\": \"Ducati\" },
                { \"id\": 7, \"name\": \"BMW Motorrad\" },
                { \"id\": 8, \"name\": \"Triumph\" },
                { \"id\": 9, \"name\": \"KTM\" },
                { \"id\": 10, \"name\": \"Royal Enfield\" },
                { \"id\": 11, \"name\": \"Benelli\" },
                { \"id\": 12, \"name\": \"Aprilia\" },
                { \"id\": 13, \"name\": \"Rahayu 5\" },
                { \"id\": 14, \"name\": \"Indo\" }
                ],
                \"data_category\": [
                { \"id\": 1, \"name\": \"Sport Bike\" },
                { \"id\": 2, \"name\": \"Naked Bike\" },
                { \"id\": 3, \"name\": \"Cruiser\" },
                { \"id\": 4, \"name\": \"Touring\" },
                { \"id\": 5, \"name\": \"Adventure\" },
                { \"id\": 6, \"name\": \"Dual-Sport\" },
                { \"id\": 7, \"name\": \"Off-road\" },
                { \"id\": 8, \"name\": \"Scooter\" },
                { \"id\": 9, \"name\": \"Underbone\" },
                { \"id\": 10, \"name\": \"Electric\" }
                ],
                \"data_color\": [
                { \"id\": 1, \"name\": \"Merah\" },
                { \"id\": 2, \"name\": \"Hitam\" },
                { \"id\": 3, \"name\": \"Putih\" },
                { \"id\": 4, \"name\": \"Biru\" },
                { \"id\": 5, \"name\": \"Kuning\" },
                { \"id\": 6, \"name\": \"Abu-abu\" },
                { \"id\": 7, \"name\": \"Oranye\" },
                { \"id\": 8, \"name\": \"Hijau\" }
                ],
                \"data_feature\": [
                { \"id\": 1, \"name\": \"Secure Key Shutter\" },
                { \"id\": 2, \"name\": \"Power Outlet/USB Charger\" },
                { \"id\": 3, \"name\": \"Adjustable Windshield\" },
                { \"id\": 4, \"name\": \"Riding Modes\" },
                { \"id\": 5, \"name\": \"Traction Control System (TCS)\" },
                { \"id\": 6, \"name\": \"Quick Shifter\" },
                { \"id\": 7, \"name\": \"Smart Key System\" },
                { \"id\": 8, \"name\": \"Alarm\" },
                { \"id\": 9, \"name\": \"Side Stand Switch\" }
                ],
                \"data_specification_item\": [
                { \"id\": 2, \"name\": \"Tipe Mesin\" }, { \"id\": 3, \"name\": \"Sistem Pendingin\" }, { \"id\": 4, \"name\": \"Konfigurasi Katup\" }, { \"id\": 5, \"name\": \"Jumlah Silinder\" }, { \"id\": 6, \"name\": \"Konfigurasi Silinder\" }, { \"id\": 7, \"name\": \"Diameter x Langkah\" }, { \"id\": 8, \"name\": \"Rasio Kompresi\" }, { \"id\": 9, \"name\": \"Daya Maksimum\" }, { \"id\": 10, \"name\": \"Torsi Maksimum\" }, { \"id\": 11, \"name\": \"Sistem Bahan Bakar\" }, { \"id\": 12, \"name\": \"Tipe Kopling\" }, { \"id\": 13, \"name\": \"Tipe Transmisi\" }, { \"id\": 14, \"name\": \"Jumlah Percepatan\" }, { \"id\": 15, \"name\": \"Sistem Starter\" }, { \"id\": 16, \"name\": \"Kapasitas Oli Mesin\" }, { \"id\": 17, \"name\": \"Tipe Rangka\" }, { \"id\": 18, \"name\": \"Panjang x Lebar x Tinggi\" }, { \"id\": 19, \"name\": \"Berat\" }, { \"id\": 20, \"name\": \"Suspensi Depan\" }, { \"id\": 21, \"name\": \"Suspensi Belakang\" }, { \"id\": 22, \"name\": \"Rem Depan\" }, { \"id\": 23, \"name\": \"Ukuran Piringan Depan\" }, { \"id\": 24, \"name\": \"Tipe Kaliper Depan\" }, { \"id\": 25, \"name\": \"Rem Belakang\" }, { \"id\": 26, \"name\": \"Ukuran Piringan Belakang\" }, { \"id\": 27, \"name\": \"Tipe Kaliper Belakang\" }, { \"id\": 28, \"name\": \"Sistem Pengereman Tambahan\" }, { \"id\": 29, \"name\": \"Channel ABS\" }, { \"id\": 30, \"name\": \"Ukuran Ban Depan\" }, { \"id\": 31, \"name\": \"Ukuran Ban Belakang\" }, { \"id\": 32, \"name\": \"Tipe Ban\" }, { \"id\": 33, \"name\": \"Tipe Velg\" }, { \"id\": 34, \"name\": \"Ukuran Velg Depan\" }, { \"id\": 35, \"name\": \"Ukuran Velg Belakang\" }, { \"id\": 36, \"name\": \"Sistem Pengapian\" }, { \"id\": 37, \"name\": \"Tipe Baterai/Aki\" }, { \"id\": 38, \"name\": \"Lampu Depan\" }, { \"id\": 39, \"name\": \"Lampu Belakang\" }, { \"id\": 40, \"name\": \"Lampu Sein\" }, { \"id\": 41, \"name\": \"Tipe Panel Meter\" }, { \"id\": 42, \"name\": \"Indikator Panel Meter\" }, { \"id\": 43, \"name\": \"Tangki Bahan Bakar\" }, { \"id\": 44, \"name\": \"Air Pendingin\" }, { \"id\": 45, \"name\": \"Bagasi\" }, { \"id\": 46, \"name\": \"Bahan Bakar\" }
                ]
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
