@push("styles")
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
@endpush

<div
    class="modal fade"
    id="changeInspectorModal-{{ $i }}"
    tabindex="-1"
    aria-labelledby="changeInspectorModalLabel-{{ $i }}"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
            <div
                class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
            >
                <h4
                    class="modal-title fw-bold"
                    id="changeInspectorModalLabel-{{ $i }}"
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
                <form
                    action="{{ route("admin.change_request.update") }}"
                    method="POST"
                >
                    @csrf
                    <input
                        type="hidden"
                        name="change_request_id"
                        value="{{ $requestChange["id"] }}"
                    />

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mitra</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $requestChange["mitra"] }}"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pengajuan</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ \Carbon\Carbon::parse($requestChange["requested_date"])->format("Y-m-d") }}"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Petugas Lama</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $requestChange["petugas"] }}"
                                readonly
                            />
                        </div>

                        <!-- Petugas Baru -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Petugas Baru</label>
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="inspectorDisplay-{{ $requestChange["id"] }}"
                                    value="{{ $requestChange["petugas_baru"] ?? "-" }}"
                                    readonly
                                />
                                <input
                                    type="hidden"
                                    name="new_inspector_id"
                                    id="inspectorId-{{ $requestChange["id"] }}"
                                    value="{{ $requestChange["new_inspector_id"] ?? "" }}"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-equal"
                                    id="btnGantiPetugas-{{ $requestChange["id"] }}"
                                    data-target="{{ $requestChange["id"] }}"
                                    data-portfolio-id="{{ $requestChange->schedule->inspector->portfolio_id ?? "" }}"
                                    data-started-date="{{ $requestChange["requested_date"] }}"
                                >
                                    Ganti
                                </button>
                            </div>
                            <small
                                class="text-muted d-block mt-1"
                                id="inspectorQuota-{{ $requestChange["id"] }}"
                            >
                                Ketersediaan: -
                            </small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alasan Penggantian</label>
                            <textarea class="form-control" rows="3" readonly>
{{ $requestChange["reason"] }}</textarea
                            >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{--
    <script>
    document.querySelectorAll('[id^=btnGantiPetugas]').forEach((button) => {
    button.addEventListener('click', function () {
    const targetId = this.dataset.target;
    const portfolioId = this.dataset.portfolioId;
    const startedDate = this.dataset.startedDate;
    
    console.log('portfolioId', portfolioId);
    console.log('startedDate', startedDate);
    
    fetch(
    `/admin/get-inspector?portfolio_id=${portfolioId}&started_date=${startedDate}`,
    )
    .then((res) => res.json())
    .then((data) => {
    if (data.inspector_id && data.name) {
    const nameInput = document.getElementById(
    `inspectorDisplay-${targetId}`,
    );
    const idInput = document.getElementById(
    `inspectorId-${targetId}`,
    );
    const quotaText = document.getElementById(
    `inspectorQuota-${targetId}`,
    );
    
    console.log('Target ID:', targetId);
    console.log('Name input:', nameInput);
    console.log('ID input:', idInput);
    console.log('Quota text:', quotaText);
    
    if (nameInput && idInput && quotaText) {
    nameInput.value = data.name;
    idInput.value = data.inspector_id;
    quotaText.textContent = `Ketersediaan: ${data.note ?? '-'}`;
    } else {
    alert('Elemen input tidak ditemukan di DOM.');
    }
    }
    })
    .catch(async (err) => {
    const text = await err.text?.();
    console.error('Fetch error:', err);
    console.error('Response text:', text);
    alert('Terjadi kesalahan saat mengambil data petugas.');
    });
    });
    });
    </script>
--}}
