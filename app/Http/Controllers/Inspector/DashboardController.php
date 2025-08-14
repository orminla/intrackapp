<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Inspector;
use App\Models\InspectorChangeRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'inspector') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya petugas yang dapat mengakses ringkasan dashboard.'
                ], 403);
            }

            abort(403, 'Hanya petugas yang dapat mengakses halaman ini.');
        }

        $inspectorId = Inspector::where('users_id', $user->id)->value('inspector_id');

        $summary = [
            'inspeksi_selesai' => Schedule::where('inspector_id', $inspectorId)
                ->where('status', 'Selesai')
                ->count(),

            'laporan_ditolak' => Schedule::where('inspector_id', $inspectorId)
                ->where('status', 'Ditolak')
                ->count(),

            'belum_lapor' => Schedule::where('inspector_id', $inspectorId)
                ->whereIn('status', ['Disetujui', 'Dalam proses'])
                ->doesntHave('report')
                ->count(),

            'menunggu_validasi' => Schedule::where('inspector_id', $inspectorId)
                ->where('status', 'Menunggu konfirmasi')
                ->count(),
        ];

        $waitingSchedules = Schedule::with(['partner', 'product'])
            ->where('inspector_id', $inspectorId)
            ->where('status', 'Menunggu konfirmasi')
            ->whereDoesntHave('changeRequests', function ($query) use ($inspectorId) {
                $query->whereIn('status', ['Menunggu Konfirmasi', 'Disetujui'])
                    ->where('old_inspector_id', $inspectorId);  // <-- tambah kondisi ini
            })
            ->orderBy('started_date')
            ->get();

        $formattedSchedules = $waitingSchedules->map(function ($schedule) {
            $alamat = $schedule->partner->address ?? '-';
            $parts = explode(',', $alamat);
            $lokasiSingkat = count($parts) >= 2
                ? trim($parts[0]) . ', ' . trim(end($parts))
                : $alamat;

            return [
                'schedule_id' => $schedule->schedule_id,
                'Mitra' => $schedule->partner->name ?? '-',
                'Tanggal Inspeksi' => Carbon::parse($schedule->started_date)->translatedFormat('d F Y'),
                'Produk' => optional($schedule->product)->name ?? '-',
                'Lokasi' => $lokasiSingkat,
                'Status' => $schedule->status,
            ];
        });

        $latest = $formattedSchedules->first();
        $latestScheduleId = $waitingSchedules->first()->schedule_id ?? null;

        if ($latest) {
            $latest['schedule_id'] = $latestScheduleId;

            $existingChangeRequest = InspectorChangeRequest::where('schedule_id', $latest['schedule_id'])
                ->where('status', 'Menunggu Konfirmasi') // pastikan konsisten statusnya
                ->exists();
        } else {
            $existingChangeRequest = false;
        }

        $latestDeadline = $waitingSchedules->first()
            ? Carbon::parse($waitingSchedules->first()->started_date)->subDays(2)->translatedFormat('d F Y')
            : null;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'summary' => $summary,
                'waiting_confirmation' => $formattedSchedules,
                'latest' => $latest,
                'latestDeadline' => $latestDeadline,
            ]);
        }

        return view('inspector.dashboard', [
            'summary' => $summary,
            'waiting_confirmation' => $formattedSchedules,
            'latest' => $latest,
            'latestDeadline' => $latestDeadline,
            'hasRequestedChange' => $existingChangeRequest,
        ]);
    }

    public function changeReq(Request $request = null, bool $returnOnly = false)
    {
        $changeRequests = InspectorChangeRequest::with([
            'schedule.partner',
            'schedule.inspector',
            'oldInspector',
            'newInspector',
        ])->orderByDesc('requested_date')->get();

        // Tambahkan properti tambahan (virtual attributes)
        $changeRequests->each(function ($req) {
            $req->tanggal_pengajuan = optional($req->requested_date)?->format('Y-m-d') ?? '-';
            $req->petugas = optional($req->oldInspector)->name ?? '-';
            $req->mitra = optional($req->schedule->partner)->name ?? '-';
            $req->alasan = $req->reason ?? '-';
            $req->petugas_baru = optional($req->newInspector)->name ?? '-';
            $req->status = $req->status ?? 'Menunggu Konfirmasi';
        });

        if ($returnOnly) {
            return $changeRequests;
        }

        if ($request && $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'changeRequests' => $changeRequests,
            ]);
        }

        return view('inspector.dashboard', [
            'changeRequests' => $changeRequests,
        ]);
    }

    public function requestChangeInspector(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,schedule_id',
            'reason' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $inspector = Inspector::where('users_id', $user->id)->first();

        if (!$inspector) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Data petugas tidak ditemukan.',
                ], 404)
                : back()->withErrors('Data petugas tidak ditemukan.');
        }

        // Ambil jadwal inspeksi milik petugas yang statusnya Menunggu Konfirmasi (case sensitive)
        $schedule = Schedule::where('schedule_id', $request->schedule_id)
            ->where('inspector_id', $inspector->inspector_id)
            ->where('status', 'Menunggu Konfirmasi')
            ->first();

        if (!$schedule) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Data jadwal tidak valid atau sudah dikonfirmasi.',
                ], 404)
                : back()->withErrors('Data jadwal tidak valid atau sudah dikonfirmasi.');
        }

        // Cek apakah sudah ada permintaan ganti petugas yang belum diproses (status Menunggu Konfirmasi)
        $existingRequest = InspectorChangeRequest::where('schedule_id', $schedule->schedule_id)
            ->where('status', 'Menunggu Konfirmasi')
            ->first();

        if ($existingRequest) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Permintaan ganti petugas untuk jadwal ini sudah dikirim.',
                ], 409)
                : back()->withErrors('Permintaan ganti petugas sudah dikirim sebelumnya.');
        }

        $startedDate = Carbon::parse($schedule->started_date);
        $twoWeeksAgo = $startedDate->copy()->subDays(14);

        // Cari inspector lain dengan portfolio yang sama, kecuali petugas saat ini
        $inspectors = Inspector::where('portfolio_id', $inspector->portfolio_id)
            ->where('inspector_id', '!=', $inspector->inspector_id)
            ->get();

        // Ambil inspector yang sibuk (dengan status menunggu konfirmasi atau dalam proses) dan jadwal masih dekat (misal dari hari ini - 1 bulan ke depan)
        $busyInspectorIds = Schedule::whereIn('status', ['Menunggu Konfirmasi', 'Dalam Proses'])
            ->whereBetween('started_date', [now()->subMonth(), now()->addMonth()])
            ->pluck('inspector_id')
            ->toArray();

        // Filter inspector yang tersedia
        $available = $inspectors->filter(function ($insp) use ($busyInspectorIds, $startedDate, $twoWeeksAgo, $schedule) {
            // Jika inspector sibuk, tolak
            if (in_array($insp->inspector_id, $busyInspectorIds)) {
                return false;
            }

            // Cek apakah inspector sudah punya jadwal inspeksi pada tanggal yang sama
            $hasSameDate = Schedule::where('inspector_id', $insp->inspector_id)
                ->whereDate('started_date', $schedule->started_date)
                ->exists();

            if ($hasSameDate) {
                return false;
            }

            // Cek inspeksi terakhir dalam 14 hari sebelum jadwal ini
            $recentInspections = Schedule::where('inspector_id', $insp->inspector_id)
                ->whereDate('started_date', '>=', $twoWeeksAgo)
                ->whereDate('started_date', '<', $startedDate)
                ->whereIn('status', ['Selesai', 'Dalam Proses', 'Menunggu Konfirmasi'])
                ->orderBy('started_date', 'desc')
                ->get();

            // Jika inspector sudah 4 inspeksi dalam periode itu, cek apakah sudah lebih dari 7 hari dari inspeksi terakhir
            if ($recentInspections->count() >= 4) {
                $lastInspectionDate = Carbon::parse($recentInspections->first()->started_date);
                $daysSinceLast = $startedDate->diffInDays($lastInspectionDate);
                return $daysSinceLast >= 7;
            }

            return true;
        });

        if ($available->isEmpty()) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Tidak ditemukan petugas pengganti yang cocok.',
                ], 404)
                : back()->withErrors('Tidak ditemukan petugas pengganti yang cocok.');
        }

        $newInspector = $available->random();

        // Simpan dalam transaksi supaya aman
        DB::beginTransaction();
        try {
            $changeRequest = InspectorChangeRequest::create([
                'schedule_id' => $schedule->schedule_id,
                'old_inspector_id' => $inspector->inspector_id,
                'new_inspector_id' => $newInspector->inspector_id,
                'reason' => $request->reason,
                'requested_date' => now(),
                'status' => 'Menunggu Konfirmasi',
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan permintaan ganti petugas. Silakan coba lagi.',
                ], 500)
                : back()->withErrors('Gagal menyimpan permintaan ganti petugas. Silakan coba lagi.');
        }

        return $request->wantsJson()
            ? response()->json([
                'success' => true,
                'message' => 'Permintaan ganti petugas berhasil dikirim.',
                'data' => $changeRequest,
            ])
            : redirect()->back()->with('success', 'Permintaan ganti petugas berhasil dikirim.');
    }
}
