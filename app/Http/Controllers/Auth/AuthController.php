<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Admin;
use App\Models\Inspector;
use App\Models\PasswordResetToken;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login gagal. Email atau password salah.'
                ], 401);
            }
            return back()->with('error', 'Email atau password salah');
        }

        $user = Auth::user();
        $user->makeHidden(['id', 'created_at', 'updated_at']);

        // Ambil nama dari tabel relasi sesuai role
        $name = $user->name; // fallback
        if ($user->role === 'admin' && $user->admin) {
            $name = $user->admin->name; // kolom name di tabel admins
        } elseif (($user->role === 'inspector' || $user->role === 'petugas') && $user->inspector) {
            $name = $user->inspector->name; // kolom name di tabel inspectors
        }

        $request->session()->regenerate();

        // Jika AJAX request
        if ($request->expectsJson()) {
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'role' => $user->role,
                    'name' => $name,
                ],
            ]);
        }

        // Login web biasa
        $message = "Selamat datang $name!";
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('success', $message),
            'inspector', 'petugas' => redirect()->route('inspector.dashboard')->with('success', $message),
            default => abort(403),
        };
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            // Logout API: hapus token aktif
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout berhasil'
            ]);
        }

        // Logout Web: hapus session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Hapus cookie remember me secara eksplisit
        Cookie::queue(Cookie::forget(Auth::getRecallerName()));

        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forget_modal');
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_num' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Nomor telepon wajib diisi.'], 422);
        }

        $phoneNum = $request->input('phone_num');
        $phoneNum = preg_replace('/[^0-9]/', '', $request->input('phone_num'));

        // Kalau diawali 0 → ganti jadi 62
        if (str_starts_with($phoneNum, '0')) {
            $phoneNum = '62' . substr($phoneNum, 1);
        }

        $admin = Admin::where('phone_num', $phoneNum)->first();
        $inspector = Inspector::where('phone_num', $phoneNum)->first();

        if (!$admin && !$inspector) {
            return response()->json(['message' => 'Nomor telepon tidak ditemukan.'], 404);
        }

        $user = $admin ? $admin->user : $inspector->user;

        $token = rand(100000, 999999);
        $expiredAt = Carbon::now()->addMinutes(10);

        PasswordResetToken::where('user_id', $user->id)
            ->where('is_used', false)
            ->delete();

        PasswordResetToken::create([
            'token' => $token,
            'user_id' => $user->id,
            'expired_at' => $expiredAt,
            'is_used' => false,
        ]);

        try {
            Http::withHeaders([
                'Authorization' => 'uf1VVEf2S7DGDWMfS5Ry'
            ])->post('https://api.fonnte.com/send', [
                'target' => $phoneNum,
                'message' => "Kode OTP Anda adalah: {$token}. Berlaku selama 10 menit.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim OTP.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'OTP dikirim ke WhatsApp.',
            'success' => true,
            'user_id' => $user->id,
        ]);
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset_modal', [
            'token' => $request->token,
            'user_id' => $request->user_id,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'user_id' => 'required|exists:users,id',
            'password' => 'required|confirmed|min:6',
        ]);

        $record = PasswordResetToken::where('user_id', $request->user_id)
            ->where('token', $request->token)
            ->where('is_used', false)
            ->where('expired_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Token tidak valid atau kadaluarsa.']);
        }

        $user = User::findOrFail($request->user_id);
        $user->update(['password' => Hash::make($request->password)]);

        // Tandai token sudah digunakan
        $record->update(['is_used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
        ]);

        $record = PasswordResetToken::where('user_id', $request->user_id)
            ->where('token', $request->token)
            ->where('is_used', false)
            ->where('expired_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'OTP salah atau kedaluwarsa.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP valid.',
            'token' => $record->token,
            'user_id' => $record->user_id,
        ]);
    }
}
