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
                            <!-- Showing -->
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">
                                    Showing
                                </span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="min-width: 70px; height: 36px"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <!-- Filter Status -->
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">Filter</span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="min-width: 100px; height: 36px"
                                >
                                    <option value="all">Semua</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="menunggu">Menunggu</option>
                                </select>
                            </div>
                            <!-- Tambah -->
                            <div>
                                <button
                                    class="btn btn-primary d-flex align-items-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#tambahJadwalModal"
                                >
                                    <i class="ti ti-plus"></i>
                                    Tambah Jadwal
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th class="text-center fw-bold">No</th>
                                    <th class="text-center fw-bold">Tanggal</th>
                                    <th class="text-center fw-bold">Mitra</th>
                                    <th class="text-center fw-bold">Lokasi</th>
                                    <th class="text-center fw-bold">Petugas</th>
                                    <th class="text-center fw-bold">Produk</th>
                                    <th class="text-center fw-bold">Status</th>
                                    <th class="text-center fw-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $index => $schedule)
                                    <tr>
                                        <td class="text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="text-center">
                                            {{ $schedule["tanggal_inspeksi"] }}
                                        </td>
                                        <td class="text-center">
                                            {{ $schedule["nama_mitra"] }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $parts = explode(",", $schedule["lokasi"]);
                                                $first = trim($parts[0] ?? "-");
                                                $last = trim(end($parts) ?? "-");
                                            @endphp

                                            {{ $first }}, {{ $last }}
                                        </td>
                                        <td class="text-center fw-bolder">
                                            {{ $schedule["nama_petugas"] }}
                                        </td>
                                        <td class="text-center">
                                            {{ $schedule["produk"] }}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge @switch($schedule['status'])
                                            @case('Menunggu konfirmasi') bg-secondary-subtle text-secondary @break
                                            @case('Dijadwalkan ganti') bg-orange-subtle text-orange @break
                                            @case('Dalam proses') bg-warning-subtle text-warning @break
                                            @case('Selesai') bg-success-subtle text-success @break
                                            @default bg-light text-dark
                                        @endswitch py-2 px-3 rounded-2"
                                            >
                                                {{ $schedule["status"] }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Lihat -->
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

                                            <!-- Hapus -->
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
                                        <td colspan="10" class="text-center">
                                            Tidak ada jadwal inspeksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">
                                    Showing
                                </span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="min-width: 70px; height: 36px"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">Filter</span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="min-width: 100px; height: 36px"
                                >
                                    <option value="all">Semua</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="menunggu">Menunggu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Mitra</th>
                                    <th class="text-center">Petugas</th>
                                    <th class="text-center">Petugas Baru</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($changeRequests as $i => $req)
                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $i + 1 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $req["tanggal_pengajuan"] ?? "-" }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $req["mitra"] ?? "-" }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $req["petugas"] ?? "-" }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $req["petugas_baru"] ?? "-" }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            @php
                                                $badgeColor = match (strtolower($req["status"] ?? "")) {
                                                    "disetujui" => "bg-success-subtle text-success",
                                                    "ditolak" => "bg-danger-subtle text-danger",
                                                    default => "bg-secondary-subtle text-secondary",
                                                };
                                            @endphp

                                            <span
                                                class="badge {{ $badgeColor }} py-2 px-3 rounded-2"
                                            >
                                                {{ ucfirst($req["status"]) }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Lihat -->
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeInspectorModal-{{ $i }}"
                                                title="Lihat"
                                            >
                                                <i
                                                    class="ti ti-edit fs-5 text-warning"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Lihat & Ubah"
                                                ></i>
                                            </button>

                                            <!-- Validasi -->
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
                                                                    class="d-inline-block w-100"
                                                                >
                                                                    @csrf
                                                                    @method("PUT")
                                                                    <input
                                                                        type="hidden"
                                                                        name="status"
                                                                        value="{{ $opt["label"] }}"
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
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include("admin.add_schedule_modal")
    @include("admin.detail_schedule_modal")
    {{-- @include("admin.edit_changeinsp_modal", ["requestChange" => $req, "i" => $i]) --}}
@endsection

@push("scripts")
    @if (session("success"))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session("success") }}',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        </script>
    @endif
@endpush
