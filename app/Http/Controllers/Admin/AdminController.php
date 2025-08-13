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

        $search = $request->input('search');

        $query = Admin::with(['user', 'portfolio.department']);

        // Filter jika ada keyword search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%") // Nama
                    ->orWhere('nip', 'like', "%{$search}%") // NIP
                    ->orWhere('phone_num', 'like', "%{$search}%") // Nomor HP
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%"); // Email
                    })
                    ->orWhereHas('portfolio', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%") // Bidang / Portfolio
                            ->orWhereHas('department', function ($d) use ($search) {
                                $d->where('name', 'like', "%{$search}%"); // Departemen
                            });
                    });
            });
        }

        $admins = $query->get();

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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $formatted,
            ]);
        }

        $departments = Department::all();
        $portfolios = Portfolio::with('department')->get();

        return view('admin.settings', [
            'admins' => $formatted,
            'departments' => $departments,
            'portfolios' => $portfolios,
            'search' => $search, // biar bisa isi kembali di form
        ]);
    }

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
            'nip' => [
                'required',
                'string',
                'size:18',
                'regex:/^\d{18}$/',
                'unique:inspectors,nip',
                'unique:pending_users,nip',
            ],
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
                'message' => 'Admin berhasil ditambahkan. Menunggu verifikasi akun.',
                'verifikasi_link' => $verifLink,
            ], 201);
        }

        // Jika request Web
        return redirect()->back()->with([
            'success' => 'Admin berhasil ditambahkan. Tunggu verifikasi akun.',
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
