@extends("inspector.layouts.app")

@section("title", "Riwayat Inspeksi")

@section("content")
    <div class="card rounded-4">
        <div class="card-body">
            <div
                class="d-flex justify-content-between align-items-center flex-wrap mb-4"
            >
                <h4 class="card-title mb-0">Riwayat Inspeksi</h4>
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap align-middle fs-3">
                    <thead>
                        <tr class="text-center text-dark fw-bold">
                            <th>No</th>
                            <th>Mitra</th>
                            <th>Lokasi</th>
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
                                        href="{{ route("inspector.riwayat.download", $item->id) }}"
                                        class="btn btn-sm px-1 border-0 bg-transparent"
                                        title="Unduh Bukti Inspeksi"
                                    >
                                        <i
                                            class="ti ti-download fs-5 text-success"
                                        ></i>
                                    </a>
                                </td>
                            </tr>

                            {{-- Include modal untuk baris ini --}}
                            @include(
                                "inspector.detail_history_modal",
                                [
                                    "index" => $index,
                                    "schedule" => [
                                        "mitra" => $item->partner,
                                        "lokasi" => $item->location ?? "-",
                                        "tanggal" => $item->date,
                                        "tanggal_selesai" => $item->tanggal_selesai ?? "",
                                        "produk" => $item->product,
                                        "detail_produk" => $item->detail_produk ?? [],
                                        "dokumen" => $item->documents ?? [],
                                    ],
                                ]
                            )
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center text-muted py-4"
                                >
                                    Tidak ada riwayat inspeksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
