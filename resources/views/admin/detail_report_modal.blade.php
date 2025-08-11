@push("styles")
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
        /* Hanya beri margin bawah biar ada jarak ke data berikutnya */
        .rejection-reason {
            padding-top: 15px;
            margin-top: 0; /* tanpa margin atas */
            margin-bottom: 15px;
        }
    </style>
@endpush

<div
    class="modal fade"
    id="detailReportModal-{{ $index }}"
    tabindex="-1"
    aria-labelledby="detailReportModalLabel-{{ $index }}"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
            <div
                class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
            >
                <h4
                    class="modal-title fw-bold mb-2"
                    id="detailReportModalLabel-{{ $index }}"
                >
                    Detail Riwayat Inspeksi
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>

            <div class="modal-body pt-2 text-start">
                <div class="row">
                    {{-- Alasan Penolakan di paling atas --}}
                    @if (! empty($schedule["alasan_penolakan"]))
                        <div class="col-12 mb-3 rejection-reason">
                            <label class="form-label fw-semibold">
                                Alasan Penolakan
                            </label>
                            <textarea
                                class="form-control"
                                rows="3"
                                readonly
                                style="resize: none"
                            >
{{ $schedule["alasan_penolakan"] }}</textarea
                            >
                        </div>
                    @endif

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mitra</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $schedule["mitra"] }}"
                            readonly
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alamat</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $schedule["lokasi"] }}"
                            readonly
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input
                            type="date"
                            class="form-control"
                            value="{{ $schedule["tanggal"] }}"
                            readonly
                        />
                    </div>

                    @if (! empty($schedule["tanggal_selesai"]))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input
                                type="date"
                                class="form-control"
                                value="{{ $schedule["tanggal_selesai"] }}"
                                readonly
                            />
                        </div>
                    @endif

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Petugas</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $schedule["petugas"] }}"
                            readonly
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bidang</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $schedule["bidang"] }}"
                            readonly
                        />
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Produk</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $schedule["produk"] }}"
                            readonly
                        />
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Detail Produk</label>
                        @if (! empty($schedule["detail_produk"]) && count($schedule["detail_produk"]))
                            @foreach ($schedule["detail_produk"] as $detail)
                                <input
                                    type="text"
                                    class="form-control mb-2"
                                    value="{{ $detail }}"
                                    readonly
                                />
                            @endforeach
                        @else
                            <input
                                type="text"
                                class="form-control text-muted"
                                value="-"
                                readonly
                            />
                        @endif
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dokumen</label>

                        @if (! empty($schedule["dokumen"]) && count($schedule["dokumen"]))
                            @foreach ($schedule["dokumen"] as $doc)
                                <div class="input-group mb-2">
                                    <a
                                        href="{{ url("storage/" . $doc["path"]) }}"
                                        target="_blank"
                                        class="form-control text-decoration-none text-primary"
                                    >
                                        {{ $doc["name"] }}
                                    </a>
                                    <a
                                        href="{{ route("document.download", $doc["id"]) }}"
                                        class="btn btn-outline-primary"
                                        title="Download {{ $doc["name"] }}"
                                    >
                                        <i class="ti ti-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <input
                                type="text"
                                class="form-control text-muted"
                                value="-"
                                readonly
                            />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
