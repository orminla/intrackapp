@extends("admin.layouts.app")

@section("title", "Jadwal Inspeksi")

@section("content")
    <div class="row d-flex align-items-stretch">
        <!-- Jadwal Inspeksi -->
        <div class="col-12 mb-2">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Jadwal Inspeksi</h4>
                        </div>

                        <!-- Showing, filter, tambah -->
                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <form
                                id="filterForm"
                                method="GET"
                                class="d-flex align-items-center gap-3 mb-0"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="showing"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Showing
                                    </label>
                                    <select
                                        name="showing"
                                        id="showing"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            min-width: 70px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterForm').submit()"
                                    >
                                        <option
                                            value="10"
                                            {{ $showingSelected == 10 ? "selected" : "" }}
                                        >
                                            10
                                        </option>
                                        <option
                                            value="25"
                                            {{ $showingSelected == 25 ? "selected" : "" }}
                                        >
                                            25
                                        </option>
                                        <option
                                            value="50"
                                            {{ $showingSelected == 50 ? "selected" : "" }}
                                        >
                                            50
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="filter"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Filter
                                    </label>
                                    <select
                                        name="filter"
                                        id="filter"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            max-width: 120px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterForm').submit()"
                                    >
                                        <option
                                            value="all"
                                            {{ $filterSelected == "all" ? "selected" : "" }}
                                        >
                                            Semua
                                        </option>
                                        <option
                                            value="Menunggu konfirmasi"
                                            {{ $filterSelected == "Menunggu konfirmasi" ? "selected" : "" }}
                                        >
                                            Menunggu
                                        </option>
                                        <option
                                            value="Dijadwalkan ganti"
                                            {{ $filterSelected == "Dijadwalkan ganti" ? "selected" : "" }}
                                        >
                                            Dijadwalkan ganti
                                        </option>
                                        <option
                                            value="Dalam proses"
                                            {{ $filterSelected == "Dalam proses" ? "selected" : "" }}
                                        >
                                            Diproses
                                        </option>
                                    </select>
                                </div>
                            </form>

                            <button
                                class="btn btn-primary d-flex align-items-center gap-2"
                                data-bs-toggle="modal"
                                data-bs-target="#tambahJadwalModal"
                                style="height: 36px; font-size: 0.875rem"
                            >
                                <i class="ti ti-plus"></i>
                                Tambah Jadwal
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <!-- Tabel Jadwal -->
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th class="text-center fw-bold">No</th>
                                    <th class="text-center fw-bold">Tanggal</th>
                                    <th class="text-center fw-bold">Mitra</th>
                                    <th class="text-center fw-bold">Petugas</th>
                                    <th class="text-center fw-bold">
                                        Portofolio
                                    </th>
                                    <th class="text-center fw-bold">Lokasi</th>
                                    <th class="text-center fw-bold">Status</th>
                                    <th class="text-center fw-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $index => $schedule)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($schedules->currentPage() - 1) * $schedules->perPage() + $index + 1 }}
                                        </td>
                                        <td class="text-center">
                                            {{ $schedule["tanggal_inspeksi"] }}
                                        </td>
                                        <td class="text-center">
                                            {{ $schedule["nama_mitra"] }}
                                        </td>
                                        <td class="text-center fw-bolder">
                                            {{ $schedule["nama_petugas"] }}
                                        </td>
                                        <td class="text-center">
                                            {{ explode("-", $schedule["portofolio"])[0] ?? $schedule["portofolio"] }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $parts = explode(",", $schedule["lokasi"] ?? "");
                                                $last = trim(end($parts));
                                            @endphp

                                            {{ $last ?: "-" }}
                                        </td>

                                        <td class="text-center">
                                            <span
                                                class="badge @switch($schedule['status'])
                            @case('Menunggu konfirmasi') bg-secondary-subtle text-secondary @break
                            @case('Dijadwalkan ganti') bg-orange-subtle text-orange @break
                            @case('Dalam proses') bg-warning-subtle text-warning @break
                            @default bg-light text-dark
                        @endswitch py-2 px-3 rounded-2"
                                            >
                                                {{ $schedule["status"] }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            @if ($schedule["status"] === "Menunggu konfirmasi")
                                                <button
                                                    class="btn btn-sm px-1 border-0 bg-transparent"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailScheduleModal-{{ $index }}"
                                                >
                                                    <i
                                                        class="ti ti-edit fs-5 text-warning"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-title="Lihat & Ubah"
                                                    ></i>
                                                </button>
                                            @endif

                                            <form
                                                method="POST"
                                                action="{{ route("admin.jadwal.destroy", $schedule["id"]) }}"
                                                class="d-inline delete-form"
                                            >
                                                @csrf
                                                @method("DELETE")
                                                <button
                                                    type="button"
                                                    class="btn btn-sm px-1 border-0 bg-transparent delete-button"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Hapus"
                                                >
                                                    <i
                                                        class="ti ti-trash fs-5 text-danger"
                                                    ></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Tidak ada jadwal inspeksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <x-table-pagination :data="$schedules" />
                </div>
            </div>
        </div>

        <!-- Permintaan Ganti Petugas -->
        <div class="col-12 mt-4">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Permintaan Ganti Petugas</h4>
                        </div>

                        <!-- Showing & Filter -->
                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <form
                                id="filterChangeForm"
                                method="GET"
                                class="d-flex align-items-center gap-3 mb-0"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="showing_change"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Showing
                                    </label>
                                    <select
                                        name="showing_change"
                                        id="showing_change"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            min-width: 70px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterChangeForm').submit()"
                                    >
                                        <option
                                            value="10"
                                            {{ ($showingChangeSelected ?? 10) == 10 ? "selected" : "" }}
                                        >
                                            10
                                        </option>
                                        <option
                                            value="25"
                                            {{ ($showingChangeSelected ?? 10) == 25 ? "selected" : "" }}
                                        >
                                            25
                                        </option>
                                        <option
                                            value="50"
                                            {{ ($showingChangeSelected ?? 10) == 50 ? "selected" : "" }}
                                        >
                                            50
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="filter_change"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Filter
                                    </label>
                                    <select
                                        name="filter_change"
                                        id="filter_change"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            max-width: 160px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterChangeForm').submit()"
                                    >
                                        <option
                                            value="all"
                                            {{ ($filterChangeSelected ?? "all") == "all" ? "selected" : "" }}
                                        >
                                            Semua
                                        </option>
                                        <option
                                            value="Menunggu Konfirmasi"
                                            {{ ($filterChangeSelected ?? "") == "Menunggu Konfirmasi" ? "selected" : "" }}
                                        >
                                            Menunggu Konfirmasi
                                        </option>
                                        <option
                                            value="Disetujui"
                                            {{ ($filterChangeSelected ?? "") == "Disetujui" ? "selected" : "" }}
                                        >
                                            Disetujui
                                        </option>
                                        <option
                                            value="Ditolak"
                                            {{ ($filterChangeSelected ?? "") == "Ditolak" ? "selected" : "" }}
                                        >
                                            Ditolak
                                        </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Permintaan -->
                    <div class="table-responsive mt-4">
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Mitra</th>
                                    <th class="text-center">Petugas Lama</th>
                                    <th class="text-center">Petugas Baru</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($changeRequests as $i => $req)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($changeRequests->currentPage() - 1) * $changeRequests->perPage() + $i + 1 }}
                                        </td>
                                        <td class="text-center">
                                            {{ $req->tanggal_pengajuan ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $req->mitra ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $req->petugas ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            {{ $req->petugas_baru ?? "-" }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $badgeColor = match (strtolower($req->status ?? "")) {
                                                    "disetujui" => "bg-success-subtle text-success",
                                                    "ditolak" => "bg-danger-subtle text-danger",
                                                    default => "bg-secondary-subtle text-secondary",
                                                };
                                            @endphp

                                            <span
                                                class="badge {{ $badgeColor }} py-2 px-3 rounded-2"
                                            >
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Lihat -->
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewChangeInspectorModal-{{ $i }}"
                                                title="Lihat"
                                            >
                                                <i
                                                    class="ti ti-eye fs-5 text-primary"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Lihat"
                                                ></i>
                                            </button>

                                            @if ($req->status === "Menunggu Konfirmasi")
                                                <!-- Reload -->
                                                <button
                                                    class="btn btn-sm px-1 border-0 bg-transparent btn-reload-inspector"
                                                    type="button"
                                                    title="Reload Petugas Baru"
                                                    data-id="{{ $req["id"] }}"
                                                    data-portfolio-id="{{ $req->schedule->inspector->portfolio_id ?? "" }}"
                                                    data-started-date="{{ $req["tanggal_pengajuan"] ?? ($req["requested_date"] ?? "") }}"
                                                    data-reload-count="0"
                                                >
                                                    <i
                                                        class="ti ti-refresh fs-5 text-warning"
                                                    ></i>
                                                </button>
                                            @endif

                                            <!-- Validasi opsi (disetujui/ditolak) untuk status 'menunggu konfirmasi' -->
                                            @php
                                                $status = strtolower($req["status"]);
                                                $validasiOpsi = [];

                                                if ($status === "menunggu konfirmasi") {
                                                    $validasiOpsi = [
                                                        ["label" => "Disetujui", "icon" => "ti ti-check", "color" => "text-success"],
                                                        ["label" => "Ditolak", "icon" => "ti ti-x", "color" => "text-danger"],
                                                    ];
                                                }
                                            @endphp

                                            @if (count($validasiOpsi))
                                                <div class="btn-group">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm px-1 border-0 bg-transparent dropdown-toggle"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        title="Validasi"
                                                    >
                                                        <i
                                                            class="ti ti-circle-check fs-5 text-success"
                                                        ></i>
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end shadow rounded-3"
                                                    >
                                                        @foreach ($validasiOpsi as $opt)
                                                            <li>
                                                                <form
                                                                    method="POST"
                                                                    action="{{ route("admin.jadwal.validasi", $req["id"]) }}"
                                                                    class="d-inline-block w-100 validation-form"
                                                                >
                                                                    @csrf
                                                                    @method("PUT")
                                                                    <input
                                                                        type="hidden"
                                                                        name="status"
                                                                        value="{{ $opt["label"] }}"
                                                                    />
                                                                    <input
                                                                        type="hidden"
                                                                        name="reloaded_inspector_id"
                                                                        value=""
                                                                        class="reload-id-input"
                                                                    />
                                                                    <button
                                                                        type="submit"
                                                                        class="dropdown-item d-flex align-items-center gap-2 {{ $opt["color"] }}"
                                                                    >
                                                                        <i
                                                                            class="{{ $opt["icon"] }}"
                                                                        ></i>
                                                                        {{ $opt["label"] }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Tidak ada permintaan ganti petugas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <x-table-pagination :data="$changeRequests" />
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include("admin.add_schedule_modal")
    @include("admin.detail_schedule_modal")
    @include("admin.detail_changeinsp_modal")
@endsection

@push("scripts")
    <script>
        // SweetAlert flash message langsung jalan tanpa tunggu DOMContentLoaded
        @if(session('success') || session('error') || session('warning'))
            Swal.fire({
                icon: '{{ session("success") ? "success" : (session("error") ? "error" : "warning") }}',
                title: '{{ session("success") ? "Berhasil!" : (session("error") ? "Gagal!" : "Perhatian!") }}',
                text: "{{ session('success') ?? session('error') ?? session('warning') }}",
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-primary rounded-2 px-4',
                },
                buttonsStyling: false,
            });
        @endif

        @if(session('inspector_changed'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('inspector_changed') }}",
                timer: 1800,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-primary rounded-2 px-4',
                },
                buttonsStyling: false,
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: '{!! implode("<br>", $errors->all()) !!}',
                timer: 1500,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-primary rounded-2 px-4',
                },
                buttonsStyling: false,
            });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tombol reload petugas di tabel
            document
                .querySelectorAll('.btn-reload-inspector')
                .forEach((btn) => {
                    btn.addEventListener('click', function () {
                        let reloadCount = parseInt(
                            this.dataset.reloadCount || '0',
                        );

                        if (reloadCount >= 2) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Batas reload tercapai',
                                text: 'Maksimal reload petugas 2 kali sebelum menyimpan.',
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-warning rounded-2 px-4',
                                },
                                buttonsStyling: false,
                            });
                            return;
                        }

                        const id = this.dataset.id;
                        const portfolioId = this.dataset.portfolioId;
                        const startedDate = this.dataset.startedDate;

                        this.disabled = true;
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.classList.add(
                                'spinner-border',
                                'spinner-border-sm',
                            );
                            icon.classList.remove('ti-refresh');
                        }

                        fetch(
                            `/admin/get-inspector?portfolio_id=${portfolioId}&started_date=${startedDate}`,
                        )
                            .then((res) => res.json())
                            .then((data) => {
                                if (data.inspector_id && data.name) {
                                    const row = btn.closest('tr');
                                    const petugasBaruCell =
                                        row.querySelectorAll('td')[4];
                                    const currentName =
                                        petugasBaruCell.textContent.trim();

                                    if (currentName === data.name) {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Petugas sama',
                                            html: `Petugas pengganti adalah <strong>${data.name}</strong> yang sudah ada di tabel.`,
                                            customClass: {
                                                popup: 'rounded-4',
                                                confirmButton:
                                                    'btn btn-info rounded-2 px-4',
                                            },
                                            buttonsStyling: false,
                                        });
                                    } else {
                                        const availabilityNote =
                                            data.note ||
                                            'Info ketersediaan tidak tersedia.';

                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Petugas pengganti tersedia',
                                            html: `<p>Ganti petugas dengan <strong>${data.name}</strong>?</p>`,
                                            showCancelButton: true,
                                            confirmButtonText: 'Ya, ganti',
                                            cancelButtonText: 'Batal',
                                            customClass: {
                                                popup: 'rounded-4',
                                                confirmButton:
                                                    'btn btn-success rounded-2 px-4 me-2',
                                                cancelButton:
                                                    'btn btn-outline-muted rounded-2 px-4',
                                            },
                                            buttonsStyling: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                petugasBaruCell.textContent =
                                                    data.name;
                                                petugasBaruCell.title =
                                                    availabilityNote;

                                                btn.dataset.newInspectorId =
                                                    data.inspector_id;

                                                reloadCount++;
                                                btn.dataset.reloadCount =
                                                    reloadCount;

                                                setTimeout(() => {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil',
                                                        text: `Petugas berhasil diganti menjadi ${data.name}`,
                                                        timer: 1500,
                                                        showConfirmButton: false,
                                                        customClass: {
                                                            popup: 'rounded-4',
                                                            confirmButton:
                                                                'btn btn-primary rounded-2 px-4',
                                                        },
                                                        buttonsStyling: false,
                                                    });
                                                }, 500);
                                            }
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Petugas tidak ditemukan',
                                        text: 'Petugas yang tersedia tidak ditemukan.',
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton:
                                                'btn btn-warning rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat mengambil data petugas.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-danger rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                });
                            })
                            .finally(() => {
                                this.disabled = false;
                                if (icon) {
                                    icon.classList.remove(
                                        'spinner-border',
                                        'spinner-border-sm',
                                    );
                                    icon.classList.add('ti-refresh');
                                }
                            });
                    });
                });

            // Saat submit validasi, sertakan ID petugas hasil reload
            document
                .querySelectorAll('form[action*="validasi"]')
                .forEach((form) => {
                    form.addEventListener('submit', function () {
                        const row = this.closest('tr');
                        const reloadBtn = row.querySelector(
                            '.btn-reload-inspector',
                        );
                        const reloadIdInput =
                            this.querySelector('.reload-id-input');

                        if (reloadBtn) {
                            if (
                                reloadIdInput &&
                                reloadBtn.dataset.newInspectorId
                            ) {
                                reloadIdInput.value =
                                    reloadBtn.dataset.newInspectorId;
                            }
                            reloadBtn.dataset.reloadCount = 0;
                            reloadBtn.dataset.newInspectorId = '';
                        }
                    });
                });

            // Konfirmasi hapus dengan SweetAlert2
            document.querySelectorAll('.delete-button').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const form = btn.closest('form');
                    Swal.fire({
                        title: 'Yakin ingin menghapus data petugas?',
                        text: 'Tindakan ini tidak dapat dibatalkan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-danger rounded-2 px-4 me-2',
                            cancelButton:
                                'btn btn-outline-muted rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]',
                                    ).content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    Accept: 'application/json',
                                },
                                body: new URLSearchParams(new FormData(form)),
                            })
                                .then(async (response) => {
                                    const data = await response.json();
                                    if (!response.ok)
                                        throw new Error(
                                            data.message ||
                                                'Gagal menghapus data',
                                        );
                                    return data;
                                })
                                .then((data) => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text:
                                            data.message ||
                                            'Data petugas berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton:
                                                'btn btn-primary rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    }).then(() => {
                                        location.reload();
                                    });
                                })
                                .catch((error) => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text:
                                            error.message ||
                                            'Terjadi kesalahan saat menghapus.',
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton:
                                                'btn btn-primary rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    });
                                });
                        }
                    });
                });
            });

            // Auto submit filter
            document
                .getElementById('showing')
                ?.addEventListener('change', function () {
                    document.getElementById('filterForm').submit();
                });
            document
                .getElementById('filter')
                ?.addEventListener('change', function () {
                    document.getElementById('filterForm').submit();
                });

            // ======= AJAX SUBMIT VALIDASI FORM =======
            document
                .querySelectorAll('form.validation-form')
                .forEach((form) => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const url = this.action;
                        const formData = new FormData(this);
                        const submitBtn = this.querySelector(
                            'button[type="submit"]',
                        );
                        if (submitBtn) submitBtn.disabled = true;

                        fetch(url, {
                            method: 'POST', // ganti dari PUT jadi POST karena pakai spoof method di form
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                            },
                            body: formData,
                        })
                            .then(async (res) => {
                                const data = await res.json();
                                if (!res.ok)
                                    throw new Error(
                                        data.message || 'Terjadi kesalahan',
                                    );
                                return data;
                            })
                            .then((data) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text:
                                        data.message ||
                                        'Status permintaan berhasil diperbarui.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch((err) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text:
                                        err.message ||
                                        'Terjadi kesalahan saat memproses permintaan.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                });
                            })
                            .finally(() => {
                                if (submitBtn) submitBtn.disabled = false;
                            });
                    });
                });
        });
    </script>
@endpush
