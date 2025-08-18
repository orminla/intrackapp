<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Hanya admin yang boleh mengakses
        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya admin yang dapat mengakses ringkasan dashboard.'
                ], 403);
            }

            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $today = now()->toDateString();

        $summary = [
            'inspeksi_selesai' => Schedule::where('status', 'Selesai')->count(),
            'inspeksi_hari_ini' => Schedule::whereDate('started_date', $today)
                ->where('status', '!=', 'Selesai')
                ->count(),
            'inspeksi_mendatang' => $this->upcomingSchedules(null, true)->count(), // ← Tambahkan ini
            'laporan_perlu_validasi' => Report::where('status', 'Menunggu konfirmasi')->count(),
        ];

        // Ambil 7 hari ke depan
        $upcoming = $this->upcomingSchedules(null, true);

        // Jika request dari API / Postman
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'upcoming' => $upcoming
                ]
            ]);
        }

        // Jika request biasa (Blade view)
        return view('admin.dashboard', [
            'summary' => $summary,
            'upcoming' => $upcoming
        ]);
    }

    public function upcomingSchedules(Request $request = null, $returnOnly = false)
    {
        // Ambil hari Senin sampai Minggu minggu ini
        $monday = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
        $sunday = now()->startOfWeek(\Carbon\Carbon::MONDAY)->addDays(6)->endOfDay(); // Ganti dari 4 ke 6

        $schedules = Schedule::with([
            'product' => function ($q) {
                $q->select('product_id', 'name')->with('details:detail_id,product_id,name');
            },
            'partner:partner_id,name,address',
            'inspector' => function ($q) {
                $q->select('inspector_id', 'name', 'portfolio_id')
                    ->with('portfolio:portfolio_id,name');
            }
        ])
            ->where('status', 'Menunggu konfirmasi')
            ->whereBetween('started_date', [$monday, $sunday]) // Ganti dari $friday ke $sunday
            ->get();

        $formattedSchedules = $schedules->map(function ($schedule) {
            $detailProdukList = $schedule->product->details->pluck('name')->toArray();

            return [
                'tanggal_mulai'     => optional($schedule->started_date)->format('Y-m-d'),
                'nama_mitra'        => $schedule->partner->name ?? '-',
                'lokasi'            => $schedule->partner->address ?? '-',
                'petugas'           => $schedule->inspector->name ?? '-',
                'portofolio'        => $schedule->inspector->portfolio->name ?? '-',
                'produk'            => $schedule->product->name ?? '-',
                'detail_produk'     => $detailProdukList,
                'status'            => $schedule->status,
            ];
        });

        // Return untuk internal pemanggilan
        if ($returnOnly) {
            return $formattedSchedules;
        }

        // Jika request berupa JSON (API/Postman)
        if ($request && $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $formattedSchedules
            ]);
        }

        // Jika untuk view biasa
        return view('admin.dashboard', ['upcoming' => $formattedSchedules]);
    }

    public function inspectionChart()
    {
        $year = now()->year;

        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = \Carbon\Carbon::createFromDate(null, $i, 1)->format('M');
        }

        $bituId = DB::table('departments')->where('name', 'Inspeksi Teknik dan Umum')->value('department_id');
        $bipId  = DB::table('departments')->where('name', 'Inspeksi dan Pengujian')->value('department_id');

        $getMonthlyData = function ($departmentId) use ($year) {
            return DB::table('schedules')
                ->join('inspectors', 'schedules.inspector_id', '=', 'inspectors.inspector_id')
                ->join('portfolios', 'inspectors.portfolio_id', '=', 'portfolios.portfolio_id')
                ->join('departments', 'portfolios.department_id', '=', 'departments.department_id')
                ->select(
                    DB::raw('MONTH(schedules.started_date) as month'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->whereYear('schedules.started_date', $year)
                ->where('departments.department_id', $departmentId)
                ->groupBy('month')
                ->pluck('jumlah', 'month');
        };

        $bituRaw = $getMonthlyData($bituId);
        $bipRaw  = $getMonthlyData($bipId);

        return response()->json([
            'labels' => $labels, // sudah array biasa
            'bitu'   => collect(range(1, 12))->map(fn($m) => $bituRaw[$m] ?? 0)->values(),
            'bip'    => collect(range(1, 12))->map(fn($m) => $bipRaw[$m] ?? 0)->values(),
        ]);
    }

    public function distributionChart()
    {
        $twoWeeksAgo = Carbon::now()->subDays(14);

        // Ambil semua petugas
        $inspectors = DB::table('inspectors')->get();

        // Hitung jumlah jadwal tiap petugas dalam 14 hari terakhir
        $scheduleCounts = DB::table('schedules')
            ->where('started_date', '>=', $twoWeeksAgo)
            ->select('inspector_id', DB::raw('count(*) as jumlah'))
            ->groupBy('inspector_id')
            ->pluck('jumlah', 'inspector_id'); // [inspector_id => jumlah]

        $sesuai = 0;
        $berlebih = 0;
        $belum = 0;

        foreach ($inspectors as $inspector) {
            $count = (int)($scheduleCounts[$inspector->inspector_id] ?? 0);

            if ($count === 0) {
                $belum++;
            } elseif ($count >= 4) {
                $berlebih++;
            } else {
                $sesuai++;
            }
        }

        return response()->json([
            'labels' => ['Sesuai', 'Berlebih', 'Belum Terverifikasi'],
            'series' => [$sesuai, $berlebih, $belum],
        ]);
    }
}
