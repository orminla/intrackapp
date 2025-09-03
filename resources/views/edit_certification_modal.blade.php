<div
    class="modal fade"
    id="editCertifModal-{{ $cert->certification_id ?? "new" }}"
    tabindex="-1"
    aria-labelledby="editCertifLabel-{{ $cert->certification_id ?? "new" }}"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
            <form
                method="POST"
                action="{{ isset($cert) ? route("certifications.update", $cert->certification_id) : route("certifications.store") }}"
                enctype="multipart/form-data"
                class="editable-form"
                data-form-id="{{ $cert->certification_id ?? "new" }}"
            >
                @csrf
                @if (isset($cert))
                    @method("PUT")
                @endif

                <div
                    class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                >
                    <h4 class="modal-title fw-bold">
                        {{ isset($cert) ? "Ubah Sertifikasi" : "Tambah Sertifikasi" }}
                    </h4>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body pt-2 mt-2 text-start">
                    <div class="row g-3 mb-3">
                        <!-- Nama Sertifikasi -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Sertifikasi</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ $cert->name ?? "" }}"
                                readonly
                                required
                            />
                        </div>

                        <!-- Penerbit -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penerbit</label>
                            <input
                                type="text"
                                name="issuer"
                                class="form-control"
                                value="{{ $cert->issuer ?? "" }}"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Tanggal Terbit -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Terbit</label>
                            <input
                                type="date"
                                name="issued_at"
                                class="form-control"
                                value="{{ $cert->issued_at ?? "" }}"
                                readonly
                            />
                        </div>

                        <!-- Tanggal Kadaluarsa -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kadaluarsa</label>
                            <input
                                type="date"
                                name="expired_at"
                                class="form-control"
                                value="{{ $cert->expired_at ?? "" }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label">Dokumen (PDF/JPG/PNG)</label>
                        <input
                            type="file"
                            class="form-control d-none dokumen-input"
                            name="file_path"
                            accept=".pdf,.jpg,.jpeg,.png"
                        />

                        <div class="dokumen-wrapper mt-2">
                            @if (isset($cert) && $cert->file_path)
                                <div class="input-group mb-2 dokumen-group">
                                    <a
                                        href="{{ route("certifications.view", $cert->certification_id) }}"
                                        target="_blank"
                                        class="form-control text-decoration-underline"
                                        readonly
                                    >
                                        {{ $cert->original_name }}
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger remove-dokumen-btn"
                                        style="display: none"
                                    >
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Portofolio terkait -->
                    <div class="mb-3">
                        <label class="form-label">Portofolio</label>
                        <select
                            name="portfolio_id"
                            class="form-select"
                            disabled
                        >
                            <option value="">Tidak terkait</option>
                            @foreach ($allPortfolios as $portfolio)
                                <option
                                    value="{{ $portfolio->portfolio_id }}"
                                    {{ isset($cert) && $cert->portfolio_id == $portfolio->portfolio_id ? "selected" : "" }}
                                >
                                    {{ $portfolio->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button
                        type="button"
                        class="btn btn-primary w-100 toggle-edit-btn"
                    >
                        Edit
                    </button>
                    <button
                        type="submit"
                        class="btn btn-success w-100 d-none save-btn mt-2"
                        disabled
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
