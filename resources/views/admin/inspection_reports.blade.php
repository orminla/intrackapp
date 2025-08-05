@extends("admin.layouts.app")

@section("title", "Riwayat Inspeksi")

@section("content")
    <div class="row d-flex align-items-stretch">
        <div class="col-12">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Laporan Inspeksi</h4>
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
                                    style="width: auto; min-width: 70px"
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
                                    style="width: auto; min-width: 100px"
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
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Mitra
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Portofolio
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $i => $item)
                                    @php
                                        $warna = match (strtolower($item["status"])) {
                                            "dalam proses" => "info",
                                            "ditolak" => "danger",
                                            "menunggu konfirmasi" => "warning",
                                            "disetujui" => "success",
                                            default => "secondary",
                                        };
                                    @endphp

                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $i + 1 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $item["tanggal_mulai"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $item["nama_mitra"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $item["lokasi"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle fw-bolder"
                                        >
                                            {{ $item["petugas"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ Str::before($item["portofolio"], " -") }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <span
                                                class="badge bg-{{ $warna }}-subtle text-{{ $warna }} py-2 px-3 rounded-2"
                                            >
                                                {{ $item["status"] }}
                                            </span>
                                        </td>

                                        {{-- Tombol Aksi --}}
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{-- Lihat --}}
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Lihat"
                                            >
                                                <i
                                                    class="ti ti-eye fs-5 text-primary"
                                                ></i>
                                            </button>

                                            {{-- Validasi --}}
                                            @if (strtolower($item["status"]) === "menunggu konfirmasi")
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
                                                        {{-- Setujui --}}
                                                        <li>
                                                            <form
                                                                method="POST"
                                                                action="{{ route("admin.laporan.validasi", $item["id"]) }}"
                                                                class="d-inline-block w-100"
                                                            >
                                                                @csrf
                                                                @method("PUT")
                                                                <input
                                                                    type="hidden"
                                                                    name="status"
                                                                    value="Disetujui"
                                                                />
                                                                <button
                                                                    type="submit"
                                                                    class="dropdown-item d-flex align-items-center gap-2 text-success"
                                                                >
                                                                    <i
                                                                        class="ti ti-check"
                                                                    ></i>
                                                                    Setujui
                                                                </button>
                                                            </form>
                                                        </li>

                                                        {{-- Tolak (buka modal) --}}
                                                        <li>
                                                            <button
                                                                class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalTolak-{{ $item["id"] }}"
                                                            >
                                                                <i
                                                                    class="ti ti-x"
                                                                ></i>
                                                                Tolak
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Tidak ada laporan inspeksi.
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

    @include("admin.rejected_modal")
@endsection
