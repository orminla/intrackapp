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
use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $query = Schedule::with(['partner', 'product', 'report.documents', 'selectedDetails', 'inspector'])
            ->where('status', 'selesai')
            ->whereHas('report', function ($q) {
                $q->where('status', 'Disetujui');
            });

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('partner', fn($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('inspector', fn($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhere('started_date', 'like', "%{$search}%")
                    ->orWhere('finished_date', 'like', "%{$search}%");
            });
        }

        $schedules = $query->paginate(10);

        $histories = $schedules->through(function ($schedule) {
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
                'inspector_name'  => $schedule->inspector ? implode(' ', array_slice(explode(' ', $schedule->inspector->name), 0, 2)) : '-',
                'bidang'          => $schedule->inspector->portfolio->name ?? '-',
                'petugas'         => $schedule->inspector->name ?? '-',
                'status'          => strtolower($schedule->report->status ?? 'pending'),
                'documents'       => $schedule->report?->documents->map(fn($d) => [
                    'id'   => $d->doc_id,
                    'name' => $d->original_name,
                    'path' => $d->file_path,
                ]) ?? [],
            ];
        });

        return view('admin.inspection_history', [
            'histories' => $histories,
        ]);
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

    public function downloadPdf($schedule_id)
    {
        $report = Report::with([
            'schedule.partner',
            'schedule.inspector.portfolio.department',
            'schedule.product',
            'schedule.selectedDetails',
            'documents'
        ])->where('schedule_id', $schedule_id)->firstOrFail();

        $schedule = $report->schedule;

        $documents = $report->documents->map(function ($doc) {
            return [
                'name' => $doc->original_name,
            ];
        })->toArray();

        $pdf = DomPdfFacade::setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ])->loadView('report_pdf', [
            'report'        => $report,
            'schedule'      => $schedule,
            'partner'       => optional($schedule->partner),
            'inspector'     => optional($schedule->inspector),
            'department'    => optional($schedule->inspector->portfolio->department)->name ?? '-',
            'portofolio'    => optional($schedule->inspector->portfolio)->name ?? '-',
            'product'       => $schedule->product,
            'details'       => $schedule->selectedDetails,
            'documents'     => $documents,
            'started_date'  => $schedule->started_date,
            'finished_date' => $report->finished_date ?? $schedule->finished_date,
        ]);

        $filename = sprintf(
            "Bukti_Inspeksi_%s_%s.pdf",
            str_replace(' ', '_', $schedule->partner->name ?? 'partner'),
            \Carbon\Carbon::parse($schedule->started_date)->format('Ymd')
        );

        return $pdf->download($filename);
    }
}
