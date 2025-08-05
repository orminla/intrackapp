<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user dulu
        $user = User::create([
            'email' => 'bayu@example.com',
            'password' => Hash::make('bayu123'),
            'role' => 'admin'
        ]);

        // Buat admin dan hubungkan ke user
        Admin::create([
            'nip' => '198809282022011001',
            'name' => 'Bayu Harjoko Adimulyo',
            'phone_num' => '6281234567890',
            'portfolio_id' => 1,
            'users_id' => $user->id
        ]);
    }
}
