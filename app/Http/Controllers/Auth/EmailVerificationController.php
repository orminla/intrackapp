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

        // Normalisasi nomor WA
        $rawPhone = preg_replace('/[^0-9]/', '', $pending->phone_num);
        if (substr($rawPhone, 0, 1) === '0') {
            $formattedPhone = '62' . substr($rawPhone, 1);
        } elseif (substr($rawPhone, 0, 3) === '620') {
            $formattedPhone = '62' . substr($rawPhone, 3);
        } elseif (substr($rawPhone, 0, 2) === '62') {
            $formattedPhone = $rawPhone;
        } elseif (substr($rawPhone, 0, 1) === '+') {
            $formattedPhone = substr($rawPhone, 1);
        } else {
            $formattedPhone = $rawPhone;
        }

        // Buat akun admin atau inspector
        if ($pending->role === 'admin') {
            \App\Models\Admin::create([
                'users_id' => $user->id,
                'name' => $pending->name,
                'nip' => $pending->nip,
                'phone_num' => $formattedPhone,
                'portfolio_id' => $pending->portfolio_id,
                'department_id' => $pending->department_id,
            ]);
        } elseif ($pending->role === 'inspector') {
            \App\Models\Inspector::create([
                'users_id' => $user->id,
                'name' => $pending->name,
                'nip' => $pending->nip,
                'phone_num' => $formattedPhone,
                'portfolio_id' => $pending->portfolio_id,
                'department_id' => $pending->department_id,
            ]);
        }

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
