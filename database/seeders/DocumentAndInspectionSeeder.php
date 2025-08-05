<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentAndInspectionSeeder extends Seeder
{
    public function run()
    {
        // Insert data ke tabel documents (sesuai migration)
        // DB::table('documents')->insert([
        //     'report_id' => 1,
        //     'file_path' => 'documents/report1.pdf',
        //     'upload_by' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // Update status di tabel inspections untuk schedule_id = 1
        DB::table('schedules')
            ->where('schedule_id', 1)
            ->update([
                'status' => 'Dalam Proses',
                'updated_at' => now(),
            ]);
    }
}
