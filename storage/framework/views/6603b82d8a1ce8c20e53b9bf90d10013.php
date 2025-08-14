<?php $__env->startPush("styles"); ?>
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="editReportModal-jadwal-<?php echo e($i); ?>"
        tabindex="-1"
        aria-labelledby="editReportModalLabel-jadwal-<?php echo e($i); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
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

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    value="<?php echo e($schedule["tanggal"]); ?>"
                                    name="tanggal_mulai"
                                    readonly
                                />
                            </div>

                            <div class="col-md-6 mb-3">
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
                                <input
                                    type="file"
                                    class="form-control dokumen-input d-none"
                                    name="dokumen[]"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    id="dokumenInput-jadwal-<?php echo e($i); ?>"
                                />

                                <div class="dokumen-wrapper mt-2">
                                    <?php $__currentLoopData = $schedule["dokumen"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div
                                            class="input-group mb-2 dokumen-group"
                                        >
                                            <a
                                                href="<?php echo e(url("storage/" . $doc["path"])); ?>"
                                                target="_blank"
                                                class="form-control text-decoration-underline"
                                                readonly
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
                            disabled
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
        function validateForm(form) {
            const tanggalInput = form.querySelector('.tanggal-selesai-input');
            const dokumenBaru = form.querySelectorAll(
                '.dokumen-baru-preview',
            ).length;
            const dokumenLamaAktif = form.querySelectorAll(
                '.dokumen-group:not([style*="display: none"])',
            ).length;
            const tanggalBerubah =
                tanggalInput.value !== tanggalInput.dataset.original;
            const saveBtn = form.querySelector('.save-btn');

            if (
                (tanggalBerubah && tanggalInput.value) ||
                dokumenBaru > 0 ||
                dokumenLamaAktif > 0
            ) {
                saveBtn.disabled = false;
            } else {
                saveBtn.disabled = true;
            }
        }

        document.querySelectorAll('.toggle-edit-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const index = this.dataset.formIndex;
                const form = document.querySelector(
                    `form[data-form-index="${index}"]`,
                );
                const saveBtn = form.querySelector('.save-btn');
                const dokumenInput = form.querySelector('.dokumen-input');
                const tanggalSelesaiInput = form.querySelector(
                    `#tanggalSelesai-${index}`,
                );

                tanggalSelesaiInput.dataset.original =
                    tanggalSelesaiInput.value;
                tanggalSelesaiInput.removeAttribute('readonly');
                tanggalSelesaiInput.setAttribute(
                    'min',
                    form.querySelector('input[name="tanggal_mulai"]')?.value ??
                        '',
                );

                dokumenInput.classList.remove('d-none');
                form.querySelectorAll('.remove-dokumen-btn').forEach(
                    (btn) => (btn.style.display = 'inline-block'),
                );

                saveBtn.disabled = true;
                saveBtn.classList.remove('d-none');
                this.classList.add('d-none');

                tanggalSelesaiInput.addEventListener('input', () =>
                    validateForm(form),
                );
                dokumenInput.addEventListener('change', () =>
                    validateForm(form),
                );
            });
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-dokumen-btn')) {
                const group = e.target.closest('.dokumen-group');
                const checkbox = group.querySelector('.dokumen-checkbox');
                if (checkbox) checkbox.checked = true;
                group.style.display = 'none';
                validateForm(group.closest('form'));
            }
            if (e.target.closest('.remove-dokumen-baru-btn')) {
                const group = e.target.closest('.dokumen-group');
                group.remove();
                validateForm(group.closest('form'));
            }
        });

        document.querySelectorAll('.dokumen-input').forEach((input) => {
            input.addEventListener('change', function () {
                const form = this.closest('form');
                const wrapper = form.querySelector('.dokumen-wrapper');
                wrapper
                    .querySelectorAll('.dokumen-baru-preview')
                    .forEach((el) => el.remove());

                Array.from(this.files).forEach((file) => {
                    const group = document.createElement('div');
                    group.className =
                        'input-group mb-2 dokumen-group dokumen-baru-preview';
                    group.innerHTML = `
                    <input type="text" class="form-control" value="${file.name.replace(/</g, '&lt;').replace(/>/g, '&gt;')}" readonly>
                    <button type="button" class="btn btn-outline-danger remove-dokumen-baru-btn">
                        <i class="ti ti-x"></i>
                    </button>`;
                    wrapper.appendChild(group);
                });
                validateForm(form);
            });
        });

        document.querySelectorAll('.modal').forEach((modal) => {
            modal.addEventListener('hidden.bs.modal', function () {
                const form = modal.querySelector('form');
                const index = form.dataset.formIndex;
                const saveBtn = form.querySelector('.save-btn');
                const editBtn = form.querySelector('.toggle-edit-btn');
                const tanggalSelesaiInput = form.querySelector(
                    `#tanggalSelesai-${index}`,
                );
                const dokumenInput = form.querySelector('.dokumen-input');
                const wrapper = form.querySelector('.dokumen-wrapper');

                tanggalSelesaiInput.setAttribute('readonly', true);
                tanggalSelesaiInput.removeAttribute('min');
                dokumenInput.classList.add('d-none');
                dokumenInput.value = '';

                form.querySelectorAll('.dokumen-group').forEach((group) => {
                    group.style.display = 'flex';
                    group.querySelector('.remove-dokumen-btn').style.display =
                        'none';
                    group.querySelector('.dokumen-checkbox').checked = false;
                });

                wrapper
                    .querySelectorAll('.dokumen-baru-preview')
                    .forEach((el) => el.remove());
                saveBtn.classList.add('d-none');
                saveBtn.disabled = true;
                editBtn.classList.remove('d-none');
            });
        });

        document.querySelectorAll('.editable-form').forEach((form) => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const modalEl = form.closest('.modal');
                const modalId = modalEl.getAttribute('id');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);

                const dokumenAktif = form.querySelectorAll(
                    '.dokumen-group:not([style*="display: none"])',
                ).length;
                const dokumenBaru = form.querySelectorAll(
                    '.dokumen-baru-preview',
                ).length;

                if (dokumenAktif + dokumenBaru === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen diperlukan',
                        text: 'Minimal satu dokumen harus ada sebelum menyimpan.',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    });
                    return;
                }

                // Tutup modal dulu
                modalInstance.hide();

                // Tunggu animasi tutup selesai
                setTimeout(() => {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin menyimpan perubahan ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton:
                                'btn btn-success rounded-2 px-4 me-2',
                            cancelButton: 'btn btn-light rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Perubahan berhasil disimpan.',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        } else {
                            // Kalau batal → buka modal lagi
                            const reopenModal = new bootstrap.Modal(
                                document.getElementById(modalId),
                            );
                            reopenModal.show();
                        }
                    });
                }, 400); // jeda biar animasi close Bootstrap kelar
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/edit_report_modal.blade.php ENDPATH**/ ?>