<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Inspector;

class InspectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inspectors = [
            [
                'name' => 'Agus Santoso',
                'email' => 'agus@example.com',
                'nip' => '199001012022011001',
                'phone_num' => '6281234567891',
                'portfolio_id' => 1,
                'password' => 'agus123'
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti@example.com',
                'nip' => '199203152022011002',
                'phone_num' => '6281234567892',
                'portfolio_id' => 2,
                'password' => 'siti123'
            ],
            [
                'name' => 'Budi Wijaya',
                'email' => 'budi@example.com',
                'nip' => '199509202022011003',
                'phone_num' => '6281234567893',
                'portfolio_id' => 3,
                'password' => 'budi123'
            ],
            [
                'name' => 'Rina Marlina',
                'email' => 'rina@example.com',
                'nip' => '199301122022011004',
                'phone_num' => '6281234567894',
                'portfolio_id' => 4,
                'password' => 'rina123'
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi@example.com',
                'nip' => '198812152022011005',
                'phone_num' => '6281234567895',
                'portfolio_id' => 5,
                'password' => 'dedi123'
            ],
            [
                'name' => 'Wulan Septiani',
                'email' => 'wulan@example.com',
                'nip' => '199611102022011006',
                'phone_num' => '6281234567896',
                'portfolio_id' => 6,
                'password' => 'wulan123'
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar@example.com',
                'nip' => '199404202022011007',
                'phone_num' => '6281234567897',
                'portfolio_id' => 7,
                'password' => 'fajar123'
            ],
            [
                'name' => 'Lestari Ayu',
                'email' => 'lestari@example.com',
                'nip' => '199208112022011008',
                'phone_num' => '6281234567898',
                'portfolio_id' => 8,
                'password' => 'lestari123'
            ],
            [
                'name' => 'Andi Saputra',
                'email' => 'andi@example.com',
                'nip' => '199701302022011009',
                'phone_num' => '6281234567899',
                'portfolio_id' => 9,
                'password' => 'andi123'
            ],
            [
                'name' => 'Nurul Hikmah',
                'email' => 'nurul@example.com',
                'nip' => '199605282022011010',
                'phone_num' => '6281234567900',
                'portfolio_id' => 10,
                'password' => 'nurul123'
            ],
        ];

        foreach ($inspectors as $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => 'inspector'
            ]);

            Inspector::create([
                'nip' => $data['nip'],
                'name' => $data['name'],
                'phone_num' => $data['phone_num'],
                'portfolio_id' => $data['portfolio_id'],
                'users_id' => $user->id
            ]);
        }
    }
}
