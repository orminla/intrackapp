<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Admin;
use App\Models\Portfolio;
use App\Models\Department;
use App\Models\User;
use App\Models\PendingUser;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        // Ambil semua admin beserta relasi user & portfolio -> department
        $admins = Admin::with(['user', 'portfolio.department'])->get();

        $formatted = $admins->map(function ($admin) {
            return [
                'admin_id'      => $admin->admin_id,
                'nip'           => $admin->nip,
                'name'          => $admin->name,
                'phone_num'     => $admin->phone_num,
                'email'         => $admin->user?->email,
                'portfolio_id'  => $admin->portfolio_id,
                'portfolio'     => $admin->portfolio?->name,
                'department'    => $admin->portfolio?->department?->name,
            ];
        });

        // Jika request API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $formatted,
            ]);
        }

        // Untuk tampilan web
        $departments = Department::all();
        $portfolios = Portfolio::with('department')->get();

        return view('admin.settings', [
            'admins' => $formatted,
            'departments' => $departments,
            'portfolios' => $portfolios,
        ]);
    }

    // public function store(Request $request)
    // {
    //     // Hanya admin yang boleh menambahkan admin lain
    //     $user = Auth::user();
    //     if ($user->role !== 'admin') {
    //         $message = 'Hanya admin yang dapat menambahkan admin lain.';
    //         return $request->expectsJson()
    //             ? response()->json(['success' => false, 'message' => $message], 403)
    //             : abort(403, $message);
    //     }

    //     // Validasi input
    //     $validated = $request->validate([
    //         'name'          => 'required|string|max:255',
    //         'nip'           => 'required|string|max:100|unique:admins,nip',
    //         'phone_num'     => 'required|string|max:20',
    //         'portfolio_id'  => 'required|exists:portfolios,portfolio_id',
    //         'department_id' => 'required|exists:departments,department_id',
    //         'email'         => 'required|email|unique:users,email',
    //     ]);

    //     // Format nomor HP
    //     $phone = $validated['phone_num'];
    //     if (strpos($phone, '08') === 0) {
    //         $phone = '62' . substr($phone, 1);
    //     }

    //     // Password default: nama depan + 123
    //     $firstName = strtolower(strtok($validated['name'], ' '));
    //     $defaultPassword = $firstName . '123';

    //     // Buat akun user baru
    //     $newUser = User::create([
    //         'email'    => $validated['email'],
    //         'password' => Hash::make($defaultPassword),
    //         'role'     => 'admin',
    //     ]);

    //     // Buat admin dengan user_id
    //     $admin = Admin::create([
    //         'users_id'      => $newUser->id,
    //         'name'          => $validated['name'],
    //         'phone_num'     => $phone,
    //         'nip'           => $validated['nip'],
    //         'portfolio_id'  => $validated['portfolio_id'],
    //         'department_id' => $validated['department_id'],
    //     ]);

    //     // Jika request API
    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Admin dan akun berhasil dibuat.',
    //             'data' => [
    //                 'admin'            => $admin,
    //                 'akun'             => $newUser,
    //                 'default_password' => $defaultPassword
    //             ]
    //         ], 201);
    //     }

    //     // Jika request web
    //     return redirect()->back()->with([
    //         'success' => 'Admin baru berhasil ditambahkan.',
    //         'default_password' => $defaultPassword,
    //     ]);
    // }

    public function store(Request $request)
    {
        // Hanya admin yang boleh menambahkan admin lain
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $message = 'Hanya admin yang dapat menambahkan admin lain.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : abort(403, $message);
        }

        // Validasi input
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'nip'           => 'required|string|max:100|unique:admins,nip|unique:pending_users,nip',
            'phone_num'     => 'required|string|max:20',
            'portfolio_id'  => 'required|exists:portfolios,portfolio_id',
            'department_id' => 'required|exists:departments,department_id',
            'email'         => 'required|email|unique:users,email|unique:pending_users,email',
        ]);

        // Format nomor HP
        $phone = $validated['phone_num'];
        if (strpos($phone, '08') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        // Buat password default
        $firstName = strtolower(strtok($validated['name'], ' '));
        $defaultPassword = $firstName . '123';

        // Buat token verifikasi unik
        $token = Str::random(40);

        // Simpan ke pending_users
        $pending = PendingUser::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone_num'     => $phone,
            'role'          => 'admin',
            'nip'           => $validated['nip'],
            'portfolio_id'  => $validated['portfolio_id'],
            'password_plain' => $defaultPassword,
            'verif_token'   => $token,
            'expired_at'    => now()->addDays(2),
        ]);

        // Kirim WA (gunakan controller Auth)
        $verif = new EmailVerificationController();
        $verifLink = url('/verify-email?token=' . $token);
        $verif->sendVerificationLink($pending);

        // Jika request API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data admin pending berhasil dibuat. Silakan verifikasi email.',
                'verifikasi_link' => $verifLink,
            ], 201);
        }

        // Jika request Web
        return redirect()->back()->with([
            'success' => 'Admin berhasil ditambahkan. Tunggu verifikasi email.',
            'verifikasi_link' => $verifLink,
        ]);
    }

    public function destroy($admin_id)
    {
        $admin = Admin::findOrFail($admin_id);

        // Cegah hapus admin terakhir
        if (Admin::count() <= 1) {
            return back()->with('error', 'Minimal harus ada 1 admin.');
        }

        $admin->user()->delete(); // Hapus dari tabel users juga
        $admin->delete();

        return redirect()->route('admin.pengaturan')->with('success', 'Admin berhasil dihapus.');
    }

    public function show($nip)
    {
        $inspector = Inspector::with(['portfolio.department'])->where('nip', $nip)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $inspector,
        ]);
    }

    public function update(Request $request, $nip)
    {
        $admin = Admin::where('nip', $nip)->firstOrFail();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'nip'          => 'required|string|max:25|unique:admins,nip,' . $admin->admin_id . ',admin_id',
            'email'        => 'required|email|unique:users,email,' . $admin->users_id . ',id',
            'phone_num'    => 'required|string|max:20',
            'portfolio_id' => 'required|exists:portfolios,portfolio_id',
        ]);


        // Format ulang nomor HP
        $phone = $validated['phone_num'];
        if (strpos($phone, '08') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        // Update Admin
        $admin->update([
            'name'         => $validated['name'],
            'nip'          => $validated['nip'],
            'phone_num'    => $phone,
            'portfolio_id' => $validated['portfolio_id'],
        ]);

        // Update User (email)
        $user = $admin->user;
        $user->update([
            'email' => $validated['email'],
        ]);


        return redirect()->back()->with('success', 'Data admin berhasil diperbarui.');
    }
}
