<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\Schedule;
use App\Models\Inspector;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Department;
use App\Models\DetailProduct;
use App\Models\InspectorChangeRequest;

class ScheduleController extends Controller
{
    public function getAutoInspector(Request $request)
    {
        $request->validate([
            'portfolio_id' => 'required|exists:portfolios,portfolio_id',
            'started_date' => 'required|date',
            'last_inspector_id' => 'nullable|exists:inspectors,inspector_id',
        ]);

        $portfolioId = $request->input('portfolio_id');
        $startedDate = Carbon::parse($request->input('started_date'));
        $lastInspectorId = $request->input('last_inspector_id');

        // Ambil semua petugas untuk portofolio ini
        $inspectors = Inspector::where('portfolio_id', $portfolioId)->get();

        if ($inspectors->isEmpty()) {
            return response()->json([
                'error' => 'Tidak ada petugas yang cocok dengan portofolio ini.'
            ], 404);
        }

        // Ambil ID petugas yang sedang memiliki jadwal aktif
        $busyInspectorIds = DB::table('schedules')
            ->whereIn(DB::raw('LOWER(status)'), ['menunggu konfirmasi', 'dalam proses'])
            ->pluck('inspector_id')
            ->toArray();

        // Filter petugas yang tersedia
        $available = $inspectors->filter(function ($inspector) use ($busyInspectorIds, $startedDate) {
            if (in_array($inspector->inspector_id, $busyInspectorIds)) {
                return false;
            }

            $twoWeeksAgo = $startedDate->copy()->subDays(14);

            $recentInspections = DB::table('schedules')
                ->where('inspector_id', $inspector->inspector_id)
                ->whereDate('started_date', '>=', $twoWeeksAgo)
                ->whereDate('started_date', '<', $startedDate)
                ->whereIn(DB::raw('LOWER(status)'), ['selesai', 'dalam proses', 'menunggu konfirmasi'])
                ->orderBy('started_date', 'desc')
                ->get();

            if ($recentInspections->count() >= 4) {
                $lastInspectionDate = Carbon::parse($recentInspections->first()->started_date);
                $daysSinceLast = $startedDate->diffInDays($lastInspectionDate);
                return $daysSinceLast >= 7;
            }

            return true;
        })->filter(function ($inspector) use ($lastInspectorId) {
            return $inspector->inspector_id != $lastInspectorId;
        });


        if ($available->isEmpty()) {
            return response()->json([
                'error' => 'Semua petugas sedang sibuk atau dalam masa istirahat.'
            ], 409);
        }

        // Pilih satu petugas secara acak dari yang tersedia
        $selected = $available->random();

        return response()->json([
            'inspector_id'     => $selected->inspector_id,
            'name'             => $selected->name,
            'total_matched'    => $inspectors->count(),
            'total_available'  => $available->count(),
            'note'             => 'Petugas ditemukan dan tidak dalam masa sibuk atau istirahat.',
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Jadwal inspeksi filter, showing, dan search
        $filter = $request->get('filter', 'all');
        $showing = (int) $request->get('showing', 10);
        $search = $request->get('search', null); // 🔹 tambahan

        // Permintaan ganti petugas filter, showing, dan search (parameter baru)
        $filterChange = $request->get('filter_change', 'all');
        $showingChange = (int) $request->get('showing_change', 10);
        $searchChange = $request->get('search_change', null); // 🔹 tambahan

        // Query jadwal inspeksi
        $query = Schedule::with([
            'product:product_id,name',
            'selectedDetails:detail_id,name',
            'partner:partner_id,name,address',
            'inspector' => function ($q) {
                $q->select('inspector_id', 'name', 'portfolio_id')
                    ->with('portfolio:portfolio_id,name');
            }
        ]);

        // Filter status
        if ($filter !== 'all') {
            $query->where('status', $filter);
        } else {
            $query->whereIn('status', ['Dalam proses', 'Menunggu konfirmasi', 'Dijadwalkan ganti']);
        }

        // 🔹 Filter search jadwal inspeksi
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('partner', function ($partnerQ) use ($search) {
                    $partnerQ->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                })
                    ->orWhereHas('inspector', function ($inspectorQ) use ($search) {
                        $inspectorQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($productQ) use ($search) {
                        $productQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Filter role inspector/petugas
        if ($user->role === 'inspector' || $user->role === 'petugas') {
            $inspectorId = optional($user->inspector)->inspector_id;
            if (!$inspectorId) abort(403, 'Akun Anda tidak terkait dengan data petugas.');
            $query->where('inspector_id', $inspectorId);
        }

        $query->orderBy('started_date', 'asc');
        $schedules = $query->paginate($showing)->withQueryString();

        // Transformasi data
        $data = $schedules->getCollection()->transform(function ($schedule) {
            $detailProdukList = $schedule->selectedDetails->pluck('name')->toArray();

            return [
                'id' => $schedule->schedule_id,
                'nomor_surat' => $schedule->letter_number,
                'tanggal_surat' => $schedule->letter_date->format('Y-m-d'),
                'tanggal_inspeksi' => $schedule->started_date->format('Y-m-d'),
                'nama_mitra' => $schedule->partner->name ?? '-',
                'lokasi' => $schedule->partner->address ?? '-',
                'nama_petugas' => $schedule->inspector->name ?? '-',
                'portofolio' => $schedule->inspector->portfolio->name ?? '-',
                'produk' => $schedule->product->name ?? '-',
                'detail_produk' => $detailProdukList,
                'status' => $schedule->status,
            ];
        });
        $schedules->setCollection($data);

        // 🔹 Search juga di permintaan ganti petugas
        $changeRequests = $this->changeReq($request, true, $filterChange, $showingChange, $searchChange);

        // Data pendukung lain
        $partners = Partner::select('partner_id', 'name', 'address')->get();
        $portfolios = Portfolio::select('portfolio_id', 'name', 'department_id')->get();
        $departments = Department::select('department_id', 'name')->get();
        $produkList = Product::select('name')->distinct()->get();

        $allDetailProduk = DetailProduct::select('detail_id', 'name')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $schedules->currentPage(),
                    'last_page' => $schedules->lastPage(),
                    'per_page' => $schedules->perPage(),
                    'total' => $schedules->total(),
                ],
            ]);
        }

        return view('admin.inspection_schedule', [
            'schedules' => $schedules,
            'changeRequests' => $changeRequests,
            'partners' => $partners,
            'portfolios' => $portfolios,
            'departments' => $departments,
            'produkList' => $produkList,
            'allDetailProduk' => $allDetailProduk, // 🔹 tambahkan ini
            'showingSelected' => $showing,
            'filterSelected' => $filter,
            'search' => $search,
            'showingChangeSelected' => $showingChange,
            'filterChangeSelected' => $filterChange,
            'searchChange' => $searchChange,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $message = 'Hanya admin yang dapat menambahkan jadwal.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : abort(403, $message);
        }

        $validated = $request->validate([
            'letter_number'       => 'required|string|max:255|unique:schedules,letter_number',
            'letter_date'         => 'required|date',
            'partner_name'        => 'required|string|max:255',
            'partner_address'     => 'required|string|max:255',
            'started_date'        => 'required|date|after_or_equal:today',
            'portfolio_id'        => 'required|exists:portfolios,portfolio_id',
            'product_name'        => 'required|string|max:255',
            'product_details_raw' => 'required|string',
        ]);

        $detail_produk = array_filter(array_map('trim', explode(',', $validated['product_details_raw'])));

        if (count($detail_produk) < 1) {
            return back()->withErrors(['product_details_raw' => 'Detail produk minimal 1 item.'])->withInput();
        }

        $partner = Partner::firstOrCreate(
            ['name' => $validated['partner_name']],
            ['address' => $validated['partner_address']]
        );

        $product = Product::firstOrCreate(
            ['name' => $validated['product_name']],
            ['created_by' => $user->id]
        );

        // Simpan detail produk jika belum ada
        foreach ($detail_produk as $detailName) {
            DetailProduct::firstOrCreate([
                'product_id' => $product->product_id,
                'name'       => $detailName,
            ]);
        }

        // Cari petugas dengan kuota paling sedikit
        $inspector = Inspector::where('portfolio_id', $validated['portfolio_id'])
            ->withCount(['schedules' => function ($q) {
                $q->whereDate('started_date', '>=', now());
            }])
            ->orderBy('schedules_count', 'asc')
            ->first();

        if (!$inspector) {
            $message = 'Tidak ada petugas yang tersedia untuk portofolio ini.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 400)
                : back()->withErrors([$message])->withInput();
        }

        $schedule = Schedule::create([
            'letter_number' => $validated['letter_number'],
            'letter_date'   => $validated['letter_date'],
            'partner_id'   => $partner->partner_id,
            'inspector_id' => $inspector->inspector_id,
            'started_date' => $validated['started_date'],
            'product_id'   => $product->product_id,
            'status'       => 'Menunggu konfirmasi',
        ]);

        // Ambil detail_id yang sesuai dengan nama detail dan produk
        $detailIds = DetailProduct::where('product_id', $product->product_id)
            ->whereIn('name', $detail_produk)
            ->pluck('detail_id')
            ->toArray();

        // Simpan relasi detail ke tabel pivot
        $schedule->selectedDetails()->sync($detailIds);

        // Kirim WhatsApp
        $this->kirimWhatsappKeInspector($inspector, $partner, $product, $detail_produk, $validated['started_date']);

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    protected function kirimWhatsappKeInspector($inspector, $partner, $product, $detail_produk, $tanggal)
    {
        try {
            $phone = preg_replace('/[^0-9]/', '', $inspector->phone_num);

            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            if (!preg_match('/^62[0-9]{8,13}$/', $phone)) {
                Log::warning('Nomor telepon tidak valid', ['phone' => $phone]);
                return false;
            }

            $message = "📢 Penugasan inspeksi baru\n\n"
                . "Halo *{$inspector->name}*, Anda memiliki inspeksi terbaru yang perlu dilakukan dengan detail sebagai berikut:\n\n"
                . "*Tanggal* : {$tanggal}\n"
                . "*Mitra* : {$partner->name}\n"
                . "*Alamat* : {$partner->address}\n"
                . "*Produk* : {$product->name}\n"
                . "*Detail* : " . implode(', ', $detail_produk) . "\n\n"
                . "Mohon segera konfirmasi melalui sistem dalam waktu maksimal 1x24 jam setelah penugasan.\n"
                . "Jika tidak dikonfirmasi, jadwal akan tetap diproses.\n\n"
                . "Terima kasih,\n"
                . "InTrack App.";

            $response = Http::withHeaders([
                'Authorization' => 'uf1VVEf2S7DGDWMfS5Ry',
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Gagal kirim WhatsApp ke petugas', [
                    'phone'    => $phone,
                    'response' => $response->body(),
                ]);
                return false;
            }

            Log::info('WhatsApp berhasil dikirim ke petugas', [
                'phone'         => $phone,
                'inspector_id'  => $inspector->inspector_id,
                'jadwal'        => $tanggal,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Exception saat kirim WA ke petugas', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthorized'], 403)
                : abort(403, 'Hanya admin yang dapat mengubah jadwal.');
        }

        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_inspeksi' => 'required|date|after_or_equal:today',
            'produk' => 'required|string|max:255',
            'detail_produk' => 'nullable|array',
            'detail_produk.*' => 'required',
        ]);

        $schedule = Schedule::with('inspector')->findOrFail($id);

        // Partner
        $partner = Partner::firstOrCreate(
            ['name' => $validated['nama_mitra']],
            ['address' => $validated['lokasi']]
        );
        $partner->address = $validated['lokasi'];
        $partner->save();

        // Produk
        $currentProduct = Product::find($schedule->product_id);
        if (!$currentProduct || $currentProduct->name !== $validated['produk']) {
            $product = Product::firstOrCreate(['name' => $validated['produk']]);
        } else {
            $product = $currentProduct;
        }

        // Detail produk
        $detailIds = [];
        $detailNames = [];
        foreach ($validated['detail_produk'] ?? [] as $d) {
            if (is_numeric($d)) {
                $detailIds[] = $d;
                $detailNames[] = \App\Models\DetailProduct::find($d)?->name ?? 'Unknown';
            } else {
                $newDetail = \App\Models\DetailProduct::firstOrCreate([
                    'product_id' => $product->product_id,
                    'name' => $d,
                ]);
                $detailIds[] = $newDetail->detail_id;
                $detailNames[] = $newDetail->name;
            }
        }

        // Update jadwal
        $schedule->partner_id = $partner->partner_id;
        $schedule->started_date = $validated['tanggal_inspeksi'];
        $schedule->product_id = $product->product_id;
        $schedule->save();

        // Sync detail produk
        $schedule->selectedDetails()->sync($detailIds);

        // Kirim WhatsApp update jadwal jika ada inspector
        if ($schedule->inspector) {
            $this->sendUpdateJadwal(
                $schedule->inspector,
                $partner,
                $product,
                $detailNames,
                $validated['tanggal_inspeksi']
            );
        }

        // Response untuk AJAX
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Jadwal berhasil diperbarui.']);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    protected function sendUpdateJadwal($inspector, $partner, $product, $detail_produk, $tanggal)
    {
        try {
            // Normalisasi nomor WA
            $phone = preg_replace('/[^0-9]/', '', $inspector->phone_num);

            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            if (!preg_match('/^62[0-9]{8,13}$/', $phone)) {
                Log::warning('Nomor telepon tidak valid', ['phone' => $phone]);
                return false;
            }

            // Pesan WA untuk update jadwal
            $message = "📢 Perubahan Jadwal Inspeksi\n\n"
                . "Halo *{$inspector->name}*, ada perubahan pada jadwal inspeksi Anda dengan detail sebagai berikut:\n\n"
                . "*Tanggal* : {$tanggal}\n"
                . "*Mitra* : {$partner->name}\n"
                . "*Alamat* : {$partner->address}\n"
                . "*Produk* : {$product->name}\n"
                . "*Detail* : " . implode(', ', $detail_produk) . "\n\n"
                . "Mohon segera cek sistem untuk konfirmasi perubahan jadwal.\n\n"
                . "Terima kasih,\n"
                . "InTrack App.";

            // Kirim request ke API Fonnte
            $response = Http::withHeaders([
                'Authorization' => 'uf1VVEf2S7DGDWMfS5Ry',
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Gagal kirim WhatsApp update jadwal ke petugas', [
                    'phone'    => $phone,
                    'response' => $response->body(),
                ]);
                return false;
            }

            Log::info('WhatsApp update jadwal berhasil dikirim ke petugas', [
                'phone'        => $phone,
                'inspector_id' => $inspector->inspector_id,
                'jadwal'       => $tanggal,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Exception saat kirim WA update jadwal', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus jadwal.');
        }

        $schedule = \App\Models\Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function changeReq(Request $request = null, bool $returnOnly = false, $filterChange = 'all', $showingChange = 10)
    {
        $query = InspectorChangeRequest::with([
            'schedule.partner',
            'schedule.inspector',
            'oldInspector',
            'newInspector',
        ])->orderByDesc('requested_date');

        if ($filterChange !== 'all') {
            $query->where('status', $filterChange);
        }

        $changeRequests = $query->paginate($showingChange)->withQueryString();

        $changeRequests->getCollection()->transform(function ($req) {
            $req->tanggal_pengajuan = optional($req->requested_date)?->format('Y-m-d') ?? '-';
            $req->petugas = optional($req->oldInspector)->name ?? '-';
            $req->mitra = optional($req->schedule->partner)->name ?? '-';
            $req->alasan = $req->reason ?? '-';
            $req->petugas_baru = optional($req->newInspector)->name ?? '-';
            $req->status = $req->status ?? 'Menunggu Konfirmasi';

            return $req;
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

        return view('admin.inspection_schedule', [
            'changeRequests' => $changeRequests,
            'showingChangeSelected' => $showingChange,
            'filterChangeSelected' => $filterChange,
        ]);
    }

    public function validasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $reqchange = InspectorChangeRequest::findOrFail($id);

        $finalInspectorId = $request->input('reloaded_inspector_id') ?: $reqchange->new_inspector_id;

        $reqchange->status = $request->status;
        if ($finalInspectorId) {
            $reqchange->new_inspector_id = $finalInspectorId;
        }
        $reqchange->save();

        $schedule = Schedule::find($reqchange->schedule_id);
        $oldInspector = Inspector::find($reqchange->old_inspector_id);

        if ($request->status === 'Disetujui' && $finalInspectorId) {
            if ($schedule) {
                $schedule->inspector_id = $finalInspectorId;
                $schedule->status = 'Menunggu Konfirmasi';
                $schedule->save();

                $newInspector = Inspector::find($finalInspectorId);
                $partner = $schedule->partner;
                $product = $schedule->product;
                $detail_produk = $schedule->selectedDetails->pluck('name')->toArray();
                $tanggal = Carbon::parse($schedule->started_date)->translatedFormat('d F Y');

                $this->kirimWhatsappKeInspector($newInspector, $partner, $product, $detail_produk, $tanggal);

                if ($oldInspector) {
                    $this->sendValidasiJadwal($oldInspector, $schedule, true);
                }
            }
        } elseif ($request->status === 'Ditolak') {
            if ($schedule) {
                $schedule->status = 'Dalam proses';
                $schedule->save();
            }

            $reqchange->new_inspector_id = $reqchange->old_inspector_id;
            $reqchange->save();

            if ($oldInspector) {
                $this->sendValidasiJadwal($oldInspector, $schedule, false);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status permintaan pergantian petugas berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Status permintaan pergantian petugas berhasil diperbarui.');
    }

    protected function sendValidasiJadwal($inspector, $schedule, bool $approved)
    {
        try {
            $phone = preg_replace('/[^0-9]/', '', $inspector->phone_num);

            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            if (!preg_match('/^62[0-9]{8,13}$/', $phone)) {
                Log::warning('Nomor telepon tidak valid', ['phone' => $phone]);
                return false;
            }

            $statusText = $approved ? 'disetujui' : 'ditolak';

            $tanggal = Carbon::parse($schedule->started_date)->translatedFormat('d F Y');

            $message = "📢 *Permintaan Pergantian Petugas*\n\n"
                . "Halo *{$inspector->name}*, permintaan pergantian petugas untuk jadwal inspeksi pada tanggal *{$tanggal}* dengan mitra *{$schedule->partner->name}* telah *{$statusText}*.\n\n"
                . "Terima kasih atas perhatian dan kerjasama Anda.\n"
                . "InTrack App.";

            $response = Http::withHeaders([
                'Authorization' => 'uf1VVEf2S7DGDWMfS5Ry',
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Gagal kirim WhatsApp validasi ke petugas lama', [
                    'phone'    => $phone,
                    'response' => $response->body(),
                ]);
                return false;
            }

            Log::info('WhatsApp validasi berhasil dikirim ke petugas lama', [
                'phone'         => $phone,
                'inspector_id'  => $inspector->inspector_id,
                'jadwal'        => $tanggal,
                'status'        => $statusText,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Exception saat kirim WA validasi ke petugas lama', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
