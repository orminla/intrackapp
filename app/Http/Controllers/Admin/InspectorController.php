<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\EmailVerificationController;
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

        $showing = (int) $request->input('showing', 10);
        $filter  = $request->input('filter', 'all');
        $currentPage = (int) $request->query('page', 1);

        $query = Inspector::with(['portfolio.department', 'schedules.report'])
            ->orderBy('name');

        if ($filter !== 'all') {
            $query->whereHas('schedules', function ($q) use ($filter) {
                $q->where('status', $filter);
            });
        }

        // Hitung total data
        $totalData = $query->count();

        // Kalau showing > total data, sesuaikan
        if ($totalData < $showing) {
            $showing = $totalData > 0 ? $totalData : 1;
        }

        // Kalau offset halaman sekarang melebihi total data, reset ke page 1
        if (($currentPage - 1) * $showing >= $totalData) {
            $currentPage = 1;
        }

        // Ambil data dengan pagination
        $inspectors = $query->paginate($showing, ['*'], 'page', $currentPage);

        // Map data untuk menambahkan kolom tambahan
        $formatted = $inspectors->getCollection()->map(function ($inspector) {
            $schedules = $inspector->schedules;

            $bebanKerja = $schedules->where('status', '!=', 'Selesai')->count();
            $totalJadwal = $schedules->count();

            $pekerjaanSelesai = $schedules
                ->where('status', 'Selesai')
                ->filter(function ($schedule) {
                    return $schedule->report && $schedule->report->status === 'Disetujui';
                })
                ->count();

            $kinerja = $totalJadwal > 0
                ? round(($pekerjaanSelesai / $totalJadwal) * 100)
                : 0;

            return [
                'nip'               => $inspector->nip,
                'name'              => $inspector->name,
                'phone_num'         => $inspector->phone_num,
                'email'             => $inspector->user->email,
                'portfolio_id'      => $inspector->portfolio_id,
                'portfolio'         => $inspector->portfolio?->name,
                'department'        => $inspector->portfolio?->department?->name,
                'beban_kerja'       => $bebanKerja,
                'pekerjaan_selesai' => $pekerjaanSelesai,
                'kinerja'           => $kinerja,
            ];
        });

        // Bungkus lagi ke paginator supaya pagination tetap jalan
        $inspectorsFormatted = new \Illuminate\Pagination\LengthAwarePaginator(
            $formatted,
            $inspectors->total(),
            $inspectors->perPage(),
            $inspectors->currentPage(),
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.inspectors', [
            'inspectors'  => $inspectorsFormatted,
            'departments' => Department::all(),
            'portfolios'  => Portfolio::with('department')->get(),
        ]);
    }

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
        $verif = new EmailVerificationController();
        $verifLink = url('/verify-email?token=' . $token);
        $verif->sendVerificationLink($pending);

        // Jika request API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Petugas berhasil ditambahkan. Menunggu verifikasi akun.',
                'verifikasi_link' => $verifLink,
            ], 201);
        }

        // Jika request Web
        return redirect()->back()->with('success', 'Petugas berhasil ditambahkan. Tunggu verifikasi email.');
    }

    public function import(Request $request)
    {
        try {
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

            // Response JSON jika ajax
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Import data berhasil! Menunggu konfirmasi petugas.',
                ]);
            }

            // Jika bukan ajax, redirect seperti biasa
            return redirect()->back()->with('success', 'Import data berhasil! Menunggu konfirmasi petugas.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import data gagal: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Import data gagal! ' . $e->getMessage());
        }
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
        $user = $inspector->user;

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'nip'          => [
                'required',
                'string',
                'size:18',
                'regex:/^\d{18}$/',
                'unique:inspectors,nip,' . $inspector->inspector_id . ',inspector_id'
            ],
            'email'        => 'required|email|unique:users,email,' . ($user ? $user->id : 'NULL'),
            'phone_num'    => 'required|string|max:20',
            'portfolio_id' => 'required|exists:portfolios,portfolio_id',
        ]);

        // Format nomor HP
        $phone = $validated['phone_num'];
        if (strpos($phone, '08') === 0) {
            $phone = '+62' . substr($phone, 1);
        }

        // Update data petugas
        $inspector->update([
            'nip'          => $validated['nip'],
            'name'         => $validated['name'],
            'phone_num'    => $phone,
            'portfolio_id' => $validated['portfolio_id'],
        ]);

        // Update email user terkait
        if ($user) {
            $user->email = $validated['email'];
            // Jika user juga menyimpan nip, update juga jika ada kolom di user
            if (isset($user->nip)) {
                $user->nip = $validated['nip'];
            }
            $user->save();
        }

        $message = 'Data petugas berhasil diperbarui.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
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
