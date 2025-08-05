@push("styles")
    <style>
        .tambah-laporan-dialog {
            max-width: 680px;
            margin: 1.5rem auto;
        }
    </style>
@endpush

@if ($jadwalDalamProses)
    <div
        class="modal fade"
        id="tambahLaporanModal"
        tabindex="-1"
        aria-labelledby="tambahLaporanModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered tambah-laporan-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h4
                        class="modal-title fw-semibold"
                        id="tambahLaporanModalLabel"
                    >
                        Tambah Laporan
                    </h4>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <form
                    method="POST"
                    action="{{ route("inspector.jadwal.store") }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <input
                        type="hidden"
                        name="schedule_id"
                        value="{{ $jadwalDalamProses["id"] }}"
                    />

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mitra</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $jadwalDalamProses["mitra"] }}"
                                readonly
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $jadwalDalamProses["lokasi"] }}"
                                readonly
                            />
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $jadwalDalamProses["tanggal"] }}"
                                    readonly
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal Selesai
                                </label>
                                <input
                                    type="date"
                                    name="tanggal_selesai"
                                    class="form-control"
                                    required
                                    min="{{ $jadwalDalamProses["tanggal"] }}"
                                />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Produk</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $jadwalDalamProses["produk"] }}"
                                readonly
                            />
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Unggah Dokumentasi</label>
                            <div class="d-flex gap-2 mb-2">
                                <input
                                    type="file"
                                    id="dokumentasiInput"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                />
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    id="addFileBtn"
                                    disabled
                                >
                                    Tambah
                                </button>
                            </div>
                            <small class="text-muted d-block mb-2">
                                Maksimal 3 file. Format: JPG, PNG, PDF.
                            </small>

                            <label class="form-label fw-semibold">
                                File Terpilih:
                            </label>
                            <ul
                                id="fileList"
                                class="list-unstyled mb-0 text-muted small"
                            ></ul>

                            {{-- Hidden input untuk menyimpan file real yang akan disubmit --}}
                            <div id="hiddenInputsContainer"></div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button
                            type="submit"
                            class="btn btn-success w-100 py-2 rounded-2"
                            id="submitBtn"
                            disabled
                            style="font-size: 1rem"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('dokumentasiInput');
            const fileList = document.getElementById('fileList');
            const addBtn = document.getElementById('addFileBtn');
            const submitBtn = document.getElementById('submitBtn');
            const hiddenContainer = document.getElementById(
                'hiddenInputsContainer',
            );

            const selectedFiles = [];

            function renderFileList() {
                fileList.innerHTML = '';
                hiddenContainer.innerHTML = '';
                selectedFiles.forEach((file, index) => {
                    const li = document.createElement('li');
                    li.classList.add(
                        'd-flex',
                        'justify-content-between',
                        'align-items-center',
                        'mb-1',
                    );
                    li.innerHTML = `
                <span>${index + 1}. ${file.name}</span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-file" data-index="${index}">
                    <i class="ti ti-x"></i>
                </button>
            `;
                    fileList.appendChild(li);

                    // Hidden input for each file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    const tempInput = document.createElement('input');
                    tempInput.type = 'file';
                    tempInput.name = 'dokumentasi[]';
                    tempInput.files = dataTransfer.files;
                    tempInput.hidden = true;

                    hiddenContainer.appendChild(tempInput);
                });

                addBtn.disabled = !fileInput.files.length;
                submitBtn.disabled = selectedFiles.length === 0;
            }

            fileInput.addEventListener('change', function () {
                addBtn.disabled = !fileInput.files.length;
            });

            addBtn.addEventListener('click', function () {
                const file = fileInput.files[0];
                if (!file) return;

                if (selectedFiles.length >= 3) {
                    alert('Maksimal unggah 3 file.');
                    return;
                }

                const alreadyExists = selectedFiles.some(
                    (f) => f.name === file.name,
                );
                if (alreadyExists) {
                    alert('File dengan nama yang sama sudah ditambahkan.');
                    fileInput.value = '';
                    return;
                }

                selectedFiles.push(file);
                fileInput.value = '';
                renderFileList();
            });

            fileList.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-remove-file');
                if (!btn) return;

                const index = parseInt(btn.dataset.index);
                selectedFiles.splice(index, 1);
                renderFileList();
            });
        });
    </script>
@endpush
