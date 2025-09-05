<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            AdminSeeder::class,
            InspectorSeeder::class,
            // PartnerSeeder::class,
            // ProductSeeder::class,
            // DetailProductSeeder::class,
            // ScheduleSeeder::class,
            // ScheduleDetailSeeder::class,
            // ReportSeeder::class,
            // InspectorChangeRequestSeeder::class,
            // DocumentAndInspectionSeeder::class,
        ]);
    }
}
