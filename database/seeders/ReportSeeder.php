<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\Schedule;
use Illuminate\Support\Carbon;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua jadwal yang ada
        // $schedules = Schedule::all();

        // foreach ($schedules as $schedule) {
        //     Report::create([
        //         'schedule_id'   => $schedule->schedule_id,
        //         'finished_date' => Carbon::parse($schedule->started_date)->addDays(rand(1, 5)),
        //         'status'        => collect(['Disetujui', 'Ditolak', 'Menunggu konfirmasi'])->random(),
        //     ]);
        // }
    }
}
