<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Report;
use Carbon\Carbon;

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
            'inspeksi_hari_ini' => Schedule::whereDate('started_date', $today)->count(),
            'jadwal_perlu_validasi' => Schedule::where('status', 'Menunggu konfirmasi')->count(),
            'laporan_perlu_validasi' => Report::where('status', 'Menunggu validasi')->count(),
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
        // Ambil hari Senin sampai Jumat minggu ini
        $monday = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
        $friday = now()->startOfWeek(\Carbon\Carbon::MONDAY)->addDays(4)->endOfDay();

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
            ->whereBetween('started_date', [$monday, $friday])
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
}
