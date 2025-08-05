<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailProduct;

class DetailProductSeeder extends Seeder
{
    public function run(): void
    {
        DetailProduct::insert([
            [
                'product_id' => 1,
                'name' => 'Moisture Content Test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'name' => 'Free Fatty Acid (FFA) Test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'name' => 'Polarity Test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'name' => 'Color Intensity Test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'name' => 'Protein Content Analysis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'name' => 'Gluten Strength Test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 4,
                'name' => 'Bongkar Muat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 4,
                'name' => 'Pemeriksaan Dokumen Kapal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
