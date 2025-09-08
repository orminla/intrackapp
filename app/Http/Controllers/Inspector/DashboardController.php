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

        // Jadwal menunggu konfirmasi
        $waitingSchedules = Schedule::with(['partner', 'product'])
            ->where('inspector_id', $inspectorId)
            ->where('status', 'Menunggu konfirmasi')
            ->whereDoesntHave('changeRequests', function ($query) use ($inspectorId) {
                $query->whereIn('status', ['Menunggu Konfirmasi', 'Disetujui'])
                    ->where('old_inspector_id', $inspectorId);
            })
            ->orderBy('started_date')
            ->get();

        // Jadwal dalam proses (hanya info)
        $inProgressSchedules = Schedule::with(['partner', 'product'])
            ->where('inspector_id', $inspectorId)
            ->where('status', 'Dalam proses')
            ->orderBy('started_date')
            ->get();

        // Gabungkan semua jadwal
        $allSchedules = $waitingSchedules->concat($inProgressSchedules);

        // Format jadwal untuk frontend
        $formattedSchedules = $allSchedules->map(function ($schedule) {
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
                'is_info_only' => $schedule->status === 'Dalam proses', // tandai sebagai info
            ];
        });

        $latest = $formattedSchedules->first();
        $latestScheduleId = $allSchedules->first()->schedule_id ?? null;

        $existingChangeRequest = $latestScheduleId
            ? InspectorChangeRequest::where('schedule_id', $latestScheduleId)
            ->where('status', 'Menunggu Konfirmasi')
            ->exists()
            : false;

        $latestDeadline = $allSchedules->first()
            ? Carbon::parse($allSchedules->first()->started_date)->subDays(2)->translatedFormat('d F Y')
            : null;

        $changeCount = InspectorChangeRequest::where('old_inspector_id', $inspectorId)
            ->whereMonth('requested_date', Carbon::now()->month)
            ->whereYear('requested_date', Carbon::now()->year)
            ->count();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'summary' => $summary,
                'waiting_confirmation' => $formattedSchedules,
                'latest' => $latest,
                'latestDeadline' => $latestDeadline,
                'changeCount' => $changeCount,
            ]);
        }

        return view('inspector.dashboard', [
            'summary' => $summary,
            'waiting_confirmation' => $formattedSchedules,
            'latest' => $latest,
            'latestDeadline' => $latestDeadline,
            'hasRequestedChange' => $existingChangeRequest,
            'changeCount' => $changeCount,
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

        // Cek batas maksimal 2 kali dalam 1 bulan
        $changeCount = InspectorChangeRequest::where('old_inspector_id', $inspector->inspector_id)
            ->whereMonth('requested_date', Carbon::now()->month)
            ->whereYear('requested_date', Carbon::now()->year)
            ->count();

        if ($changeCount >= 2) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Anda sudah mencapai batas maksimal 2 kali penggantian petugas bulan ini.',
                ], 403)
                : back()->withErrors('Anda sudah mencapai batas maksimal 2 kali penggantian petugas bulan ini.');
        }

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

        // Cek request aktif
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
        $busyInspectorIds = Schedule::whereIn('status', ['Menunggu Konfirmasi', 'Dalam Proses'])
            ->whereBetween('started_date', [now()->subMonth(), now()->addMonth()])
            ->pluck('inspector_id')
            ->toArray();

        $filterAvailable = function ($inspectors) use ($busyInspectorIds, $startedDate, $inspector) {
            $twoWeeksAgo = $startedDate->copy()->subDays(14);
            return $inspectors->filter(function ($insp) use ($busyInspectorIds, $startedDate, $twoWeeksAgo, $inspector) {
                if (in_array($insp->inspector_id, $busyInspectorIds)) return false;
                if ($insp->inspector_id == $inspector->inspector_id) return false;
                if (Schedule::where('inspector_id', $insp->inspector_id)
                    ->whereDate('started_date', $startedDate)
                    ->exists()
                ) return false;

                $recentInspections = Schedule::where('inspector_id', $insp->inspector_id)
                    ->whereDate('started_date', '>=', $twoWeeksAgo)
                    ->whereDate('started_date', '<', $startedDate)
                    ->whereIn('status', ['Selesai', 'Dalam Proses', 'Menunggu Konfirmasi'])
                    ->orderBy('started_date', 'desc')
                    ->get();

                if ($recentInspections->count() >= 4) {
                    $lastInspectionDate = Carbon::parse($recentInspections->first()->started_date);
                    return $startedDate->diffInDays($lastInspectionDate) >= 7;
                }

                return true;
            });
        };

        // 1️⃣ Prioritas: inspector dari portofolio asli
        $originalInspectors = Inspector::where('portfolio_id', $inspector->portfolio_id)
            ->where('inspector_id', '!=', $inspector->inspector_id)
            ->get();
        $availableOriginal = $filterAvailable($originalInspectors);

        if ($availableOriginal->isNotEmpty()) {
            $newInspector = $availableOriginal->random();
            $fromOriginalPortfolio = true;
        } else {
            // 2️⃣ Fallback: inspector dengan sertifikasi relevan
            $certifiedInspectors = Inspector::whereHas('certifications', function ($q) use ($inspector) {
                $q->where('portfolio_id', $inspector->portfolio_id);
            })->where('inspector_id', '!=', $inspector->inspector_id)->get();

            $availableCertified = $filterAvailable($certifiedInspectors);

            if ($availableCertified->isEmpty()) {
                return $request->wantsJson()
                    ? response()->json([
                        'success' => false,
                        'message' => 'Tidak ditemukan petugas pengganti yang cocok.',
                    ], 404)
                    : back()->withErrors('Tidak ditemukan petugas pengganti yang cocok.');
            }

            $newInspector = $availableCertified->random();
            $fromOriginalPortfolio = false;
        }

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
                'inspector_id' => $newInspector->inspector_id,
                'name' => $newInspector->name,
                'fromOriginalPortfolio' => $fromOriginalPortfolio,
            ])
            : redirect()->back()->with('success', 'Permintaan ganti petugas berhasil dikirim.');
    }
}
