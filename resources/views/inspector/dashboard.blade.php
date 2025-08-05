@extends("inspector.layouts.app")

@section("title", "Dashboard")

@section("content")
    {{-- Summary Cards --}}
    <div class="row mb-4">
        @php
            $cards = [
                ["key" => "inspeksi_selesai", "label" => "Inspeksi Selesai", "icon" => "ti-circle-check", "color" => "primary"],
                ["key" => "laporan_ditolak", "label" => "Laporan Ditolak", "icon" => "ti-circle-x", "color" => "danger"],
                ["key" => "belum_lapor", "label" => "Belum Lapor", "icon" => "ti-clock-hour-4", "color" => "warning"],
                ["key" => "belum_validasi", "label" => "Belum Divalidasi", "icon" => "ti-alert-circle", "color" => "secondary"],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="card h-100 rounded-4">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
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
                                {{ $card["label"] }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Inspeksi & Aktivitas --}}
    <div class="row align-items-stretch">
        {{-- Inspeksi Terbaru --}}
        <div class="col-lg-5 d-flex">
            <div class="card rounded-4 h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-semibold mb-4">Inspeksi Terbaru</h5>

                    @if ($latest)
                        @foreach (["Mitra", "Tanggal Inspeksi", "Produk", "Lokasi"] as $label)
                            <div class="mb-2 d-flex">
                                <div class="text-muted" style="width: 130px">
                                    {{ $label }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="bg-light p-2 rounded">
                                        {{ $latest[$label] ?? "-" }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button
                            class="btn w-100 mt-auto mb-2 {{ $hasRequestedChange ? "btn-muted" : "btn-primary" }}"
                            data-bs-toggle="modal"
                            data-bs-target="#changeInspectorModal"
                            @if ($hasRequestedChange) disabled @endif
                        >
                            @if ($hasRequestedChange)
                                Menunggu Konfirmasi
                            @else
                                    Ajukan Ganti Petugas
                            @endif
                        </button>

                        {{-- Keterangan di bawah tombol --}}
                        @if ($hasRequestedChange)
                            <small class="text-danger d-block mb-2">
                                Anda telah mengajukan pergantian petugas dan
                                sedang menunggu konfirmasi dari admin.
                            </small>
                        @else
                            <small
                                class="d-block text-muted"
                                style="font-size: 0.75rem"
                            >
                                Maksimal 2 kali pergantian petugas setiap bulan.
                                <br />
                                Konfirmasi pergantian terakhir sebelum
                                <strong>{{ $latestDeadline ?? "-" }}</strong>
                                .
                            </small>
                        @endif
                    @else
                        <p class="text-muted">Belum ada jadwal inspeksi.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Aktivitas & Pemberitahuan --}}
        <div class="col-lg-7 d-flex">
            <div class="card rounded-4 h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-semibold">Aktivitas & Pemberitahuan</h5>
                    <div class="mt-4">
                        <small class="text-muted">Hari Ini</small>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i class="ti ti-upload text-primary me-2"></i>
                                Anda mengunggah laporan inspeksi untuk PT Wilfar
                            </div>
                            <small class="text-muted">10 Menit lalu</small>
                        </div>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i
                                    class="ti ti-calendar-event text-danger me-2"
                                ></i>
                                Jadwal inspeksi baru ditugaskan: UD Sentosa
                            </div>
                            <small class="text-muted">Hari ini, 09.10</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">Minggu Ini</small>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i
                                    class="ti ti-calendar-event text-danger me-2"
                                ></i>
                                Jadwal inspeksi baru ditugaskan: UD Sentosa
                            </div>
                            <small class="text-muted">Kemarin, 16.45</small>
                        </div>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i class="ti ti-upload text-primary me-2"></i>
                                Anda mengunggah laporan inspeksi untuk PT Wilfar
                            </div>
                            <small class="text-muted">2 Hari lalu</small>
                        </div>
                    </div>
                    <div class="flex-grow-1"></div>
                </div>
            </div>
        </div>
    </div>

    @include("inspector.change_officer_modal", ["latest" => $latest])
@endsection
