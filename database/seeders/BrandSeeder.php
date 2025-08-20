<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            [
                'name' => 'Honda',
                'desc' => 'Produsen sepeda motor terbesar di dunia, terkenal dengan keandalan dan beragam modelnya, dari skuter hingga motor sport.',
            ],
            [
                'name' => 'Yamaha',
                'desc' => 'Produsen Jepang yang dikenal dengan inovasi, performa, dan desain agresifnya, terutama di segmen motor sport dan naked bike.',
            ],
            [
                'name' => 'Kawasaki',
                'desc' => 'Dikenal dengan motor sport berperforma tinggi, seperti seri Ninja, serta motor petualangan dan retro klasik.',
            ],
            [
                'name' => 'Suzuki',
                'desc' => 'Menawarkan berbagai model yang andal, mulai dari skuter matik, motor bebek, hingga motor sport legendaris seperti GSX-R.',
            ],
            [
                'name' => 'Harley-Davidson',
                'desc' => 'Merek ikonik Amerika yang terkenal dengan motor cruiser berkapasitas besar dan desain klasiknya yang khas.',
            ],
            [
                'name' => 'Ducati',
                'desc' => 'Pabrikan Italia yang terkenal dengan motor sport mewah, performa tinggi, dan desain yang sangat stylish.',
            ],
            [
                'name' => 'BMW Motorrad',
                'desc' => 'Divisi sepeda motor dari BMW Group, dikenal dengan motor touring, adventure, dan sport yang dilengkapi teknologi canggih dari Jerman.',
            ],
            [
                'name' => 'Triumph',
                'desc' => 'Produsen Inggris yang memiliki sejarah panjang, menawarkan perpaduan motor klasik modern, roadster, dan motor petualangan.',
            ],
            [
                'name' => 'KTM',
                'desc' => 'Pabrikan Austria yang dominan di dunia off-road, juga dikenal dengan motor street berperforma tinggi dan desain yang agresif.',
            ],
            [
                'name' => 'Royal Enfield',
                'desc' => 'Merek tertua di dunia yang masih berproduksi, dikenal dengan motor bergaya retro-klasik yang tangguh dan memiliki basis penggemar yang kuat.',
            ],
            [
                'name' => 'Benelli',
                'desc' => 'Merek Italia dengan sejarah panjang, kini terkenal dengan motor sport, naked bike, dan motor touring yang terjangkau dan stylish.',
            ],
            [
                'name' => 'Aprilia',
                'desc' => 'Produsen Italia yang fokus pada motor sport, balap, dan superbike berperforma tinggi dengan teknologi mutakhir.',
            ],
        ];

        DB::table('motorinci_brands')->insert($brands);
    }
}
