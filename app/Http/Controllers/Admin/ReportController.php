<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar laporan inspeksi
     * Hanya bisa diakses oleh admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Hanya admin yang diizinkan
        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya admin yang dapat mengakses data laporan.'
                ], 403);
            }

            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        // Ambil data laporan dengan relasi jadwal, mitra, petugas, dan portofolio
        $reports = Report::whereIn('status', ['Menunggu konfirmasi', 'Ditolak'])
            ->with([
                'schedule.partner:partner_id,name,address',
                'schedule.inspector' => function ($q) {
                    $q->select('inspector_id', 'name', 'portfolio_id')
                        ->with('portfolio:portfolio_id,name');
                }
            ])
            ->get();

        // Format data untuk frontend & API
        $data = $reports->map(function ($report) {
            $alamat = optional($report->schedule->partner)->address ?? '-';

            // Pisah alamat berdasarkan koma
            $parts = explode(',', $alamat);
            $lokasiSingkat = count($parts) >= 2
                ? trim($parts[0]) . ', ' . trim(end($parts))
                : $alamat;

            return [
                'id'            => $report->report_id,
                'tanggal_mulai' => optional($report->schedule->started_date)->format('Y-m-d'),
                'nama_mitra'    => optional($report->schedule->partner)->name ?? '-',
                'lokasi'        => $lokasiSingkat,
                'petugas'       => optional($report->schedule->inspector)->name ?? '-',
                'portofolio'    => optional(optional($report->schedule->inspector)->portfolio)->name ?? '-',
                'status'        => $report->status,
            ];
        });

        // Untuk API (Postman/AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        // Untuk Web
        return view('admin.inspection_reports', ['data' => $data]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan' => $request->status === 'Ditolak' ? 'required|string|max:255' : 'nullable',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;

        // Jika ada kolom keterangan di tabel reports
        if ($request->status === 'Ditolak') {
            $report->rejection_reason = $request->alasan;
        }

        $report->save();

        // Update status jadwal juga
        $report->schedule->update([
            'status' => $request->status === 'Disetujui' ? 'Selesai' : 'Dalam proses',
        ]);

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
    }
}
