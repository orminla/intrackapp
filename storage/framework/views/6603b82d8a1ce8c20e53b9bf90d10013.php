<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="editReportModal-jadwal-<?php echo e($i); ?>"
        tabindex="-1"
        aria-labelledby="editReportModalLabel-jadwal-<?php echo e($i); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3">
                <form
                    method="POST"
                    action="<?php echo e(route("inspector.jadwal.update", $schedule["id"])); ?>"
                    enctype="multipart/form-data"
                    class="editable-form"
                    data-form-index="jadwal-<?php echo e($i); ?>"
                >
                    <?php echo csrf_field(); ?>
                    <?php echo method_field("PUT"); ?>

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4 class="modal-title fw-bold">
                            Ubah Laporan Inspeksi
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
                            <?php if(! empty($schedule["alasan_penolakan"])): ?>
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
<?php echo e($schedule["alasan_penolakan"]); ?></textarea
                                    >
                                </div>
                            <?php endif; ?>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mitra</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($schedule["mitra"]); ?>"
                                    readonly
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($schedule["lokasi"]); ?>"
                                    readonly
                                />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    value="<?php echo e($schedule["tanggal"]); ?>"
                                    name="tanggal_mulai"
                                    readonly
                                />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Tanggal Selesai
                                </label>
                                <input
                                    type="date"
                                    class="form-control tanggal-selesai-input"
                                    name="tanggal_selesai"
                                    value="<?php echo e($schedule["tanggal_selesai"]); ?>"
                                    readonly
                                    id="tanggalSelesai-jadwal-<?php echo e($i); ?>"
                                />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Tunda</label>
                                <input
                                    type="date"
                                    class="form-control tanggal-tunda-input"
                                    name="tanggal_tunda"
                                    value="<?php echo e($schedule["tanggal_tunda"]); ?>"
                                    readonly
                                />
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alasan Tunda</label>
                                <textarea
                                    class="form-control keterangan-tunda-input"
                                    name="keterangan_tunda"
                                    rows="2"
                                    placeholder="Masukkan alasan penundaan (jika ada)"
                                    readonly
                                    style="resize: none"
                                >
<?php echo e($schedule["keterangan_tunda"]); ?></textarea
                                >
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Produk</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($schedule["produk"]); ?>"
                                    readonly
                                />
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Detail Produk</label>
                                <?php $__currentLoopData = $schedule["detail_produk"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input
                                        type="text"
                                        class="form-control mb-2"
                                        value="<?php echo e($detail); ?>"
                                        readonly
                                    />
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="col-md-12 mt-2">
                                <label class="form-label">
                                    Dokumen (PDF/JPG/PNG)
                                </label>

                                <div
                                    class="input-group dokumen-upload-wrapper mb-2 d-none"
                                >
                                    <input
                                        type="file"
                                        class="form-control dokumen-input"
                                        name="dokumen[]"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        multiple
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary tambah-dokumen-btn"
                                    >
                                        Tambah
                                    </button>
                                </div>

                                <div class="dokumen-wrapper mt-2">
                                    <?php $__currentLoopData = $schedule["dokumen"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div
                                            class="input-group mb-2 dokumen-group"
                                        >
                                            <a
                                                href="<?php echo e(url("storage/" . $doc["path"])); ?>"
                                                target="_blank"
                                                class="form-control text-decoration-underline"
                                            >
                                                <?php echo e($doc["name"]); ?>

                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger remove-dokumen-btn"
                                                style="display: none"
                                                data-doc-id="<?php echo e($doc["id"]); ?>"
                                            >
                                                <i class="ti ti-x"></i>
                                            </button>
                                            <input
                                                type="checkbox"
                                                name="hapus_dokumen[]"
                                                class="d-none dokumen-checkbox"
                                                value="<?php echo e($doc["id"]); ?>"
                                            />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button
                            type="button"
                            class="btn btn-primary w-100 toggle-edit-btn"
                            data-form-index="jadwal-<?php echo e($i); ?>"
                        >
                            Edit
                        </button>
                        <button
                            type="submit"
                            class="btn btn-success w-100 d-none save-btn mt-2"
                            data-form-index="jadwal-<?php echo e($i); ?>"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        const MAX_DOKUMEN = 3;

        // Toggle edit
        document.querySelectorAll('.toggle-edit-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const index = this.dataset.formIndex;
                const form = document.querySelector(
                    `form[data-form-index="${index}"]`,
                );
                const saveBtn = form.querySelector('.save-btn');
                const dokumenInputWrapper = form.querySelector(
                    '.dokumen-upload-wrapper',
                );

                // Buka tanggal selesai
                const tanggalSelesaiInput = form.querySelector(
                    `#tanggalSelesai-${index}`,
                );
                tanggalSelesaiInput.removeAttribute('readonly');
                tanggalSelesaiInput.setAttribute(
                    'min',
                    form.querySelector('input[name="tanggal_mulai"]').value ??
                        '',
                );

                // Buka tanggal tunda & keterangan tunda
                const tanggalTundaInput = form.querySelector(
                    'input[name="tanggal_tunda"]',
                );
                if (tanggalTundaInput)
                    tanggalTundaInput.removeAttribute('readonly');

                const keteranganTundaInput = form.querySelector(
                    'textarea[name="keterangan_tunda"]',
                );
                if (keteranganTundaInput)
                    keteranganTundaInput.removeAttribute('readonly');

                // Buka dokumen
                dokumenInputWrapper.classList.remove('d-none');
                form.querySelectorAll('.remove-dokumen-btn').forEach(
                    (b) => (b.style.display = 'inline-block'),
                );

                saveBtn.classList.remove('d-none'); // tombol Simpan muncul langsung
                this.classList.add('d-none');
            });
        });

        // Tambah dokumen baru
        document.querySelectorAll('.tambah-dokumen-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const wrapper = this.closest('.dokumen-upload-wrapper');
                const input = wrapper.querySelector('.dokumen-input');
                const form = wrapper.closest('form');
                const dokumenWrapper = form.querySelector('.dokumen-wrapper');

                if (!input.files.length) return;

                const existingCount = dokumenWrapper.querySelectorAll(
                    '.dokumen-group:not([style*="display: none"])',
                ).length;
                if (existingCount + input.files.length > MAX_DOKUMEN) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas dokumen',
                        text: `Maksimal ${MAX_DOKUMEN} dokumen per laporan.`,
                        confirmButtonText: 'OK',
                    });
                    return;
                }

                // Tambah preview di DOM
                Array.from(input.files).forEach((file) => {
                    const group = document.createElement('div');
                    group.className =
                        'input-group mb-2 dokumen-group dokumen-baru-preview';
                    group.innerHTML = `
                <input type="text" class="form-control" value="${file.name}" readonly>
                <button type="button" class="btn btn-outline-danger remove-dokumen-baru-btn">
                    <i class="ti ti-x"></i>
                </button>
            `;
                    dokumenWrapper.appendChild(group);
                });

                validateForm(form);
            });
        });

        // Hapus dokumen baru
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-dokumen-baru-btn')) {
                const group = e.target.closest('.dokumen-group');
                const form = group.closest('form');
                const input = form.querySelector('.dokumen-input');

                const dt = new DataTransfer();
                Array.from(input.files)
                    .filter(
                        (f) =>
                            f.name !==
                            group.querySelector('input[type="text"]').value,
                    )
                    .forEach((f) => dt.items.add(f));
                input.files = dt.files;

                group.remove();
                validateForm(form);
            }
        });

        // Hapus dokumen lama
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-dokumen-btn')) {
                const btn = e.target.closest('.remove-dokumen-btn');
                const group = btn.closest('.dokumen-group');
                const checkbox = group.querySelector('.dokumen-checkbox');
                checkbox.checked = !checkbox.checked;
                group.style.display = checkbox.checked ? 'none' : 'flex';
                validateForm(group.closest('form'));
            }
        });

        // Submit dengan konfirmasi
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        document.querySelectorAll('.editable-form').forEach((form) => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const modalEl = form.closest('.modal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menyimpan perubahan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-success rounded-2 px-4 me-2',
                        cancelButton: 'btn btn-light rounded-2 px-4',
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                modalInstance.hide();

                                // SweetAlert sukses otomatis hilang
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil diperbarui',
                                    text: data.message,
                                    timer: 2000, // muncul 2 detik
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                    customClass: {
                                        popup: 'rounded-4',
                                    },
                                }).then(() => {
                                    location.reload();
                                });

                                // Opsional: update DOM / table setelah berhasil
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message || 'Terjadi kesalahan',
                                    confirmButtonText: 'OK',
                                });
                            }
                        })
                        .catch((err) => {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim data.',
                                confirmButtonText: 'OK',
                            });
                        });
                });
            });
        });

        // Reset modal saat ditutup
        document.querySelectorAll('.modal').forEach((modal) => {
            modal.addEventListener('hidden.bs.modal', () => {
                const form = modal.querySelector('form');
                const saveBtn = form.querySelector('.save-btn');
                const editBtn = form.querySelector('.toggle-edit-btn');
                const tanggalSelesaiInput = form.querySelector(
                    '.tanggal-selesai-input',
                );
                const dokumenInputWrapper = form.querySelector(
                    '.dokumen-upload-wrapper',
                );
                const wrapper = form.querySelector('.dokumen-wrapper');

                tanggalSelesaiInput.setAttribute('readonly', true);
                tanggalSelesaiInput.removeAttribute('min');
                dokumenInputWrapper.classList.add('d-none');

                // Hanya hapus preview baru, file tetap ada
                wrapper
                    .querySelectorAll('.dokumen-baru-preview')
                    .forEach((el) => el.remove());

                // Reset tombol
                saveBtn.classList.add('d-none');
                saveBtn.disabled = true;
                editBtn.classList.remove('d-none');
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/edit_report_modal.blade.php ENDPATH**/ ?>