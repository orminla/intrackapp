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
                'phone_num' => '6287742393890',
                'portfolio_id' => 1,
                'password' => 'agus123',
                'gender' => 'Laki-laki'
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti@example.com',
                'nip' => '199203152022011002',
                'phone_num' => '6287742393890',
                'portfolio_id' => 2,
                'password' => 'siti123',
                'gender' => 'Perempuan'
            ],
            [
                'name' => 'Budi Wijaya',
                'email' => 'budi@example.com',
                'nip' => '199509202022011003',
                'phone_num' => '6287742393890',
                'portfolio_id' => 3,
                'password' => 'budi123',
                'gender' => 'Laki-laki'
            ],
            [
                'name' => 'Rina Marlina',
                'email' => 'rina@example.com',
                'nip' => '199301122022011004',
                'phone_num' => '6287742393890',
                'portfolio_id' => 4,
                'password' => 'rina123',
                'gender' => 'Perempuan'
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi@example.com',
                'nip' => '198812152022011005',
                'phone_num' => '6287742393890',
                'portfolio_id' => 5,
                'password' => 'dedi123',
                'gender' => 'Laki-laki'
            ],
            [
                'name' => 'Wulan Septiani',
                'email' => 'wulan@example.com',
                'nip' => '199611102022011006',
                'phone_num' => '6287742393890',
                'portfolio_id' => 6,
                'password' => 'wulan123',
                'gender' => 'Perempuan'
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar@example.com',
                'nip' => '199404202022011007',
                'phone_num' => '6287742393890',
                'portfolio_id' => 7,
                'password' => 'fajar123',
                'gender' => 'Laki-laki'
            ],
            [
                'name' => 'Lestari Ayu',
                'email' => 'lestari@example.com',
                'nip' => '199208112022011008',
                'phone_num' => '6287742393890',
                'portfolio_id' => 8,
                'password' => 'lestari123',
                'gender' => 'Perempuan'
            ],
            [
                'name' => 'Andi Saputra',
                'email' => 'andi@example.com',
                'nip' => '199701302022011009',
                'phone_num' => '6287742393890',
                'portfolio_id' => 9,
                'password' => 'andi123',
                'gender' => 'Laki-laki'
            ],
            [
                'name' => 'Nurul Hikmah',
                'email' => 'nurul@example.com',
                'nip' => '199605282022011010',
                'phone_num' => '6287742393890',
                'portfolio_id' => 10,
                'password' => 'nurul123',
                'gender' => 'Perempuan'
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
                'users_id' => $user->id,
                'gender' => $data['gender']
            ]);
        }
    }
}
