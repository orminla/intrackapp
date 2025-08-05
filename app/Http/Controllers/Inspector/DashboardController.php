<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Inspector;
use App\Models\InspectorChangeRequest;
use Carbon\Carbon;

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
            'inspeksi_selesai' => Schedule::where('inspector_id', $inspectorId)->where('status', 'Selesai')->count(),
            'laporan_ditolak' => Schedule::where('inspector_id', $inspectorId)->where('status', 'Ditolak')->count(),
            'belum_lapor' => Schedule::where('inspector_id', $inspectorId)
                ->whereIn('status', ['Disetujui', 'Dalam proses'])->doesntHave('report')->count(),
            'belum_validasi' => Schedule::where('inspector_id', $inspectorId)
                ->where('status', 'Disetujui')->has('report')->count(),
        ];

        $waitingSchedules = Schedule::with(['partner', 'product'])
            ->where('inspector_id', $inspectorId)
            ->where('status', 'Menunggu konfirmasi')
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

        $latest = $formattedSchedules->first();
        $latestScheduleId = $waitingSchedules->first()->schedule_id ?? null;

        if ($latest) {
            $latest['schedule_id'] = $latestScheduleId;

            $existingChangeRequest = InspectorChangeRequest::where('schedule_id', $latest['schedule_id'])
                ->where('status', 'Menunggu konfirmasi')
                ->exists();
        } else {
            $existingChangeRequest = false;
        }

        return view('inspector.dashboard', [
            'summary' => $summary,
            'waiting_confirmation' => $formattedSchedules,
            'latest' => $latest,
            'latestDeadline' => $latestDeadline,
            'hasRequestedChange' => $existingChangeRequest,
        ]);
    }

    public function requestChangeInspector(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,schedule_id',
            'reason' => 'required|string',
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

        $schedule = Schedule::where('schedule_id', $request->schedule_id)
            ->where('inspector_id', $inspector->inspector_id)
            ->where('status', 'Menunggu konfirmasi')
            ->first();

        if (!$schedule) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Data jadwal tidak valid atau sudah dikonfirmasi.',
                ], 404)
                : back()->withErrors('Data jadwal tidak valid atau sudah dikonfirmasi.');
        }

        $existingRequest = InspectorChangeRequest::where('schedule_id', $schedule->schedule_id)
            ->where('status', 'Menunggu')
            ->first();

        if ($existingRequest) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Permintaan ganti petugas untuk jadwal ini sudah dikirim.',
                ], 409)
                : back()->withErrors('Permintaan ganti petugas sudah dikirim sebelumnya.');
        }

        $changeRequest = InspectorChangeRequest::create([
            'schedule_id' => $schedule->schedule_id,
            'old_inspector_id' => $inspector->inspector_id,
            'new_inspector_id' => null,
            'reason' => $request->reason,
            'requested_date' => now(),
            'status' => 'Menunggu konfirmasi',
        ]);

        return $request->wantsJson()
            ? response()->json([
                'success' => true,
                'message' => 'Permintaan ganti petugas berhasil dikirim.',
                'data' => $changeRequest,
            ])
            : redirect()->back()->with('success', 'Permintaan ganti petugas berhasil dikirim.');
    }

    // public function requestChangeInspector(Request $request)
    // {
    //     $request->validate([
    //         'schedule_id' => 'required|exists:schedules,schedule_id',
    //         'reason' => 'required|string|min:10|max:255',
    //     ]);

    //     $user = Auth::user();
    //     $inspector = Inspector::where('users_id', $user->id)->first();

    //     if (!$inspector) {
    //         return $request->wantsJson()
    //             ? response()->json([
    //                 'success' => false,
    //                 'message' => 'Data petugas tidak ditemukan.',
    //             ], 404)
    //             : back()->withErrors('Data petugas tidak ditemukan.');
    //     }

    //     // Ambil jadwal yang sesuai
    //     $schedule = Schedule::where('schedule_id', $request->schedule_id)
    //         ->where('inspector_id', $inspector->inspector_id)
    //         ->where('status', 'Menunggu konfirmasi')
    //         ->first();

    //     if (!$schedule) {
    //         return $request->wantsJson()
    //             ? response()->json([
    //                 'success' => false,
    //                 'message' => 'Data jadwal tidak valid atau sudah dikonfirmasi.',
    //             ], 404)
    //             : back()->withErrors('Data jadwal tidak valid atau sudah dikonfirmasi.');
    //     }

    //     // Cek apakah sudah ada permintaan sebelumnya
    //     $existingRequest = InspectorChangeRequest::where('schedule_id', $schedule->schedule_id)
    //         ->where('status', 'Menunggu')
    //         ->first();

    //     if ($existingRequest) {
    //         return $request->wantsJson()
    //             ? response()->json([
    //                 'success' => false,
    //                 'message' => 'Permintaan ganti petugas untuk jadwal ini sudah dikirim.',
    //             ], 409)
    //             : back()->withErrors('Permintaan ganti petugas sudah dikirim sebelumnya.');
    //     }

    //     $request->merge([
    //         'portfolio_id' => $inspector->portfolio_id,
    //         'started_date' => $schedule->started_date,
    //     ]);

    //     // 🧠 Panggil getAutoInspector dari ScheduleController
    //     $controller = app(\App\Http\Controllers\Admin\ScheduleController::class);
    //     $response = $controller->getAutoInspector($request);

    //     $newInspector = $response instanceof \Illuminate\Http\JsonResponse
    //         ? json_decode($response->getContent())->data ?? null
    //         : null;

    //     if (!$newInspector) {
    //         return $request->wantsJson()
    //             ? response()->json([
    //                 'success' => false,
    //                 'message' => 'Tidak ditemukan petugas pengganti yang cocok.',
    //             ], 404)
    //             : back()->withErrors('Tidak ditemukan petugas pengganti yang cocok.');
    //     }

    //     // Simpan permintaan ganti petugas
    //     $changeRequest = InspectorChangeRequest::create([
    //         'schedule_id' => $schedule->schedule_id,
    //         'old_inspector_id' => $inspector->inspector_id,
    //         'new_inspector_id' => $newInspector->inspector_id,
    //         'reason' => $request->reason,
    //         'requested_date' => now(),
    //         'status' => 'Menunggu',
    //     ]);

    //     return $request->wantsJson()
    //         ? response()->json([
    //             'success' => true,
    //             'message' => 'Permintaan ganti petugas berhasil dikirim.',
    //             'data' => $changeRequest,
    //         ])
    //         : redirect()->back()->with('success', 'Permintaan ganti petugas berhasil dikirim.');
    // }
}
