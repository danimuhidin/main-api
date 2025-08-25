<?php

namespace Database\Seeders;

use App\Models\Motorinci\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Merah', 'hex' => '#FF0000'],
            ['name' => 'Hitam', 'hex' => '#000000'],
            ['name' => 'Putih', 'hex' => '#FFFFFF'],
            ['name' => 'Biru', 'hex' => '#0000FF'],
            ['name' => 'Kuning', 'hex' => '#FFFF00'],
            ['name' => 'Abu-abu', 'hex' => '#808080'],
            ['name' => 'Oranye', 'hex' => '#FFA500'],
            ['name' => 'Hijau', 'hex' => '#008000'],
        ];

        // Masukkan data ke tabel menggunakan Eloquent
        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
