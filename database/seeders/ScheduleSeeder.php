<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('schedules')->insert([
            [
                'schedule_id' => 1,
                'inspector_id' => 1, // Misal: BIP
                'partner_id' => 1,
                'product_id' => 1, // Coconut Palm Oil
                'started_date' => '2025-07-15',
                'status' => 'Dalam proses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'inspector_id' => 2, // BIP lagi
                'partner_id' => 2,
                'product_id' => 2, // Refined Sugar
                'started_date' => '2025-07-17',
                'status' => 'Menunggu konfirmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'inspector_id' => 3,
                'partner_id' => 1,
                'product_id' => 4, // Shipping Vessel
                'started_date' => '2025-07-20',
                'status' => 'Menunggu konfirmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
