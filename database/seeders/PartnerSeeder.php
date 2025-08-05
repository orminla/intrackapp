<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('partners')->insert([
            [
                'partner_id' => 1,
                'name' => 'PT Bunga Laut',
                'address' => 'Jl. Pelabuhan No. 1, Surabaya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'partner_id' => 2,
                'name' => 'CV Gula Prima',
                'address' => 'Jl. Industri Gula No. 5, Gresik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
