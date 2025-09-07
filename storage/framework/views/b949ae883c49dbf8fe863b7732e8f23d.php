<?php $__env->startPush("styles"); ?>
    <style>
        .tambah-laporan-dialog {
            max-width: 680px;
            margin: 1.5rem auto;
        }

        .tambah-laporan-dialog .modal-content {
            padding: 1rem;
        }

        .tambah-laporan-dialog .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }

        #fileList li span {
            font-size: 0.95rem;
            font-weight: 500;
            color: #333;
        }

        .file-preview {
            margin-top: 6px;
            max-height: 200px;
            overflow: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 4px;
            background: #fff;
        }

        .file-preview img {
            display: block;
            max-width: 100%;
        }
        .file-preview iframe {
            width: 100%;
            height: 200px;
            border: none;
        }

        #fileList {
            margin-top: 0.75rem;
        }

        #fileList li {
            margin-bottom: 1rem;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php if($jadwalDalamProses): ?>
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
                    action="<?php echo e(route("inspector.jadwal.store")); ?>"
                    enctype="multipart/form-data"
                >
                    <?php echo csrf_field(); ?>
                    <input
                        type="hidden"
                        name="schedule_id"
                        value="<?php echo e($jadwalDalamProses["id"]); ?>"
                    />

                    <div class="modal-body">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mitra</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($jadwalDalamProses["mitra"]); ?>"
                                    readonly
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($jadwalDalamProses["lokasi"]); ?>"
                                    readonly
                                />
                            </div>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Produk</label>
                            <input
                                type="text"
                                class="form-control"
                                value="<?php echo e($jadwalDalamProses["produk"]); ?>"
                                readonly
                            />
                        </div>

                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($jadwalDalamProses["tanggal"]); ?>"
                                    readonly
                                />
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Tunda</label>
                                <input
                                    type="date"
                                    name="tanggal_tunda"
                                    class="form-control"
                                    min="<?php echo e($jadwalDalamProses["tanggal"]); ?>"
                                />
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Tanggal Selesai
                                </label>
                                <input
                                    type="date"
                                    name="tanggal_selesai"
                                    class="form-control"
                                    required
                                    min="<?php echo e($jadwalDalamProses["tanggal"]); ?>"
                                />
                            </div>

                            <small class="text-muted mt-2">
                                Tanggal Tunda bersifat opsional, gunakan jika
                                jadwal ditunda.
                            </small>
                        </div>

                        
                        <div class="mb-3 d-none" id="keteranganTundaWrapper">
                            <label class="form-label">Keterangan Tunda</label>
                            <textarea
                                name="keterangan_tunda"
                                class="form-control"
                                rows="3"
                                maxlength="255"
                                placeholder="Alasan penundaan"
                            ></textarea>
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
<?php endif; ?>

<?php $__env->startPush("scripts"); ?>
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
            const tambahLaporanModal =
                document.getElementById('tambahLaporanModal');
            const form = tambahLaporanModal
                ? tambahLaporanModal.querySelector('form')
                : null;

            function renderFileList() {
                fileList.innerHTML = '';
                hiddenContainer.innerHTML = '';
                selectedFiles.forEach((file, index) => {
                    const li = document.createElement('li');
                    li.classList.add(
                        'mb-3',
                        'p-2',
                        'border',
                        'rounded',
                        'bg-light',
                    );

                    // nama file
                    let fileInfo = `<span>${index + 1}. ${file.name}</span>`;

                    // preview file (image / pdf)
                    let preview = '';
                    if (file.type.startsWith('image/')) {
                        preview = `<div class="file-preview"><img src="${URL.createObjectURL(file)}" class="img-fluid"/></div>`;
                    } else if (file.type === 'application/pdf') {
                        preview = `<div class="file-preview"><iframe src="${URL.createObjectURL(file)}"></iframe></div>`;
                    }

                    li.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>${fileInfo}</div>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-file" data-index="${index}">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            ${preview}
        `;
                    fileList.appendChild(li);

                    // hidden input agar file terkirim saat submit
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

            // === Submit AJAX + SweetAlert ===
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(async (response) => {
                            const data = await response.json();
                            if (!response.ok)
                                throw new Error(
                                    data.message || 'Gagal menyimpan data',
                                );
                            return data;
                        })
                        .then((data) => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text:
                                    data.message ||
                                    'Laporan berhasil disimpan.',
                                timer: 1500,
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-primary rounded-2 px-4',
                                },
                                showConfirmButton: false,
                            });
                            bootstrap.Modal.getInstance(
                                tambahLaporanModal,
                            ).hide();
                            setTimeout(() => location.reload(), 1600);
                        })
                        .catch((error) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text:
                                    error.message ||
                                    'Terjadi kesalahan, silakan coba lagi.',
                            });
                        });
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const tanggalTundaInput = document.querySelector(
                '[name="tanggal_tunda"]',
            );
            const keteranganWrapper = document.getElementById(
                'keteranganTundaWrapper',
            );

            tanggalTundaInput.addEventListener('input', function () {
                if (this.value) {
                    keteranganWrapper.classList.remove('d-none');
                    keteranganWrapper
                        .querySelector('textarea')
                        .setAttribute('required', 'required');
                } else {
                    keteranganWrapper.classList.add('d-none');
                    keteranganWrapper
                        .querySelector('textarea')
                        .removeAttribute('required');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/add_report_modal.blade.php ENDPATH**/ ?>