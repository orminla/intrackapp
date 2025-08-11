@php
    $showingSelected = request()->get("showing", "10");
@endphp

@extends("admin.layouts.app")

@section("title", "Riwayat Inspeksi")

@section("content")
    <div class="card rounded-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Riwayat Inspeksi</h4>

                <form
                    method="GET"
                    id="showingForm"
                    class="d-flex align-items-center gap-2"
                >
                    <label for="showing" class="fw-normal text-muted mb-0">
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
                        onchange="document.getElementById('showingForm').submit()"
                    >
                        <option
                            value="10"
                            {{ $showingSelected == "10" ? "selected" : "" }}
                        >
                            10
                        </option>
                        <option
                            value="25"
                            {{ $showingSelected == "25" ? "selected" : "" }}
                        >
                            25
                        </option>
                        <option
                            value="50"
                            {{ $showingSelected == "50" ? "selected" : "" }}
                        >
                            50
                        </option>
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap align-middle fs-3">
                    <thead>
                        <tr class="text-center text-dark fw-bold">
                            <th>No</th>
                            <th>Mitra</th>
                            <th>Lokasi</th>
                            <th>Petugas</th>
                            <th>Produk</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histories as $index => $item)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->partner }}</td>
                                <td>{{ $item->location ?? "-" }}</td>
                                <td>{{ $item->inspector_name ?? "-" }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ $item->date ?? "-" }}</td>
                                <td>{{ $item->tanggal_selesai ?? "-" }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm px-1 border-0 bg-transparent"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailHistoryModal-{{ $index }}"
                                        title="Lihat Detail"
                                    >
                                        <i
                                            class="ti ti-eye fs-5 text-primary"
                                        ></i>
                                    </button>

                                    <a
                                        href="{{ route("admin.riwayat.download", $item->id) }}"
                                        class="btn btn-sm px-1 border-0 bg-transparent"
                                        title="Unduh Bukti Inspeksi"
                                    >
                                        <i
                                            class="ti ti-download fs-5 text-success"
                                        ></i>
                                    </a>
                                </td>
                            </tr>

                            @include(
                                "admin.detail_history_modal",
                                [
                                    "index" => $index,
                                    "schedule" => [
                                        "mitra" => $item->partner,
                                        "lokasi" => $item->location ?? "-",
                                        "tanggal" => $item->date,
                                        "tanggal_selesai" => $item->tanggal_selesai ?? "",
                                        "produk" => $item->product,
                                        "bidang" => $item->bidang ?? "-",
                                        "petugas" => $item->petugas ?? "-",
                                        "detail_produk" => $item->detail_produk ?? [],
                                        "dokumen" => $item->documents ?? [],
                                    ],
                                ]
                            )
                        @empty
                            <tr>
                                <td
                                    colspan="8"
                                    class="text-center text-muted py-4"
                                >
                                    Tidak ada riwayat inspeksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <x-table-pagination :data="$histories" />
            </div>
        </div>
    </div>
@endsection
