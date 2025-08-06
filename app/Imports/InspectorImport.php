<?php

namespace App\Imports;

use App\Models\PendingUser;
use App\Models\User;
use App\Models\Inspector;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\EmailVerificationController;

class InspectorImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Lewati baris header
        $rows->skip(1)->each(function ($row, $index) {
            try {
                $name  = trim($row[0]);  // Kolom A: Nama
                $nip   = trim($row[1]);  // Kolom B: NIP
                $phone = trim($row[2]);  // Kolom C: Telepon
                $portfolioId = $row[4];  // Kolom E: ID Portofolio
                $email = strtolower(trim($row[5])); // Kolom F: Email

                // Skip jika data kosong
                if (!$name || !$nip || !$phone || !$portfolioId || !$email) {
                    Log::warning("Baris ke-" . ($index + 2) . " dilewati: Data kosong.");
                    return;
                }

                // Cek duplikat email/nip di users, inspectors, dan pending_users
                if (
                    User::where('email', $email)->exists() ||
                    Inspector::where('nip', $nip)->exists() ||
                    PendingUser::where('email', $email)->exists() ||
                    PendingUser::where('nip', $nip)->exists()
                ) {
                    Log::warning("Baris ke-" . ($index + 2) . " dilewati: Email atau NIP sudah digunakan ($email / $nip).");
                    return;
                }

                // Format nomor HP
                if (preg_match('/^8\d+$/', $phone)) {
                    $phone = '62' . $phone;
                } elseif (preg_match('/^0\d+$/', $phone)) {
                    $phone = '62' . substr($phone, 1);
                }

                // Generate password default dan token
                $password = strtolower(explode(' ', $name)[0]) . '123';
                $token = Str::random(40);

                // Simpan ke pending_users
                $pending = PendingUser::create([
                    'name'           => $name,
                    'email'          => $email,
                    'phone_num'      => $phone,
                    'role'           => 'inspector',
                    'nip'            => $nip,
                    'portfolio_id'   => $portfolioId,
                    'password_plain' => $password,
                    'verif_token'    => $token,
                    'expired_at'     => now()->addDays(2),
                ]);

                // Log::debug("Berhasil simpan ke pending_users: " . $pending->id);

                // Kirim link verifikasi
                $verif = new EmailVerificationController();
                $verif->sendVerificationLink($pending);

                // Log sukses
                Log::info("Berhasil import petugas: $email (Baris ke-" . ($index + 2) . ")");
            } catch (\Throwable $e) {
                Log::error('Gagal import baris ke-' . ($index + 2) . ': ' . $e->getMessage());
            }
        });
    }
}
