<?php $__env->startPush("styles"); ?>
    <style>
        .add-detail-btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            display: none;
        }

        .add-detail-btn i {
            font-size: 1rem;
        }

        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="detailScheduleModal-<?php echo e($index); ?>"
        tabindex="-1"
        aria-labelledby="detailScheduleModalLabel-<?php echo e($index); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
                <form
                    method="POST"
                    action="<?php echo e(route("admin.jadwal.update", $schedule["id"])); ?>"
                    class="editable-form"
                    data-form-index="<?php echo e($index); ?>"
                    data-original-detail="<?php echo e(json_encode($schedule["detail_produk"])); ?>"
                >
                    <?php echo csrf_field(); ?>
                    <?php echo method_field("PUT"); ?>

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4
                            class="modal-title fw-bold"
                            id="detailScheduleModalLabel-<?php echo e($index); ?>"
                        >
                            Detail Jadwal Inspeksi
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
                            <?php
                                $fields = [
                                    ["label" => "Mitra", "value" => $schedule["nama_mitra"], "name" => "nama_mitra"],
                                    ["label" => "Tanggal Inspeksi", "value" => $schedule["tanggal_inspeksi"], "name" => "tanggal_inspeksi"],
                                    ["label" => "Alamat", "value" => $schedule["lokasi"], "name" => "lokasi"],
                                    ["label" => "Petugas", "value" => $schedule["nama_petugas"], "name" => "nama_petugas"],
                                    ["label" => "Produk", "value" => $schedule["produk"], "name" => "produk"],
                                    ["label" => "Portofolio", "value" => $schedule["portofolio"], "name" => "portofolio"],
                                ];
                            ?>

                            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <?php echo e($f["label"]); ?>

                                    </label>
                                    <input
                                        type="<?php echo e($f["name"] === "tanggal_inspeksi" ? "date" : "text"); ?>"
                                        class="form-control <?php echo e(in_array($f["name"], ["nama_petugas", "portofolio"]) ? "text-muted bg-light" : ""); ?>"
                                        name="<?php echo e($f["name"]); ?>"
                                        value="<?php echo e($f["value"]); ?>"
                                        <?php echo e(in_array($f["name"], ["nama_petugas", "portofolio"]) ? "readonly" : "readonly required"); ?>

                                        id="<?php echo e($f["name"] === "tanggal_inspeksi" ? "tanggalInspeksi-" . $index : ""); ?>"
                                    />
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <div class="col-md-12 mt-2">
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2"
                                >
                                    <label class="form-label m-0">
                                        Detail Produk
                                    </label>
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary d-flex align-items-center gap-2 add-detail-btn d-none"
                                        data-form-index="<?php echo e($index); ?>"
                                    >
                                        <i class="ti ti-plus"></i>
                                        Tambah Detail
                                    </button>
                                </div>

                                <div class="detail-produk-wrapper">
                                    <?php $__empty_1 = true; $__currentLoopData = $schedule["detail_produk"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div
                                            class="input-group mb-2 detail-produk-group"
                                        >
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="detail_produk[]"
                                                value="<?php echo e($detail); ?>"
                                                required
                                                readonly
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger remove-detail-btn"
                                                style="display: none"
                                            >
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div
                                            class="input-group mb-2 detail-produk-group"
                                        >
                                            <input
                                                type="text"
                                                class="form-control text-muted"
                                                value="-"
                                                disabled
                                            />
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button
                            type="button"
                            class="btn btn-primary w-100 toggle-edit-btn"
                            data-form-index="<?php echo e($index); ?>"
                        >
                            Edit Jadwal
                        </button>
                        <button
                            type="submit"
                            class="btn btn-success w-100 d-none save-btn mt-2"
                            data-form-index="<?php echo e($index); ?>"
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
        // Toggle edit mode
        document.querySelectorAll('.toggle-edit-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const index = this.dataset.formIndex;
                const form = document.querySelector(
                    `form[data-form-index="${index}"]`,
                );
                const inputs = form.querySelectorAll('input[name]');
                const saveBtn = form.querySelector('.save-btn');
                const addDetailBtn = form.querySelector('.add-detail-btn');
                const wrapper = form.querySelector('.detail-produk-wrapper');
                const tanggalInspeksiInput = form.querySelector(
                    `#tanggalInspeksi-${index}`,
                );

                const updateRemoveButtons = () => {
                    const groups = wrapper.querySelectorAll(
                        '.detail-produk-group',
                    );
                    groups.forEach((group) => {
                        const removeBtn =
                            group.querySelector('.remove-detail-btn');
                        if (groups.length > 1) {
                            removeBtn.style.display = 'inline-block';
                            removeBtn.disabled = false;
                        } else {
                            removeBtn.style.display = 'none';
                            removeBtn.disabled = true;
                        }
                    });
                };

                inputs.forEach((input) => {
                    const name = input.getAttribute('name');
                    if (name !== 'nama_petugas' && name !== 'portofolio') {
                        input.removeAttribute('readonly');
                    }
                });

                if (tanggalInspeksiInput) {
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${yyyy}-${mm}-${dd}`;
                    tanggalInspeksiInput.setAttribute('min', todayStr);
                }

                this.classList.add('d-none');
                saveBtn.classList.remove('d-none');
                saveBtn.disabled = false;
                addDetailBtn.classList.remove('d-none');
                addDetailBtn.style.display = 'inline-flex';

                wrapper
                    .querySelectorAll('.remove-detail-btn')
                    .forEach((removeBtn) => {
                        removeBtn.addEventListener('click', function () {
                            this.closest('.detail-produk-group').remove();
                            saveBtn.disabled = false;
                            updateRemoveButtons();
                        });
                    });

                if (!addDetailBtn.dataset.bound) {
                    addDetailBtn.addEventListener('click', function () {
                        const group = document.createElement('div');
                        group.className =
                            'input-group mb-2 detail-produk-group';
                        group.innerHTML = `
                        <input type="text" name="detail_produk[]" class="form-control" required />
                        <button type="button" class="btn btn-outline-danger remove-detail-btn">
                            <i class="ti ti-x"></i>
                        </button>
                    `;
                        wrapper.appendChild(group);
                        saveBtn.disabled = false;

                        group
                            .querySelector('.remove-detail-btn')
                            .addEventListener('click', function () {
                                group.remove();
                                saveBtn.disabled = false;
                                updateRemoveButtons();
                            });

                        updateRemoveButtons();
                        group.querySelector('input').focus();
                    });
                    addDetailBtn.dataset.bound = 'true';
                }

                updateRemoveButtons();
            });
        });

        // Reset form on modal close
        document.querySelectorAll('.modal').forEach((modal) => {
            modal.addEventListener('hidden.bs.modal', function () {
                const form = modal.querySelector('form');
                if (!form) return;

                const index = form.dataset.formIndex;
                const saveBtn = form.querySelector('.save-btn');
                const editBtn = form.querySelector('.toggle-edit-btn');
                const addDetailBtn = form.querySelector('.add-detail-btn');
                const wrapper = form.querySelector('.detail-produk-wrapper');
                const tanggalInspeksiInput = form.querySelector(
                    `#tanggalInspeksi-${index}`,
                );

                form.querySelectorAll('input[name]').forEach((input) => {
                    const name = input.getAttribute('name');
                    if (
                        name !== 'nama_petugas' &&
                        name !== 'portofolio' &&
                        name !== 'tanggal_inspeksi'
                    ) {
                        input.setAttribute('readonly', true);
                    }
                });

                if (tanggalInspeksiInput) {
                    tanggalInspeksiInput.setAttribute('readonly', true);
                    tanggalInspeksiInput.removeAttribute('min');
                }

                const originalDetails = JSON.parse(
                    form.dataset.originalDetail || '[]',
                );
                wrapper.innerHTML = '';
                if (originalDetails.length) {
                    originalDetails.forEach((detail) => {
                        const group = document.createElement('div');
                        group.className =
                            'input-group mb-2 detail-produk-group';
                        group.innerHTML = `
                        <input type="text" class="form-control" name="detail_produk[]" value="${detail}" readonly required>
                        <button type="button" class="btn btn-outline-danger remove-detail-btn" style="display: none">
                            <i class="ti ti-x"></i>
                        </button>
                    `;
                        wrapper.appendChild(group);
                    });
                } else {
                    wrapper.innerHTML = `<input type="text" class="form-control text-muted" value="-" disabled />`;
                }

                editBtn.classList.remove('d-none');
                saveBtn.classList.add('d-none');
                saveBtn.disabled = true;
                addDetailBtn.classList.add('d-none');
                addDetailBtn.style.display = 'none';
            });
        });

        // submit form ajax
        document.querySelectorAll('.editable-form').forEach((form) => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const currentForm = this;
                const modalEl = currentForm.closest('.modal');
                let modalInstance = bootstrap.Modal.getInstance(modalEl);

                // Tutup modal dulu kalau ada
                if (modalInstance) modalInstance.hide();

                modalEl.addEventListener(
                    'hidden.bs.modal',
                    function onHidden() {
                        modalEl.removeEventListener(
                            'hidden.bs.modal',
                            onHidden,
                        );

                        Swal.fire({
                            title: 'Konfirmasi',
                            text: 'Simpan perubahan jadwal inspeksi?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, simpan',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-primary px-4 me-2',
                                cancelButton: 'btn btn-outline-danger px-4',
                            },
                            buttonsStyling: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const formData = new FormData(currentForm);
                                fetch(currentForm.action, {
                                    method: currentForm.method,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]',
                                        ).content,
                                        Accept: 'application/json',
                                    },
                                    body: formData,
                                })
                                    .then(async (response) => {
                                        const data = await response.json();
                                        if (!response.ok)
                                            throw new Error(
                                                data.message ||
                                                    'Gagal menyimpan data',
                                            );
                                        return data;
                                    })
                                    .then((data) => {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text:
                                                data.message ||
                                                'Jadwal berhasil diperbarui.',
                                            timer: 1500,
                                            showConfirmButton: false,
                                            customClass: { popup: 'rounded-4' },
                                        }).then(() => location.reload());
                                    })
                                    .catch((err) => {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text:
                                                err.message ||
                                                'Terjadi kesalahan saat menyimpan.',
                                            customClass: { popup: 'rounded-4' },
                                        });
                                    });
                            } else if (
                                result.dismiss === Swal.DismissReason.cancel
                            ) {
                                // Buka lagi modal
                                if (!modalInstance) {
                                    modalInstance = new bootstrap.Modal(
                                        modalEl,
                                    );
                                }
                                modalInstance.show();

                                // SET FORM KE MODE EDIT (seperti tombol Edit ditekan)
                                const inputs =
                                    currentForm.querySelectorAll('input[name]');
                                const saveBtn =
                                    currentForm.querySelector('.save-btn');
                                const editBtn =
                                    currentForm.querySelector(
                                        '.toggle-edit-btn',
                                    );
                                const addDetailBtn =
                                    currentForm.querySelector(
                                        '.add-detail-btn',
                                    );
                                const wrapper = currentForm.querySelector(
                                    '.detail-produk-wrapper',
                                );
                                const tanggalInspeksiInput =
                                    currentForm.querySelector(
                                        `#tanggalInspeksi-${currentForm.dataset.formIndex}`,
                                    );

                                // Buka semua input kecuali 'nama_petugas' dan 'portofolio'
                                inputs.forEach((input) => {
                                    const name = input.getAttribute('name');
                                    if (
                                        name !== 'nama_petugas' &&
                                        name !== 'portofolio'
                                    ) {
                                        input.removeAttribute('readonly');
                                    }
                                });

                                // Set minimal tanggal inspeksi hari ini (atau tanggal sebelumnya jika sudah ada)
                                if (tanggalInspeksiInput) {
                                    const now = new Date()
                                        .toISOString()
                                        .slice(0, 10);
                                    const currentVal =
                                        tanggalInspeksiInput.value || now;
                                    tanggalInspeksiInput.removeAttribute(
                                        'readonly',
                                    );
                                    tanggalInspeksiInput.setAttribute(
                                        'min',
                                        now,
                                    );
                                    if (!tanggalInspeksiInput.value) {
                                        tanggalInspeksiInput.value = currentVal;
                                    }
                                }

                                // Show save button, hide edit button
                                if (saveBtn) {
                                    saveBtn.classList.remove('d-none');
                                    saveBtn.disabled = false;
                                }
                                if (editBtn) {
                                    editBtn.classList.add('d-none');
                                }

                                // Show add detail button
                                if (addDetailBtn) {
                                    addDetailBtn.classList.remove('d-none');
                                    addDetailBtn.style.display = 'inline-flex';

                                    // Bind add detail btn event if not yet bound
                                    if (!addDetailBtn.dataset.bound) {
                                        addDetailBtn.addEventListener(
                                            'click',
                                            function () {
                                                const group =
                                                    document.createElement(
                                                        'div',
                                                    );
                                                group.className =
                                                    'input-group mb-2 detail-produk-group';
                                                group.innerHTML = `
                                    <input type="text" name="detail_produk[]" class="form-control" required />
                                    <button type="button" class="btn btn-outline-danger remove-detail-btn">
                                        <i class="ti ti-x"></i>
                                    </button>
                                `;
                                                wrapper.appendChild(group);
                                                saveBtn.disabled = false;

                                                group
                                                    .querySelector(
                                                        '.remove-detail-btn',
                                                    )
                                                    .addEventListener(
                                                        'click',
                                                        function () {
                                                            group.remove();
                                                            saveBtn.disabled = false;
                                                            // Update remove buttons logic if any
                                                        },
                                                    );
                                            },
                                        );
                                        addDetailBtn.dataset.bound = 'true';
                                    }
                                }

                                // Setup remove buttons visible if more than one detail
                                const updateRemoveButtons = () => {
                                    const groups = wrapper.querySelectorAll(
                                        '.detail-produk-group',
                                    );
                                    groups.forEach((group) => {
                                        const removeBtn =
                                            group.querySelector(
                                                '.remove-detail-btn',
                                            );
                                        if (groups.length > 1) {
                                            removeBtn.style.display =
                                                'inline-block';
                                            removeBtn.disabled = false;
                                        } else {
                                            removeBtn.style.display = 'none';
                                            removeBtn.disabled = true;
                                        }
                                    });
                                };
                                updateRemoveButtons();
                            }
                        });
                    },
                );
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/detail_schedule_modal.blade.php ENDPATH**/ ?>