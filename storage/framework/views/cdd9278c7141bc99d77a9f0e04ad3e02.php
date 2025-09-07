<?php $__env->startPush("style"); ?>
    <style>
        .certification-card {
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                color 0.25s ease,
                box-shadow 0.25s ease;
            cursor: pointer;
            box-sizing: border-box;
            word-wrap: break-word;
        }

        .certification-card:hover {
            background-color: #e2ebff !important;
            border-color: #e2ebff !important;
            color: #1e4db7;
        }

        .certification-card .small {
            display: block;
            width: 100%;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            word-break: break-word;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__currentLoopData = $inspectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inspector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="updateModal-<?php echo e($inspector["nip"]); ?>"
        tabindex="-1"
        aria-labelledby="updateModalLabel-<?php echo e($inspector["nip"]); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3">
                <form
                    action="<?php echo e(route("admin.petugas.update", $inspector["nip"])); ?>"
                    method="POST"
                >
                    <?php echo csrf_field(); ?>
                    <?php echo method_field("PUT"); ?>

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4
                            class="modal-title fw-bold"
                            id="updateModalLabel-<?php echo e($inspector["nip"]); ?>"
                        >
                            Edit Data Petugas
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    value="<?php echo e($inspector["name"]); ?>"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="nip"
                                    value="<?php echo e($inspector["nip"]); ?>"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select
                                    name="gender"
                                    class="form-select"
                                    disabled
                                    required
                                >
                                    <option value="">Pilih</option>
                                    <option
                                        value="Laki-laki"
                                        <?php echo e(($inspector["gender"] ?? "") == "Laki-laki" ? "selected" : ""); ?>

                                    >
                                        Laki-laki
                                    </option>
                                    <option
                                        value="Perempuan"
                                        <?php echo e(($inspector["gender"] ?? "") == "Perempuan" ? "selected" : ""); ?>

                                    >
                                        Perempuan
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    value="<?php echo e($inspector["email"]); ?>"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="phone_num"
                                    value="<?php echo e($inspector["phone_num"]); ?>"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bidang</label>
                                <select
                                    name="department_id"
                                    class="form-select department-select"
                                    data-target="portfolio-select-<?php echo e($inspector["nip"]); ?>"
                                    disabled
                                    required
                                >
                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option
                                            value="<?php echo e($dept->department_id); ?>"
                                            <?php echo e($dept->name == $inspector["department"] ? "selected" : ""); ?>

                                        >
                                            <?php echo e($dept->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Portofolio</label>
                                <select
                                    name="portfolio_id"
                                    id="portfolio-select-<?php echo e($inspector["nip"]); ?>"
                                    class="form-select portfolio-select"
                                    disabled
                                    required
                                >
                                    <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($portfolio->department->name == $inspector["department"]): ?>
                                            <option
                                                value="<?php echo e($portfolio->portfolio_id); ?>"
                                                <?php echo e($portfolio->portfolio_id == $inspector["portfolio_id"] ? "selected" : ""); ?>

                                            >
                                                <?php echo e($portfolio->name); ?>

                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Sertifikasi</label>
                                <div
                                    class="certification-list"
                                    style="max-height: 200px; overflow-y: auto"
                                >
                                    <?php if(isset($inspector["sertifikasi"]) && $inspector["sertifikasi"]->isNotEmpty()): ?>
                                        <?php $__currentLoopData = $inspector["sertifikasi"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $certUrl = $cert->file_path ? asset("storage/" . $cert->file_path) : null;
                                                $fullPortfolioName = $cert->portfolio->name ?? "-";
                                                $portfolioParts = explode("-", $fullPortfolioName, 2);
                                                $portfolioName = $portfolioParts[1] ?? $fullPortfolioName;
                                            ?>

                                            <div
                                                class="d-flex align-items-start mb-2"
                                            >
                                                <div
                                                    class="certification-card flex-grow-1"
                                                >
                                                    <div class="fw-bold mb-1">
                                                        <?php echo e($cert->name); ?>

                                                        <span
                                                            class="text-muted"
                                                        >
                                                            &nbsp;&ndash;&nbsp;<?php echo e($portfolioName); ?>

                                                        </span>
                                                    </div>
                                                    <div
                                                        class="small text-muted"
                                                    >
                                                        Diterbitkan oleh
                                                        <?php echo e($cert->issuer ?? "-"); ?>

                                                        | Berlaku:
                                                        <?php echo e($cert->issued_at ? \Carbon\Carbon::parse($cert->issued_at)->format("d M Y") : "-"); ?>

                                                        hingga
                                                        <?php echo e($cert->expired_at ? \Carbon\Carbon::parse($cert->expired_at)->format("d M Y") : "selamanya"); ?>

                                                    </div>
                                                </div>
                                                <div
                                                    class="d-flex flex-column ms-2 gap-1"
                                                >
                                                    <?php if($certUrl): ?>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-primary view-cert-btn"
                                                            data-url="<?php echo e($certUrl); ?>"
                                                            title="Lihat File"
                                                        >
                                                            <i
                                                                class="ti ti-eye"
                                                            ></i>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger remove-cert-btn"
                                                            data-cert-id="<?php echo e($cert->certification_id); ?>"
                                                            title="Hapus Sertifikasi"
                                                            disabled
                                                        >
                                                            <i
                                                                class="ti ti-trash"
                                                            ></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div class="text-muted">
                                            Belum ada sertifikasi
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 mt-2">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button
                                type="button"
                                class="btn btn-primary w-100"
                                id="editSaveBtn-<?php echo e($inspector["nip"]); ?>"
                            >
                                Edit Profil
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        const allPortfolios = <?php echo json_encode($portfolios, 15, 512) ?>;

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal').forEach((modalEdit) => {
                if (!modalEdit.id.startsWith('updateModal-')) return;

                const btn = modalEdit.querySelector('[id^="editSaveBtn-"]');
                const form = modalEdit.querySelector('form');
                const inputs = modalEdit.querySelectorAll('input, select');
                const removeBtns =
                    modalEdit.querySelectorAll('.remove-cert-btn');
                const viewBtns = modalEdit.querySelectorAll('.view-cert-btn');

                let isEditing = false;
                let deletedCerts = [];

                // Tombol Edit Profil / Simpan
                // bagian submit form
                btn?.addEventListener('click', () => {
                    if (!isEditing) {
                        inputs.forEach((el) => el.removeAttribute('disabled'));
                        removeBtns.forEach((el) =>
                            el.removeAttribute('disabled'),
                        );
                        viewBtns.forEach((el) =>
                            el.removeAttribute('disabled'),
                        );
                        btn.textContent = 'Simpan';
                        btn.classList.replace('btn-primary', 'btn-success');
                        isEditing = true;
                    } else {
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }

                        // Tambahkan sertifikasi yang dihapus
                        deletedCerts.forEach((id) => {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'deleted_certifications[]';
                            hiddenInput.value = id;
                            form.appendChild(hiddenInput);
                        });

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
                                        data.message ||
                                            'Gagal memperbarui data',
                                    );
                                return data;
                            })
                            .then((data) => {
                                // Tutup modal dulu
                                const bootstrapModal =
                                    bootstrap.Modal.getInstance(modalEdit);
                                bootstrapModal.hide();

                                // Setelah modal tertutup, tampilkan popup
                                modalEdit.addEventListener(
                                    'hidden.bs.modal',
                                    function handler() {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text:
                                                data.message ||
                                                'Data petugas berhasil diperbarui.',
                                            timer: 3000,
                                            showConfirmButton: false,
                                            customClass: {
                                                popup: 'rounded-4',
                                                confirmButton:
                                                    'btn btn-primary rounded-2 px-4',
                                                timerProgressBar: true,
                                            },
                                            buttonsStyling: false,
                                        });
                                        modalEdit.removeEventListener(
                                            'hidden.bs.modal',
                                            handler,
                                        );
                                        window.location.reload();
                                    },
                                );
                            })
                            .catch((error) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text:
                                        error.message ||
                                        'Terjadi kesalahan, silakan coba lagi.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                        timerProgressBar: true,
                                    },
                                    buttonsStyling: false,
                                });
                            });
                    }
                });

                // Dropdown Bidang -> Portofolio dinamis
                modalEdit
                    .querySelectorAll('.department-select')
                    .forEach((select) => {
                        select.addEventListener('change', function () {
                            const deptId = this.value;
                            const targetId = this.dataset.target;
                            const targetSelect =
                                document.getElementById(targetId);
                            if (targetSelect) {
                                targetSelect.innerHTML = '';
                                allPortfolios.forEach((portfolio) => {
                                    if (
                                        portfolio.department.department_id ==
                                        deptId
                                    ) {
                                        const opt =
                                            document.createElement('option');
                                        opt.value = portfolio.portfolio_id;
                                        opt.textContent = `${portfolio.name} (${portfolio.department.name})`;
                                        targetSelect.appendChild(opt);
                                    }
                                });
                            }
                        });
                    });

                // Reset saat modal ditutup
                modalEdit.addEventListener('hidden.bs.modal', () => {
                    inputs.forEach((el) => el.setAttribute('disabled', true));
                    removeBtns.forEach((el) =>
                        el.setAttribute('disabled', true),
                    );
                    viewBtns.forEach((el) => el.removeAttribute('disabled')); // lihat tetap aktif
                    btn.textContent = 'Edit Profil';
                    btn.classList.replace('btn-success', 'btn-primary');
                    isEditing = false;
                    deletedCerts = [];
                });

                // Tombol lihat & hapus sertifikasi
                modalEdit.addEventListener('click', function (e) {
                    // Lihat file
                    if (e.target.closest('.view-cert-btn')) {
                        const btn = e.target.closest('.view-cert-btn');
                        const url = btn.dataset.url;
                        if (url) window.open(url, '_blank');
                    }

                    // Hapus sertifikasi hanya saat edit
                    if (isEditing && e.target.closest('.remove-cert-btn')) {
                        const btn = e.target.closest('.remove-cert-btn');
                        const cardWrapper = btn.closest(
                            '.d-flex.align-items-start.mb-2',
                        );
                        if (!cardWrapper) return;

                        cardWrapper.remove(); // Hapus dari UI
                        deletedCerts.push(btn.dataset.certId); // Simpan untuk dikirim
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/edit_inspector_modal.blade.php ENDPATH**/ ?>