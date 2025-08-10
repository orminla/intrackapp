@push("styles")
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
@endpush

@foreach ($changeRequests as $i => $requestChange)
    <div
        class="modal fade"
        id="viewChangeInspectorModal-{{ $i }}"
        tabindex="-1"
        aria-labelledby="viewChangeInspectorModalLabel-{{ $i }}"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
                <div
                    class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                >
                    <h4
                        class="modal-title fw-bold"
                        id="viewChangeInspectorModalLabel-{{ $i }}"
                    >
                        Detail Permintaan Ganti Petugas
                    </h4>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body pt-2 mt-2 text-start">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mitra</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $requestChange["mitra"] ?? "-" }}"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pengajuan</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ \Carbon\Carbon::parse($requestChange["requested_date"] ?? now())->format("Y-m-d") }}"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Petugas Lama</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $requestChange["petugas"] ?? "-" }}"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Petugas Baru</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $requestChange["petugas_baru"] ?? "-" }}"
                                readonly
                            />
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alasan Penggantian</label>
                            <textarea class="form-control" rows="3" readonly>
{{ $requestChange["reason"] ?? "-" }}</textarea
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
