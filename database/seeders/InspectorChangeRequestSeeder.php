<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectorChangeRequestSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inspector_change_requests')->insert([
            [
                'schedule_id'       => 1,
                'old_inspector_id'  => 1, // BIP
                'new_inspector_id'  => 2, // BITU
                'requested_date'    => '2025-07-09',
                'status'            => 'Menunggu',
                'reason'            => 'Berhalangan karena tugas luar kota',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'schedule_id'       => 3,
                'old_inspector_id'  => 2, // BITU
                'new_inspector_id'  => 1, // BIP
                'requested_date'    => '2025-07-09',
                'status'            => 'Menunggu',
                'reason'            => 'Jadwal bertabrakan dengan inspeksi sebelumnya',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        ]);
    }
}
