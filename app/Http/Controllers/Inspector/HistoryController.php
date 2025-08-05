<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Inspector;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use function Laravel\Prompts\form;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'inspector') {
            abort(403, 'Akses ditolak.');
        }

        $inspectorId = Inspector::where('users_id', $user->id)->value('inspector_id');

        $schedules = Schedule::with(['partner', 'product', 'report.documents', 'selectedDetails'])
            ->where('inspector_id', $inspectorId)
            ->whereHas('report', function ($query) {
                $query->whereIn('status', ['Disetujui', 'Ditolak']);
            })
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
                'location'        => $lokasiSingkat, // <--- Sudah diproses singkat
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

        return view('inspector.inspection_history', compact('histories'));
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
}
