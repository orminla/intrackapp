<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Inspector;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class EmailVerificationController extends Controller
{
    public function verify($token)
    {
        $pending = PendingUser::where('verif_token', $token)->first();

        if (!$pending) {
            return redirect('/login')->with('error', 'Token tidak valid.');
        }

        if (Carbon::parse($pending->expired_at)->isPast()) {
            return redirect('/login')->with('error', 'Token telah kedaluwarsa.');
        }

        // Buat akun user
        $user = User::create([
            'email' => $pending->email,
            'password' => Hash::make($pending->password_plain),
            'role' => $pending->role,
        ]);

        // Buat akun admin atau inspector
        if ($pending->role === 'admin') {
            Admin::create([
                'users_id' => $user->id,
                'name' => $pending->name,
                'nip' => $pending->nip,
                'phone_num' => $pending->phone_num,
                'portfolio_id' => $pending->portfolio_id,
                'department_id' => $pending->department_id,
            ]);
        } elseif ($pending->role === 'inspector') {
            Inspector::create([
                'users_id' => $user->id,
                'name' => $pending->name,
                'nip' => $pending->nip,
                'phone_num' => $pending->phone_num,
                'portfolio_id' => $pending->portfolio_id,
                'department_id' => $pending->department_id,
            ]);
        }

        // Kirim WA info login
        // $msg = "Selamat! Akun Anda telah aktif 🎉\n\nEmail: {$user->email}\nPassword: {$pending->password_plain}\n\nSilakan login ke sistem.\n\nTerima kasih 🙏";
        // $this->sendWhatsappMessage($pending->phone_num, $msg);

        // Hapus pending
        $pending->delete();

        return redirect('/login')->with('success', 'Verifikasi berhasil. Silakan login.');
    }

    public function sendVerificationLink(PendingUser $pending)
    {
        $link = url('/verify-email/' . $pending->verif_token);

        $message = "Halo {$pending->name},\n" .
            "Akun InTrack Anda telah didaftarkan.\n\n" .
            "Email: {$pending->email}\n" .
            "Password: {$pending->password_plain}\n\n" .
            "Silakan verifikasi akun Anda melalui link berikut:\n$link\n\n" .
            "Terima kasih 😊🙏";

        $this->sendWhatsappMessage($pending->phone_num, $message);
    }

    public function sendWhatsappMessage($phone, $message)
    {
        Http::withHeaders([
            'Authorization' => 'uf1VVEf2S7DGDWMfS5Ry',
        ])->post('https://api.fonnte.com/send', [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62',
        ]);
    }
}
