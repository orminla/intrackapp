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

        //auto-approve 1x24 jam
        Schedule::where('status', 'Menunggu konfirmasi')
            ->where('created_at', '<=', now()->subDay())
            ->update(['status' => 'Dalam proses']);

        // Ambil semua jadwal dengan relasi
        $allSchedules = Schedule::with([
            'partner',
            'product',
            'selectedDetails',
            'report.documents'
        ])
            ->where('inspector_id', $inspectorId)
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
            'schedule_id' => 'required|exists:schedules,schedule_id',
            'tanggal_selesai' => 'required|date',
            'dokumentasi' => 'required|array|min:1|max:3',
            'dokumentasi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $schedule = Schedule::where('schedule_id', $request->schedule_id)->firstOrFail();

        // Buat laporan
        $report = Report::create([
            'schedule_id'   => $schedule->schedule_id,
            'finished_date' => $request->tanggal_selesai,
            'status'        => 'Menunggu konfirmasi',
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
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthorized'], 403)
                : abort(403, 'Hanya petugas yang dapat mengubah jadwal.');
        }

        // Validasi input
        $validated = $request->validate([
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_inspeksi',
            'dokumen'           => 'nullable|array',
            'dokumen.*'         => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'hapus_dokumen'     => 'nullable|array',
            'hapus_dokumen.*'   => 'integer|exists:documents,doc_id',
        ]);

        // Ambil jadwal & laporan
        $schedule = Schedule::with('report.documents')->findOrFail($id);
        $report = $schedule->report;

        if (!$report) {
            return redirect()->back()->withErrors('Laporan tidak ditemukan untuk jadwal ini.');
        }

        // Update tanggal selesai & status
        $report->finished_date = $validated['tanggal_selesai'];
        $report->status = 'Menunggu konfirmasi';
        $report->save();

        // Hapus dokumen jika ada yang dipilih
        if (!empty($validated['hapus_dokumen'])) {
            foreach ($validated['hapus_dokumen'] as $docId) {
                $document = Document::find($docId);
                if ($document && $document->report_id === $report->report_id) {
                    if (Storage::exists($document->file_path)) {
                        Storage::delete($document->file_path);
                    }
                    $document->delete();
                }
            }
        }

        // REFRESH relasi dokumen setelah penghapusan
        $report->load('documents');

        // Hitung total dokumen setelah penghapusan
        $currentDocumentCount = $report->documents->count();

        // Upload dokumen baru (maks total 3)
        if ($request->hasFile('dokumen')) {
            $newFiles = $request->file('dokumen');

            if (($currentDocumentCount + count($newFiles)) > 3) {
                return redirect()->back()->withErrors(['dokumen' => 'Maksimal 3 dokumen diperbolehkan per laporan.']);
            }

            foreach ($newFiles as $file) {
                $document = new Document();
                $document->uploadDokumen($file, $report->report_id, $user->id);
            }
        }

        return redirect()->back()->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
