<?php $__env->startPush("styles"); ?>
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
        /* Tombol full lebar dalam footer */
        .btn-equal {
            flex: 1;
        }
        .modal-footer {
            display: flex;
            gap: 1rem;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $requestChange): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="changeInspectorModal-<?php echo e($i); ?>"
        tabindex="-1"
        aria-labelledby="changeInspectorModalLabel-<?php echo e($i); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
                <div
                    class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                >
                    <h4
                        class="modal-title fw-bold"
                        id="changeInspectorModalLabel-<?php echo e($i); ?>"
                    >
                        Ubah Permintaan Ganti Petugas
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
                        action="<?php echo e(route("admin.change_request.update")); ?>"
                        method="POST"
                        id="editChangeInsp-changeRequestForm-<?php echo e($requestChange["id"]); ?>"
                    >
                        <?php echo csrf_field(); ?>
                        <input
                            type="hidden"
                            name="change_request_id"
                            value="<?php echo e($requestChange["id"]); ?>"
                        />

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mitra</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($requestChange["mitra"]); ?>"
                                    readonly
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tanggal Pengajuan
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e(\Carbon\Carbon::parse($requestChange["requested_date"])->format("Y-m-d")); ?>"
                                    readonly
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Petugas Lama</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo e($requestChange["petugas"]); ?>"
                                    readonly
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Petugas Baru</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="editChangeInsp-inspectorDisplay-<?php echo e($requestChange["id"]); ?>"
                                        name="inspector_display"
                                        value="<?php echo e($requestChange["petugas_baru"] ?? "-"); ?>"
                                        readonly
                                        disabled
                                    />
                                    <input
                                        type="hidden"
                                        name="new_inspector_id"
                                        id="editChangeInsp-inspectorId-<?php echo e($requestChange["id"]); ?>"
                                        value="<?php echo e($requestChange["new_inspector_id"] ?? ""); ?>"
                                        disabled
                                    />

                                    <button
                                        type="button"
                                        class="btn btn-outline-success btn-equal"
                                        id="editChangeInsp-btnReloadPetugas-<?php echo e($requestChange["id"]); ?>"
                                        data-target="<?php echo e($requestChange["id"]); ?>"
                                        data-portfolio-id="<?php echo e($requestChange->schedule->inspector->portfolio_id ?? ""); ?>"
                                        data-started-date="<?php echo e($requestChange["requested_date"]); ?>"
                                        disabled
                                    >
                                        Reload
                                    </button>
                                </div>
                                <small
                                    class="text-muted d-block mt-1"
                                    id="editChangeInsp-inspectorQuota-<?php echo e($requestChange["id"]); ?>"
                                >
                                    Ketersediaan: -
                                </small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Alasan Penggantian
                                </label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    readonly
                                    disabled
                                >
<?php echo e($requestChange["reason"]); ?></textarea
                                >
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-equal"
                                id="editChangeInsp-btnEdit-<?php echo e($requestChange["id"]); ?>"
                            >
                                Edit Petugas
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary btn-equal"
                                id="editChangeInsp-btnSimpan-<?php echo e($requestChange["id"]); ?>"
                                disabled
                                style="display: none"
                            >
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        document.querySelectorAll('[id^=editChangeInsp-btnEdit-]').forEach((btnEdit) => {
                btnEdit.addEventListener('click', function () {
                    const id = this.id.replace('editChangeInsp-btnEdit-', '');
                    const form = document.getElementById(`editChangeInsp-changeRequestForm-${id}`);

                    // Simpan nilai awal ke dataset untuk reset nanti
                    const displayInput = document.getElementById(`editChangeInsp-inspectorDisplay-${id}`);
                    const hiddenInput = document.getElementById(`editChangeInsp-inspectorId-${id}`);
                    displayInput.dataset.initialValue = displayInput.value;
                    hiddenInput.dataset.initialValue = hiddenInput.value;

                    // Disable semua input kecuali petugas baru
                    form.querySelectorAll('input, textarea').forEach((el) => {
                        if (
                            el.id !== `editChangeInsp-inspectorDisplay-${id}` &&
                            el.id !== `editChangeInsp-inspectorId-${id}`
                        ) {
                            el.readOnly = true;
                            el.disabled = true;
                        }
                    });

                    // Aktifkan petugas baru (reload button dan input)
                    displayInput.readOnly = true; // tetap readonly supaya user tidak bisa ketik manual
                    displayInput.disabled = false;
                    hiddenInput.disabled = false;

                    const btnReload = document.getElementById(`editChangeInsp-btnReloadPetugas-${id}`);
                    btnReload.disabled = false;
                    btnReload.dataset.changeCount = 0;

                    const quotaText = document.getElementById(`editChangeInsp-inspectorQuota-${id}`);
                    quotaText.textContent = 'Ketersediaan: -';

                    // Toggle tombol
                    this.style.display = 'none';
                    const btnSimpan = document.getElementById(`editChangeInsp-btnSimpan-${id}`);
                    btnSimpan.style.display = 'inline-block';
                    btnSimpan.disabled = true; // akan enabled kalau reload sudah dijalankan
                });
            });

            document.querySelectorAll('[id^=editChangeInsp-btnReloadPetugas-]').forEach((btnReload) => {
                btnReload.addEventListener('click', function () {
                    const id = this.dataset.target;
                    const portfolioId = this.dataset.portfolioId;
                    const startedDate = this.dataset.startedDate;
                    const displayInput = document.getElementById(`editChangeInsp-inspectorDisplay-${id}`);
                    const hiddenInput = document.getElementById(`editChangeInsp-inspectorId-${id}`);
                    const quotaText = document.getElementById(`editChangeInsp-inspectorQuota-${id}`);
                    const btnSimpan = document.getElementById(`editChangeInsp-btnSimpan-${id}`);

                    let changeCount = parseInt(this.dataset.changeCount || '0');

                    if (changeCount >= 2) {
                        alert('Maksimal Reload petugas 2 kali sebelum menyimpan.');
                        return;
                    }

                    const originalBtnText = this.textContent;
                    this.textContent = 'Memuat...';
                    this.disabled = true;

                    fetch(`/admin/get-inspector?portfolio_id=${portfolioId}&started_date=${startedDate}`)
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.inspector_id && data.name) {
                                displayInput.value = data.name;
                                hiddenInput.value = data.inspector_id;
                                quotaText.textContent = `Ketersediaan: ${data.note ?? '-'}`;

                                changeCount++;
                                this.dataset.changeCount = changeCount;

                                if (changeCount > 0) {
                                    btnSimpan.disabled = false;
                                }
                            } else {
                                alert('Data petugas tidak ditemukan.');
                            }
                        })
                        .catch(() => {
                            alert('Terjadi kesalahan saat mengambil data petugas.');
                        })
                        .finally(() => {
                            this.textContent = originalBtnText;
                            this.disabled = false;
                        });
                });
            });

            // Reset modal saat ditutup
            <?php $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $requestChange): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                const modalEl<?php echo e($i); ?> = document.getElementById("changeInspectorModal-<?php echo e($i); ?>");
                modalEl<?php echo e($i); ?>.addEventListener('hidden.bs.modal', function (event) {
                    const displayInput = document.getElementById("editChangeInsp-inspectorDisplay-<?php echo e($requestChange['id']); ?>");
                    const hiddenInput = document.getElementById("editChangeInsp-inspectorId-<?php echo e($requestChange['id']); ?>");
                    const btnEdit = document.getElementById("editChangeInsp-btnEdit-<?php echo e($requestChange['id']); ?>");
                    const btnSimpan = document.getElementById("editChangeInsp-btnSimpan-<?php echo e($requestChange['id']); ?>");
                    const btnReload = document.getElementById("editChangeInsp-btnReloadPetugas-<?php echo e($requestChange['id']); ?>");
                    const quotaText = document.getElementById("editChangeInsp-inspectorQuota-<?php echo e($requestChange['id']); ?>");

                    if (displayInput.dataset.initialValue !== undefined) {
                        displayInput.value = displayInput.dataset.initialValue;
                    }
                    if (hiddenInput.dataset.initialValue !== undefined) {
                        hiddenInput.value = hiddenInput.dataset.initialValue;
                    }

                    displayInput.readOnly = true;
                    displayInput.disabled = true;
                    hiddenInput.disabled = true;

                    quotaText.textContent = 'Ketersediaan: -';

                    btnReload.disabled = true;
                    btnReload.dataset.changeCount = 0;
                    btnReload.textContent = 'Reload';

                    btnSimpan.style.display = 'none';
                    btnSimpan.disabled = true;
                    btnEdit.style.display = 'inline-block';
                });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/edit_changeinsp_modal.blade.php ENDPATH**/ ?>