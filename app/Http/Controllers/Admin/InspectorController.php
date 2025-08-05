<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Imports\InspectorImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Inspector;
use App\Models\Department;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\PendingUser;


class InspectorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        // Ambil semua inspector dengan relasi portfolio dan department
        $inspectors = Inspector::with(['portfolio.department'])->get();

        $formatted = $inspectors->map(function ($inspector) {
            // Hitung beban kerja: jumlah jadwal yang status belum selesai
            $bebanKerja = $inspector->schedules()
                ->where('status', '!=', 'Selesai')
                ->count();

            // Total jadwal
            $totalJadwal = $inspector->schedules()->count();

            // Jadwal selesai & disetujui
            $pekerjaanSelesai = $inspector->schedules()
                ->where('status', 'Selesai')
                ->whereHas('report', function ($q) {
                    $q->where('status', 'Disetujui');
                })
                ->count();

            // Hitung kinerja
            $kinerja = $totalJadwal > 0 ? round(($pekerjaanSelesai / $totalJadwal) * 100) : 0;

            return [
                'nip'           => $inspector->nip,
                'name'          => $inspector->name,
                'phone_num'     => $inspector->phone_num,
                'email'         => $inspector->user->email,
                'portfolio_id'  => $inspector->portfolio_id,
                'portfolio'     => $inspector->portfolio?->name,
                'department'    => $inspector->portfolio?->department?->name,
                'beban_kerja'   => $bebanKerja,
                'kinerja'       => $kinerja,
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

        // session()->flash('success', 'Data petugas berhasil diimpor.');
        return view('admin.inspectors', [
            'inspectors' => $formatted,
            'departments' => $departments,
            'portfolios' => $portfolios,
        ]);
    }

    // public function store(Request $request)
    // {
    //     // Hanya admin yang boleh
    //     $user = Auth::user();
    //     if ($user->role !== 'admin') {
    //         $message = 'Hanya admin yang dapat menambahkan petugas.';
    //         return $request->expectsJson()
    //             ? response()->json(['success' => false, 'message' => $message], 403)
    //             : abort(403, $message);
    //     }

    //     // Validasi
    //     $validated = $request->validate([
    //         'name'          => 'required|string|max:255',
    //         'nip'           => 'required|string|max:100|unique:inspectors,nip',
    //         'phone_num'     => 'required|string|max:20',
    //         'department_id' => 'required|exists:departments,department_id',
    //         'portfolio_id'  => 'required|exists:portfolios,portfolio_id',
    //         'email'         => 'required|email|unique:users,email',
    //     ]);

    //     // Format nomor HP: jika dimulai dengan 08 ubah ke +62
    //     $phone = $validated['phone_num'];
    //     if (strpos($phone, '08') === 0) {
    //         $phone = '62' . substr($phone, 1);
    //     }

    //     // Buat password default: nama depan + 123
    //     $firstName = strtolower(strtok($validated['name'], ' '));
    //     $defaultPassword = $firstName . '123';

    //     // Buat user baru
    //     $newUser = User::create([
    //         'email'    => $validated['email'],
    //         'password' => Hash::make($defaultPassword),
    //         'role'     => 'inspector',
    //     ]);

    //     // Buat inspector (petugas) dengan FK users_id
    //     $inspector = Inspector::create([
    //         'nip'           => $validated['nip'],
    //         'name'          => $validated['name'],
    //         'phone_num'     => $phone,
    //         'portfolio_id'  => $validated['portfolio_id'],
    //         'users_id'       => $newUser->id,
    //     ]);

    //     // Respons API
    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Petugas dan akun berhasil dibuat.',
    //             'data' => [
    //                 'petugas'          => $inspector,
    //                 'akun'             => $newUser,
    //                 'default_password' => $defaultPassword
    //             ]
    //         ], 201);
    //     }

    //     // Respons Web
    //     return redirect()->back()->with([
    //         'success' => 'Petugas dan akun berhasil dibuat.',
    //         'default_password' => $defaultPassword,
    //     ]);
    // }

    public function store(Request $request)
    {
        // Hanya admin yang boleh menambahkan inspector
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $message = 'Hanya admin yang dapat menambahkan petugas.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : abort(403, $message);
        }

        // Validasi input
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'nip'           => 'required|string|max:100|unique:inspectors,nip|unique:pending_users,nip',
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
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'phone_num'      => $phone,
            'role'           => 'inspector',
            'nip'            => $validated['nip'],
            'portfolio_id'   => $validated['portfolio_id'],
            'password_plain' => $defaultPassword,
            'verif_token'    => $token,
            'expired_at'     => now()->addDays(2),
        ]);

        // Kirim link verifikasi (via EmailVerificationController)
        $verif = new \App\Http\Controllers\Auth\EmailVerificationController();
        $verifLink = url('/verify-email?token=' . $token);
        $verif->sendVerificationLink($pending);

        // Jika request API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Petugas pending berhasil ditambahkan. Silakan verifikasi email.',
                'verifikasi_link' => $verifLink,
            ], 201);
        }

        // Jika request Web
        return redirect()->back()->with([
            'success' => 'Petugas berhasil ditambahkan. Tunggu verifikasi email.',
            'verifikasi_link' => $verifLink,
        ]);
    }

    public function import(Request $request)
    {
        // Hanya admin yang boleh
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $message = 'Hanya admin yang dapat menambahkan petugas.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : abort(403, $message);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new InspectorImport, $request->file('file'));
        return redirect()->route('admin.petugas.index')->withSuccess('Data petugas berhasil diimpor.');
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
        $inspector = Inspector::where('nip', $nip)->firstOrFail();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone_num'    => 'required|string|max:20',
            'portfolio_id' => 'required|exists:portfolios,portfolio_id',
        ]);

        // Format ulang no HP
        $phone = $validated['phone_num'];
        if (strpos($phone, '08') === 0) {
            $phone = '+62' . substr($phone, 1);
        }

        // Update data petugas
        $inspector->update([
            'name'         => $validated['name'],
            'phone_num'    => $phone,
            'portfolio_id' => $validated['portfolio_id'],
        ]);

        return redirect()->back()->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy($nip)
    {
        try {
            $inspector = Inspector::where('nip', $nip)->firstOrFail();

            // Hapus user jika ada relasi
            if ($inspector->user) {
                $inspector->user->delete();
            }

            $inspector->delete();

            // Cek apakah request dari API (biasanya Accept: application/json)
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data petugas berhasil dihapus.'
                ], 200);
            }

            // Jika request biasa (dari web)
            return redirect()->back()->with('success', 'Data petugas berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Petugas tidak ditemukan.'
                ], 404);
            }

            return redirect()->back()->with('error', 'Petugas tidak ditemukan.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus.');
        }
    }
}
