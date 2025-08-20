<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorinciCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('motorinci_categories')->truncate();

        $categories = [
            [
                'name' => 'Sport Bike',
                'desc' => 'Kategori motor dengan performa tinggi, dirancang untuk kecepatan dan aerodinamika.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Naked Bike',
                'desc' => 'Motor sport tanpa fairing (penutup mesin), yang menonjolkan desain mesin dan rangka. Posisi berkendara lebih tegak.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cruiser',
                'desc' => 'Motor dengan gaya klasik ala Amerika, setang tinggi, posisi duduk rendah, dan santai.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Touring',
                'desc' => 'Motor yang dirancang khusus untuk perjalanan jarak jauh, dilengkapi dengan fitur kenyamanan dan ruang penyimpanan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Adventure',
                'desc' => 'Motor serbaguna yang cocok untuk jalanan aspal dan off-road. Menggabungkan fitur touring dan dual-sport.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dual-Sport',
                'desc' => 'Motor yang legal di jalan raya tetapi memiliki kemampuan off-road. Ringan dan serbaguna.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Off-road',
                'desc' => 'Motor yang dirancang khusus untuk medan off-road, seperti motocross dan enduro.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Scooter',
                'desc' => 'Motor dengan bodi skuter, posisi duduk tegak, dan transmisi otomatis. Umumnya digunakan untuk mobilitas perkotaan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Underbone',
                'desc' => 'Kategori motor yang populer di Asia Tenggara, dengan rangka "tulang punggung" dan transmisi semi-otomatis atau manual. Dikenal juga sebagai motor bebek.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Electric',
                'desc' => 'Motor yang menggunakan tenaga listrik sebagai sumber penggerak, ramah lingkungan dan efisien.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('motorinci_categories')->insert($categories);
    }
}
