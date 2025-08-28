<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Imports\InspectorImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\AccountCreated;

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
        $search  = $request->input('search'); // 🔹 ambil keyword pencarian
        $currentPage = (int) $request->query('page', 1);

        $query = Inspector::with(['portfolio.department', 'schedules.report'])
            ->orderBy('name');

        // 🔹 filter status jika ada
        if ($filter !== 'all') {
            $query->whereHas('schedules', function ($q) use ($filter) {
                $q->where('status', $filter);
            });
        }

        // 🔹 filter pencarian jika ada
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('phone_num', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('portfolio', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('portfolio.department', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Hitung total data
        $totalData = $query->count();

        if ($totalData < $showing) {
            $showing = $totalData > 0 ? $totalData : 1;
        }

        if (($currentPage - 1) * $showing >= $totalData) {
            $currentPage = 1;
        }

        // Pagination
        $inspectors = $query->paginate($showing, ['*'], 'page', $currentPage);

        // Map data
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
                'gender'            => $inspector->gender,
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
            'search'      => $search, // 🔹 biar bisa ditampilkan di input search di view
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $message = 'Hanya admin yang dapat menambahkan petugas.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : abort(403, $message);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'size:18',
                'regex:/^\d{18}$/',
                'unique:inspectors,nip',
                'unique:pending_users,nip',
            ],
            'phone_num' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $formatted = strpos($value, '08') === 0 ? '62' . substr($value, 1) : $value;
                    if (
                        DB::table('inspectors')->where('phone_num', $formatted)->exists() ||
                        DB::table('admins')->where('phone_num', $formatted)->exists()
                    ) {
                        $fail('Nomor HP sudah digunakan di sistem.');
                    }
                }
            ],
            'portfolio_id'  => 'required|exists:portfolios,portfolio_id',
            'department_id' => 'required|exists:departments,department_id',
            'email' => 'required|email|unique:users,email|unique:pending_users,email',
        ]);

        $phone = strpos($validated['phone_num'], '08') === 0 ? '62' . substr($validated['phone_num'], 1) : $validated['phone_num'];
        $firstName = strtolower(strtok($validated['name'], ' '));
        $defaultPassword = $firstName . '123';
        $token = Str::random(40);

        $pending = PendingUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_num' => $phone,
            'role' => 'inspector',
            'nip' => $validated['nip'],
            'portfolio_id' => $validated['portfolio_id'],
            'password_plain' => $defaultPassword,
            'verif_token' => $token,
            'expired_at' => now()->addDays(2),
        ]);

        $verifLink = url('/verify-email/' . $token);

        $verifController = new EmailVerificationController();
        $verifController->sendVerificationLink($pending);

        Mail::to($pending->email)->send(new AccountCreated($pending, $verifLink));

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Petugas berhasil ditambahkan. Menunggu verifikasi akun.', 'verifikasi_link' => $verifLink], 201)
            : redirect()->back()->with('success', 'Petugas berhasil ditambahkan. Tunggu verifikasi email.');
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
            'name' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'size:18',
                'regex:/^\d{18}$/',
                'unique:inspectors,nip,' . $inspector->inspector_id . ',inspector_id'
            ],
            'email' => 'required|email|unique:users,email,' . ($user ? $user->id : 'NULL'),
            'phone_num' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($inspector) {
                    $formatted = strpos($value, '08') === 0 ? '62' . substr($value, 1) : $value;
                    if (
                        DB::table('inspectors')->where('phone_num', $formatted)->where('inspector_id', '!=', $inspector->inspector_id)->exists() ||
                        DB::table('admins')->where('phone_num', $formatted)->exists()
                    ) {
                        $fail('Nomor HP sudah digunakan di sistem.');
                    }
                }
            ],
            'portfolio_id' => 'required|exists:portfolios,portfolio_id',
        ]);

        $phone = strpos($validated['phone_num'], '08') === 0 ? '62' . substr($validated['phone_num'], 1) : $validated['phone_num'];

        $inspector->update([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'phone_num' => $phone,
            'portfolio_id' => $validated['portfolio_id'],
        ]);

        if ($user) {
            $user->email = $validated['email'];
            if (isset($user->nip)) {
                $user->nip = $validated['nip'];
            }
            $user->save();
        }

        $message = 'Data petugas berhasil diperbarui.';
        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect()->back()->with('success', $message);
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
