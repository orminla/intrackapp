<style>
    .custom-modal-width {
        max-width: 85%;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 768px) {
        .custom-modal-width {
            max-width: 480px;
        }
    }
</style>

@foreach ($data as $item)
    @if (strtolower($item["status"]) === "menunggu konfirmasi")
        <!-- Modal Tolak Laporan -->
        <div
            class="modal fade"
            id="modalTolak-{{ $item["id"] }}"
            tabindex="-1"
            aria-labelledby="modalTolakLabel-{{ $item["id"] }}"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered custom-modal-width">
                <div class="modal-content rounded-4 p-3">
                    <!-- Header -->
                    <div class="modal-header border-0 pb-0">
                        <h4
                            class="modal-title fw-semibold"
                            id="modalTolakLabel-{{ $item["id"] }}"
                        >
                            Tolak Laporan
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Tutup"
                        ></button>
                    </div>

                    <!-- Form -->
                    <form
                        method="POST"
                        action="{{ route("admin.laporan.validasi", $item["id"]) }}"
                    >
                        @csrf
                        @method("PUT")
                        <input type="hidden" name="status" value="Ditolak" />

                        <!-- Body -->
                        <div class="modal-body pt-2">
                            <p class="mb-4">
                                Apakah Anda yakin ingin menolak laporan inspeksi
                                dari
                                <strong>{{ $item["nama_mitra"] }}</strong>
                                ?
                            </p>

                            <div>
                                <label
                                    for="alasan-{{ $item["id"] }}"
                                    class="fw-semibold mb-1"
                                >
                                    Alasan Penolakan
                                </label>
                                <textarea
                                    id="alasan-{{ $item["id"] }}"
                                    name="alasan"
                                    class="form-control rounded-3"
                                    placeholder="Contoh: Dokumen tidak lengkap atau alasan lainnya"
                                    rows="3"
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="modal-footer border-0 pt-0 mt-2 justify-content-end"
                        >
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
