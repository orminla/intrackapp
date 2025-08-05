<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Inspector;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $schedules = Schedule::with(['partner', 'product', 'report.documents', 'selectedDetails', 'inspector'])
            ->where('status', 'selesai')
            ->whereHas('report', function ($query) {
                $query->where('status', 'Disetujui');
            })
            ->get();

        $histories = $schedules->map(function ($schedule) {
            $alamat = $schedule->partner->address ?? '-';
            $parts = array_map('trim', explode(',', $alamat));
            $lokasiSingkat = count($parts) >= 2 ? $parts[0] . ', ' . end($parts) : $alamat;

            return (object)[
                'id'              => $schedule->schedule_id,
                'date'            => optional($schedule->started_date)->format('Y-m-d'),
                'tanggal_selesai' => optional(optional($schedule->report)->finished_date)->format('Y-m-d'),
                'partner'         => $schedule->partner->name ?? '-',
                'location'        => $lokasiSingkat,
                'product'         => $schedule->product->name ?? '-',
                'detail_produk'   => $schedule->selectedDetails->pluck('name')->toArray() ?? [],
                'inspector_name' => $schedule->inspector ? implode(' ', array_slice(explode(' ', $schedule->inspector->name), 0, 2)) : '-',
                'bidang'          => $schedule->inspector->portfolio->name ?? '-', // ✅ tambahkan ini
                'petugas'         => $schedule->inspector->name ?? '-',             // ✅ dan ini juga (biar konsisten)
                'status'          => strtolower($schedule->report->status ?? 'pending'),
                'documents'       => $schedule->report?->documents->map(fn($d) => [
                    'id'   => $d->doc_id,
                    'name' => $d->original_name,
                    'path' => $d->file_path,
                ]) ?? [],
            ];
        });

        return view('admin.inspection_history', compact('histories'));
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

        return view('admin.detail_history.show', compact('detail'));
    }
}
