<?php

namespace App\Imports;

use App\Models\PendingUser;
use App\Models\User;
use App\Models\Inspector;
use App\Models\Admin;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\EmailVerificationController;

class InspectorImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        if ($rows->count() == 0) {
            throw new \Exception("File Excel kosong atau tidak sesuai format yang diharapkan.");
        }

        $header = $rows->first()->toArray();

        $expectedHeader = [
            'nama lengkap',
            'nip',
            'jenis kelamin',
            'telepon',
            'department id',
            'portfolio id',
            'email',
        ];

        foreach ($expectedHeader as $index => $expectedColumnName) {
            if (!isset($header[$index]) || strtolower(trim($header[$index])) !== $expectedColumnName) {
                throw new \Exception("Pastikan file Excel memiliki kolom yang benar dan sesuai urutan.");
            }
        }

        // Skip header
        $rows = $rows->slice(1);

        $rows->each(function ($row, $index) {
            $rowNumber = $index + 2;

            $name  = trim($row[0]);
            $nip   = trim($row[1]);
            $gender = strtolower(trim($row[2]));
            $phone = trim($row[3]);
            $departmentId = trim($row[4]);
            $portfolioId = trim($row[5]);
            $email = strtolower(trim($row[6]));

            if (!$name || !$nip || !$phone || !$departmentId || !$portfolioId || !$email) {
                throw new \Exception("Data tidak lengkap pada baris $rowNumber.");
            }

            if (!is_numeric($departmentId)) {
                throw new \Exception("Department ID harus berupa angka pada baris $rowNumber.");
            }

            if (!is_numeric($portfolioId)) {
                throw new \Exception("Portfolio ID harus berupa angka pada baris $rowNumber.");
            }

            // Format nomor HP
            if (preg_match('/^08\d+$/', $phone)) {
                $phone = '62' . substr($phone, 1);
            } elseif (preg_match('/^8\d+$/', $phone)) {
                $phone = '62' . $phone;
            }

            // Cek unik: NIP, email, nomor HP di semua tabel terkait
            if (
                User::where('email', $email)->exists() ||
                Inspector::where('nip', $nip)->exists() ||
                PendingUser::where('email', $email)->exists() ||
                PendingUser::where('nip', $nip)->exists() ||
                PendingUser::where('phone_num', $phone)->exists() ||
                Inspector::where('phone_num', $phone)->exists() ||
                Admin::where('phone_num', $phone)->exists()
            ) {
                throw new \Exception("Email, NIP, atau nomor HP sudah ada di database pada baris $rowNumber.");
            }

            $password = strtolower(explode(' ', $name)[0]) . '123';
            $token = Str::random(40);

            $pending = PendingUser::create([
                'name'           => $name,
                'gender'         => $gender,
                'email'          => $email,
                'phone_num'      => $phone,
                'role'           => 'inspector',
                'nip'            => $nip,
                'department_id'  => $departmentId,
                'portfolio_id'   => $portfolioId,
                'password_plain' => $password,
                'verif_token'    => $token,
                'expired_at'     => now()->addDays(2),
            ]);

            (new EmailVerificationController())->sendVerificationLink($pending);
        });
    }
}
