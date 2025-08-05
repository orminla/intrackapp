<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleDetailSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('schedule_details')->insert([
            // Jadwal 1 (produk_id 1 = Coconut Palm Oil), ambil 1-2 detail
            [
                'schedule_id' => 1,
                'detail_id' => 1, // Moisture Content Test
            ],
            [
                'schedule_id' => 1,
                'detail_id' => 2, // FFA Test
            ],

            // Jadwal 2 (produk_id 2 = Refined Sugar), ambil 3-4 detail
            [
                'schedule_id' => 2,
                'detail_id' => 3, // Polarity Test
            ],
            [
                'schedule_id' => 2,
                'detail_id' => 4, // Color Intensity Test
            ],

            // Jadwal 3 (produk_id 4 = Shipping Vessel), ambil 7-8 detail
            [
                'schedule_id' => 3,
                'detail_id' => 7, // Bongkar Muat
            ],
            [
                'schedule_id' => 3,
                'detail_id' => 8, // Pemeriksaan Dokumen Kapal
            ],
        ]);
    }
}
