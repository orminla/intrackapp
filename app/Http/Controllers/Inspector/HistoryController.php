<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Inspector;
use App\Models\Document;
use Carbon\Carbon;
use function Laravel\Prompts\form;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'inspector') {
            abort(403, 'Akses ditolak.');
        }

        $search = $request->input('search');
        $inspectorId = Inspector::where('users_id', $user->id)->value('inspector_id');

        $schedules = Schedule::with(['partner', 'product', 'report.documents', 'selectedDetails'])
            ->where('inspector_id', $inspectorId)
            ->whereHas('report', function ($query) {
                $query->whereIn('status', ['Disetujui', 'Ditolak']);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('partner', function ($partnerQuery) use ($search) {
                        $partnerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    })
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('report', function ($reportQuery) use ($search) {
                            $reportQuery->where('status', 'like', "%{$search}%");
                        })
                        ->orWhereDate('started_date', $search);
                });
            })
            ->orderBy('started_date', 'desc')
            ->get();

        $histories = $schedules->map(function ($schedule) {
            $alamat = $schedule->partner->address ?? '-';
            $parts = array_map('trim', explode(',', $alamat));

            $lokasiSingkat = count($parts) >= 2
                ? $parts[0] . ', ' . end($parts)
                : $alamat;

            return (object)[
                'id'              => $schedule->schedule_id,
                'date'            => optional($schedule->started_date)->format('Y-m-d'),
                'tanggal_selesai' => optional(optional($schedule->report)->finished_date)->format('Y-m-d'),
                'partner'         => $schedule->partner->name ?? '-',
                'location'        => $lokasiSingkat,
                'product'         => $schedule->product->name ?? '-',
                'status'          => strtolower($schedule->report->status ?? 'pending'),
                'documents'       => $schedule->report?->documents->map(fn($d) => [
                    'id'   => $d->doc_id,
                    'name' => $d->original_name,
                    'path' => $d->file_path,
                ]) ?? [],
                'detail_produk'   => $schedule->selectedDetails->pluck('name')->toArray() ?? [],
            ];
        });

        return view('inspector.inspection_history', [
            'histories' => $histories,
            'search' => $search
        ]);
    }

    public function show($id)
    {
        $schedule = Schedule::with([
            'partner',
            'product',
            'selectedDetails',
            'report.documents'
        ])->findOrFail($id);

        $detail = [
            'mitra'           => $schedule->partner->name ?? '-',
            'lokasi'          => $schedule->partner->address ?? '-',
            'tanggal'         => optional($schedule->started_date)->format('Y-m-d'),
            'tanggal_selesai' => optional(optional($schedule->report)->finished_date)->format('Y-m-d'),
            'produk'          => $schedule->product->name ?? '-',
            'detail_produk'   => $schedule->selectedDetails->pluck('name')->toArray() ?? [],
            'dokumen'         => optional($schedule->report)->documents->map(function ($doc) {
                return [
                    'name' => $doc->original_name,
                    'path' => asset('storage/' . $doc->file_path),
                ];
            }) ?? [],
        ];

        return view('inspector.detail_history.index', compact('detail'));
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
