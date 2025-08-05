@push("styles")
    <style>
        tr[onclick]:hover {
            background-color: #f5f5f5;
        }
    </style>
@endpush

@extends("admin.layouts.app")

@section("title", "Dashboard")

@section("content")
    {{-- Summary Cards --}}
    <div class="row">
        @foreach ([
                [
                    "key" => "inspeksi_selesai",
                    "title" => "Inspeksi Selesai",
                    "icon" => "ti-circle-check",
                    "color" => "primary",
                    "url" => route("admin.riwayat")
                ],
                [
                    "key" => "inspeksi_hari_ini",
                    "title" => "Inspeksi Hari Ini",
                    "icon" => "ti-calendar-event",
                    "color" => "success",
                    "url" => route("admin.jadwal.index")
                ],
                [
                    "key" => "inspeksi_mendatang",
                    "title" => "Akan Datang",
                    "icon" => "ti-calendar-stats",
                    "color" => "warning",
                    "url" => route("admin.dashboard") . "#upcoming"
                ],
                [
                    "key" => "laporan_perlu_validasi",
                    "title" => "Validasi Laporan",
                    "icon" => "ti-file-check",
                    "color" => "danger",
                    "url" => route("admin.laporan")
                ]
            ]
            as $card)
            <div class="col-md-3 col-sm-6 mb-1">
                <a href="{{ $card["url"] }}" class="text-decoration-none">
                    <div class="card h-100 rounded-4">
                        <div
                            class="card-body d-flex align-items-center gap-3 p-3"
                        >
                            <span
                                class="d-inline-flex align-items-center justify-content-center rounded-circle bg-{{ $card["color"] }}-subtle text-{{ $card["color"] }} ms-4"
                                style="width: 40px; height: 40px"
                            >
                                <i
                                    class="ti {{ $card["icon"] }}"
                                    style="font-size: 18px"
                                ></i>
                            </span>
                            <div style="max-width: 100px">
                                <h5 class="mb-0 fw-semibold">
                                    {{ $summary[$card["key"]] ?? 0 }}
                                </h5>
                                <small class="text-muted">
                                    {{ $card["title"] }}
                                </small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Chart Section --}}
    <div class="row d-flex align-items-stretch mt-4">
        {{-- Line Chart --}}
        <div class="col-lg-8 d-flex">
            <div class="card w-100 rounded-4">
                <div class="card-body">
                    <div
                        class="d-flex justify-content-between align-items-start gap-2 mb-2"
                    >
                        <div>
                            <h4 class="card-title">Statistik Inspeksi</h4>
                            <p class="card-subtitle text-muted">
                                Perbandingan jumlah inspeksi BITU & BIP
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a
                                href="javascript:void(0)"
                                id="download-png"
                                class="text-muted"
                                title="Download PNG"
                            >
                                <i class="ti ti-download fs-6"></i>
                            </a>
                        </div>
                    </div>

                    <div
                        id="inspectionChart"
                        class="mt-1 mx-n6"
                        style="height: 300px; max-height: 300px"
                    ></div>

                    <ul class="list-unstyled mb-0 mt-3">
                        <li class="list-inline-item text-primary">
                            <span
                                class="round-8 text-bg-primary rounded-circle me-1 d-inline-block"
                            ></span>
                            Bidang Inspeksi Teknik & Umum (BITU)
                        </li>
                        <li class="list-inline-item text-info">
                            <span
                                class="round-8 text-bg-info rounded-circle me-1 d-inline-block"
                            ></span>
                            Bidang Inspeksi & Pengujian (BIP)
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Pie Chart --}}
        <div class="col-lg-4 d-flex">
            <div class="card w-100 rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div>
                            <h4 class="card-title">Status Distribusi Tugas</h4>
                            <p class="card-subtitle text-muted">
                                Distribusi berdasarkan kategori
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="ti ti-dots fs-7"></i>
                            </a>
                        </div>
                    </div>
                    <div id="distributionPieChart" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Jadwal --}}
    <div class="row d-flex align-items-stretch" id="upcoming">
        <div class="col-12 d-flex">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center">
                        <div>
                            <h4 class="card-title">
                                Jadwal Inspeksi Mendatang
                            </h4>
                            <p class="card-subtitle">
                                Inspeksi 7 hari ke depan
                            </p>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcoming as $i => $item)
                                    <tr
                                        style="cursor: pointer"
                                        onclick="window.location='{{ route("admin.jadwal.index") }}'"
                                    >
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
                                            {{ $item["portofolio"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            @php
                                                $status = $item["status"];
                                                $badgeMap = [
                                                    "Selesai" => ["label" => "Selesai", "warna" => "success"],
                                                    "Menunggu konfirmasi" => ["label" => "Menunggu", "warna" => "info"],
                                                    "Dalam proses" => ["label" => "Diproses", "warna" => "warning"],
                                                    "Dijadwalkan ganti" => ["label" => "Ganti", "warna" => "secondary"],
                                                ];
                                                $statusBadge = $badgeMap[$status] ?? ["label" => $status, "warna" => "dark"];
                                            @endphp

                                            <span
                                                class="badge bg-{{ $statusBadge["warna"] }}-subtle text-{{ $statusBadge["warna"] }} py-2 px-3 rounded-2"
                                            >
                                                {{ $statusBadge["label"] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script src="{{ asset("admin_assets/libs/apexcharts/dist/apexcharts.min.js") }}"></script>
    <script src="{{ asset("admin_assets/js/line_chart.js") }}"></script>
    <script src="{{ asset("admin_assets/js/doughnut_chart.js") }}"></script>
    <script src="{{ asset("admin_assets/js/dashboard.js") }}"></script>
@endpush
