<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Inspector;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\Document;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    //otomatis setuju jika tidak ada konfirmasi
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'inspector') {
            $message = 'Akses ditolak. Hanya petugas yang dapat mengakses.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : abort(403, $message);
        }

        $inspectorId = Inspector::where('users_id', $user->id)->value('inspector_id');

        $approvedThisMonth = \DB::table('inspector_change_requests')
            ->where('old_inspector_id', $inspectorId) // gunakan old_inspector_id
            ->where('status', 'Disetujui')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Jika sudah >=2, jadwal menunggu konfirmasi langsung menjadi Dalam proses
        if ($approvedThisMonth >= 2) {
            Schedule::where('inspector_id', $inspectorId)
                ->where('status', 'Menunggu konfirmasi')
                ->update(['status' => 'Dalam proses']);
        }

        //auto-approve 1x24 jam
        Schedule::where('status', 'Menunggu konfirmasi')
            ->where('created_at', '<=', now()->subDay())
            ->update(['status' => 'Dalam proses']);

        // Ambil semua jadwal dengan relasi
        $allSchedules = Schedule::with([
            'partner',
            'product',
            'selectedDetails',
            'report.documents',
            'changeRequests'
        ])
            ->where('inspector_id', $inspectorId)
            ->whereDoesntHave('changeRequests', function ($query) {
                $query->where('status', 'Menunggu konfirmasi');
            })
            ->orderBy('started_date')
            ->get();

        // Filter sesuai kebutuhan
        $filteredJadwal = $allSchedules->filter(function ($schedule) {
            return in_array($schedule->status, ['Menunggu konfirmasi', 'Dalam proses']) && $schedule->report === null;
        });

        $filteredLaporan = Schedule::with('report')
            ->where('inspector_id', $inspectorId)
            ->whereHas('report', function ($query) {
                $query->whereIn('status', ['Menunggu konfirmasi', 'Ditolak']);
            })
            ->get();

        // Mapping data
        $mapSchedules = function ($collection, $isReport = false) {
            return $collection->map(function ($schedule) use ($isReport) {
                $dokumen = ($schedule->report && $schedule->report->documents)
                    ? $schedule->report->documents->map(function ($doc) {
                        return [
                            'id'   => $doc->doc_id,
                            'path' => $doc->file_path,
                            'name' => $doc->original_name ?: basename($doc->file_path),
                        ];
                    })->toArray()
                    : [];

                return [
                    'id' => $schedule->schedule_id,
                    'tanggal' => Carbon::parse($schedule->started_date)->format('Y-m-d'),
                    'tanggal_selesai' => $schedule->report && $schedule->report->finished_date
                        ? Carbon::parse($schedule->report->finished_date)->format('Y-m-d')
                        : null,
                    'mitra' => $schedule->partner->name ?? '-',
                    'lokasi' => $schedule->partner->address ?? '-',
                    'produk' => $schedule->product->name ?? '-',
                    'status' => $isReport && $schedule->report ? $schedule->report->status : $schedule->status,
                    'dokumen' => $dokumen,
                    'detail_produk' => $schedule->selectedDetails->pluck('name')->toArray(),
                    'tanggal_tunda' => $schedule->report ? ($schedule->report->postponed_date ? Carbon::parse($schedule->report->postponed_date)->format('Y-m-d') : null) : null,
                    'keterangan_tunda' => $schedule->report ? $schedule->report->postponed_reason : null,
                    'alasan_penolakan' => $schedule->report ? $schedule->report->rejection_reason : null,
                ];
            });
        };

        // Gunakan mapper
        $schedules = $mapSchedules($filteredJadwal);
        $reports = $mapSchedules($filteredLaporan, true);

        $jadwalDalamProses = $schedules->firstWhere('status', 'Dalam proses');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'schedules' => $schedules,
                'reports' => $reports,
                'jadwalDalamProses' => $jadwalDalamProses
            ]);
        }

        return view('inspector.inspection_schedule_and_report', compact(
            'schedules',
            'reports',
            'jadwalDalamProses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tanggal_selesai'  => 'required|date',
            'tanggal_tunda'    => 'nullable|date|after_or_equal:schedules.schedule_date',
            'keterangan_tunda' => 'nullable|string|max:500',
            'dokumentasi'      => 'required|array|min:1|max:3',
            'dokumentasi.*'    => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $schedule = Schedule::where('schedule_id', $request->schedule_id)->firstOrFail();

        // Buat laporan
        $report = Report::create([
            'schedule_id'      => $schedule->schedule_id,
            'finished_date'    => $request->tanggal_selesai,
            'postponed_date'   => $request->tanggal_tunda,
            'postponed_reason' => $request->keterangan_tunda,
            'status'           => 'Menunggu Konfirmasi',
        ]);

        // Simpan dokumentasi
        foreach ($request->file('dokumentasi') as $file) {
            $document = new Document();
            $document->uploadDokumen($file, $report->report_id, $user->id);
        }

        // Ubah status jadwal jadi dalam proses
        $schedule->status = 'Dalam Proses';
        $schedule->save();

        return redirect()->back()->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $schedule = Schedule::findOrFail($id);

        if ($request->status === 'Disetujui') {
            $schedule->status = 'Dalam proses';
            $schedule->save();

            return redirect()->back()->with('success', 'Status jadwal diperbarui menjadi Dalam proses.');
        }

        // Jika Ditolak → modal akan ditampilkan di frontend
        return redirect()->back()->with('tolak_id', $schedule->schedule_id);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'inspector') {
            return response()->json(['success' => false, 'message' => 'Hanya petugas yang dapat mengubah laporan.'], 403);
        }

        $schedule = Schedule::with('report.documents')->findOrFail($id);
        $report = $schedule->report;

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'tanggal_selesai'   => 'nullable|date|after_or_equal:' . $schedule->started_date,
            'tanggal_tunda'     => 'nullable|date|before_or_equal:' . ($request->tanggal_selesai ?? $schedule->finished_date),
            'keterangan_tunda'  => 'nullable|string|max:255',
            'dokumen.*'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5048',
            'hapus_dokumen'     => 'nullable|array',
            'hapus_dokumen.*'   => 'integer|exists:documents,doc_id',
        ]);

        if (!empty($validated['tanggal_selesai'])) {
            $report->finished_date = $validated['tanggal_selesai'];
            $report->status = 'Menunggu konfirmasi';
        }

        if (!empty($validated['tanggal_tunda'])) {
            $report->postponed_date = $validated['tanggal_tunda'];
        }

        if (!empty($validated['keterangan_tunda'])) {
            $report->postponed_reason = $validated['keterangan_tunda'];
        }

        $report->save();

        // Update tanggal tunda & keterangan tunda
        $report->postponed_date = $validated['tanggal_tunda'] ?? null;
        $report->postponed_reason = $validated['keterangan_tunda'] ?? null;

        $report->save();

        // Hapus dokumen lama
        if (!empty($validated['hapus_dokumen'])) {
            $deletedIds = $validated['hapus_dokumen'];
            Document::whereIn('doc_id', $deletedIds)
                ->where('report_id', $report->report_id)
                ->get()
                ->each(function ($doc) {
                    Storage::delete($doc->file_path);
                    $doc->delete();
                });
        }

        // Upload dokumen baru
        if ($request->hasFile('dokumen')) {
            $currentCount = $report->documents()->count();
            $newFiles = $request->file('dokumen', []);

            if (($currentCount + count($newFiles)) > 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 3 dokumen per laporan.'
                ], 422);
            }

            foreach ($newFiles as $file) {
                $document = new Document();
                $document->uploadDokumen($file, $report->report_id, $user->id);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui.'
        ]);
    }
}
