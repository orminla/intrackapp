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

        // Ambil data laporan dengan relasi lengkap
        $reports = Report::whereIn('status', ['Menunggu konfirmasi', 'Ditolak'])
            ->with([
                'schedule.partner:partner_id,name,address',
                'schedule.product:product_id,name',
                'schedule.selectedDetails:detail_id,name', // ← perbaikan di sini
                'schedule.inspector' => function ($q) {
                    $q->select('inspector_id', 'name', 'portfolio_id')
                        ->with('portfolio:portfolio_id,name');
                },
                'documents:report_id,doc_id,original_name,file_path'
            ])
            ->get();


        // Format data
        $data = $reports->map(function ($report) {
            $alamat = optional($report->schedule->partner)->address ?? '-';
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

                // Data tambahan untuk modal detail
                'detail' => [
                    'mitra'           => optional($report->schedule->partner)->name ?? '-',
                    'lokasi'          => $alamat,
                    'tanggal'         => optional($report->schedule->started_date)->format('Y-m-d'),
                    'tanggal_selesai' => optional($report->finished_date)->format('Y-m-d'),
                    'produk'          => optional($report->schedule->product)->name ?? '-',
                    'detail_produk'   => $report->schedule->selectedDetails->pluck('name')->toArray(),
                    'petugas'         => optional($report->schedule->inspector)->name ?? '-',
                    'bidang'          => optional(optional($report->schedule->inspector)->portfolio)->name ?? '-',
                    'dokumen' => $report->documents->map(function ($doc) {
                        return [
                            'id' => $doc->doc_id,
                            'name' => $doc->original_name,
                            'path' => $doc->file_path,
                        ];
                    }) ?? [],

                ],
            ];
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

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

    public function show($id)
    {
        $schedule = Schedule::with([
            'partner',
            'product',
            'selectedDetails',
            'report.documents',
            'inspector.portfolios' // tambahkan relasi portofolio dari inspector
        ])->findOrFail($id);

        $detail = [
            'mitra'           => $schedule->partner->name ?? '-',
            'lokasi'          => $schedule->partner->address ?? '-',
            'tanggal'         => optional($schedule->started_date)->format('Y-m-d'),
            'tanggal_selesai' => optional(optional($schedule->report)->finished_date)->format('Y-m-d'),
            'produk'          => $schedule->product->name ?? '-',
            'detail_produk'   => $schedule->selectedDetails->pluck('name')->toArray() ?? [],
            'petugas'         => $schedule->inspector->name ?? '-',
            'bidang'          => $schedule->inspector->portfolio->name ?? '-',
            'dokumen'         => optional($schedule->report)->documents->map(function ($doc) {
                return [
                    'name' => $doc->original_name,
                    'path' => asset('storage/' . $doc->file_path),
                ];
            }) ?? [],
        ];

        return view('admin.detail_report.show', compact('detail'));
    }
}
