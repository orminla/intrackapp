<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya admin yang dapat mengakses data laporan.'
                ], 403);
            }
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $search = $request->input('search'); // 🔹 ambil keyword pencarian

        $reportsQuery = Report::whereIn('status', ['Menunggu konfirmasi', 'Ditolak'])
            ->whereHas('schedule', function ($query) {
                $query->where('status', 'Dalam proses');
            })
            ->with([
                'schedule.partner:partner_id,name,address',
                'schedule.product:product_id,name',
                'schedule.selectedDetails:detail_id,name',
                'schedule.inspector' => function ($q) {
                    $q->select('inspector_id', 'name', 'portfolio_id')
                        ->with('portfolio:portfolio_id,name');
                },
                'documents:doc_id,report_id,original_name,file_path'
            ]);

        // 🔹 Tambahkan filter search
        if (!empty($search)) {
            $reportsQuery->where(function ($q) use ($search) {
                $q->whereHas('schedule.partner', function ($partnerQ) use ($search) {
                    $partnerQ->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                })
                    ->orWhereHas('schedule.inspector', function ($inspectorQ) use ($search) {
                        $inspectorQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('schedule.product', function ($productQ) use ($search) {
                        $productQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $reports = $reportsQuery->get();

        // 🔹 Format data
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
                'detail' => [
                    'mitra'           => optional($report->schedule->partner)->name ?? '-',
                    'lokasi'          => $alamat,
                    'tanggal'         => optional($report->schedule->started_date)->format('Y-m-d'),
                    'tanggal_selesai' => optional($report->finished_date)->format('Y-m-d'),
                    'tanggal_tunda'   => optional($report->postponed_date)->format('Y-m-d'),
                    'keterangan_tunda' => $report->postponed_reason ?? null,
                    'produk'          => optional($report->schedule->product)->name ?? '-',
                    'detail_produk'   => $report->schedule->selectedDetails->pluck('name')->toArray(),
                    'petugas'         => optional($report->schedule->inspector)->name ?? '-',
                    'bidang'          => optional(optional($report->schedule->inspector)->portfolio)->name ?? '-',
                    'dokumen'         => $report->documents->map(function ($doc) {
                        return [
                            'id'   => $doc->doc_id,
                            'name' => $doc->original_name,
                            'path' => $doc->file_path,
                        ];
                    }) ?? [],
                    'alasan_penolakan' => $report->rejection_reason ?? null,
                ],
            ];
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return view('admin.inspection_reports', [
            'data' => $data,
            'search' => $search // 🔹 biar form search bisa tetap ada nilai
        ]);
    }

    protected function sendNotifLaporan($inspector, $schedule, bool $approved)
    {
        try {
            $phone = preg_replace('/[^0-9]/', '', $inspector->phone_num);

            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            if (!preg_match('/^62[0-9]{8,13}$/', $phone)) {
                Log::warning('Nomor telepon tidak valid untuk WA notifikasi laporan', ['phone' => $phone]);
                return false;
            }

            $statusText = $approved ? 'disetujui' : 'ditolak';
            $tanggal = Carbon::parse($schedule->started_date)->translatedFormat('d F Y');

            $message = "📢 *Status Laporan Inspeksi*\n\n"
                . "Halo *{$inspector->name}*, laporan inspeksi pada tanggal *{$tanggal}* dengan mitra *{$schedule->partner->name}* telah *{$statusText}*.\n\n";

            if (!$approved) {
                // Alasan penolakan diambil dari report
                $message .= "Alasan penolakan: " . ($schedule->report->rejection_reason ?? '-') . "\n\n";
            } else {
                $message .= "Untuk mengunduh bukti, kunjungi halaman Riwayat Inspeksi.\n\n";
            }

            $message .= "Terima kasih atas perhatian dan kerjasama Anda.\nInTrack App.";

            $response = Http::withHeaders([
                'Authorization' => 'uf1VVEf2S7DGDWMfS5Ry',
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Gagal kirim WhatsApp notifikasi status laporan', [
                    'phone'    => $phone,
                    'response' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Exception saat kirim WA notifikasi status laporan', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            // Jika status Ditolak, alasan wajib diisi
            'alasan' => $request->status === 'Ditolak' ? 'required|string|max:255' : 'nullable',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;

        if ($request->status === 'Ditolak') {
            $report->rejection_reason = $request->alasan;
        } else {
            // Kosongkan alasan jika disetujui
            $report->rejection_reason = null;
        }

        $report->save();

        // Update status schedule terkait
        $report->schedule->update([
            'status' => $request->status === 'Disetujui' ? 'Selesai' : 'Dalam proses',
        ]);

        $inspector = $report->schedule->inspector;

        if ($inspector) {
            $this->sendNotifLaporan($inspector, $report->schedule, $request->status === 'Disetujui');
        }

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function show($id)
    {
        $schedule = \App\Models\Schedule::with([
            'partner',
            'product',
            'selectedDetails',
            'report.documents',
            'inspector.portfolios'
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
