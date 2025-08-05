<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Portfolio;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Inspeksi Teknik dan Umum',
                'portfolios' => [
                    'AEBT - Aset dan Energi Baru dan Terbarukan',
                    'HMPM - Hulu Migas dan Produk Migas',
                    'IND - Industri',
                    'PIK - Perdagangan, Industri, dan Kelautan',
                    'LSI - Layanan Publik, SDA, dan Investasi',
                    'KSP - Komoditi Solusi dan Perdagangan',
                ],
            ],
            [
                'name' => 'Inspeksi dan Pengujian',
                'portfolios' => [
                    'LAB - Laboratorium',
                    'SERCO - Sertifikasi dan ECO Framework',
                    'MIN - Mineral',
                    'BTBR - Batu Bara',
                ],
            ]
        ];

        foreach ($departments as $data) {
            $dept = Department::create([
                'name' => $data['name']
            ]);

            if (!$dept) {
                throw new \Exception("Gagal membuat department: {$data['name']}");
            }

            foreach ($data['portfolios'] as $portfolio) {
                Portfolio::create([
                    'department_id' => $dept->department_id,
                    'name' => $portfolio
                ]);
            }
        }
    }
}
