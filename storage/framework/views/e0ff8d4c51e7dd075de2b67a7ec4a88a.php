
<?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="updateModal-<?php echo e($admin["nip"]); ?>"
        tabindex="-1"
        aria-labelledby="updateModalLabel-<?php echo e($admin["nip"]); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3">
                <form
                    action="<?php echo e(route("admin.pengaturan.update", $admin["nip"])); ?>"
                    method="POST"
                >
                    <?php echo csrf_field(); ?>
                    <?php echo method_field("PUT"); ?>

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4
                            class="modal-title fw-bold"
                            id="updateModalLabel-<?php echo e($admin["nip"]); ?>"
                        >
                            Edit Data Admin
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Tutup"
                        ></button>
                    </div>

                    <div class="modal-body pt-2 mt-2 text-start">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    value="<?php echo e($admin["name"]); ?>"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="nip"
                                    value="<?php echo e($admin["nip"]); ?>"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    value="<?php echo e($admin["email"]); ?>"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="phone_num"
                                    value="<?php echo e($admin["phone_num"]); ?>"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select
                                    name="gender"
                                    class="form-select"
                                    required
                                    disabled
                                >
                                    <option value="">Pilih</option>
                                    <option
                                        value="Laki-laki"
                                        <?php echo e(($admin["gender"] ?? "") == "Laki-laki" ? "selected" : ""); ?>

                                    >
                                        Laki-laki
                                    </option>
                                    <option
                                        value="Perempuan"
                                        <?php echo e(($admin["gender"] ?? "") == "Perempuan" ? "selected" : ""); ?>

                                    >
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Portofolio</label>
                                <select
                                    name="portfolio_id"
                                    class="form-select"
                                    required
                                    disabled
                                >
                                    <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option
                                            value="<?php echo e($portfolio->portfolio_id); ?>"
                                            <?php echo e($portfolio->portfolio_id == $admin["portfolio_id"] ? "selected" : ""); ?>

                                        >
                                            <?php echo e($portfolio->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 mt-2">
                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            id="editSaveBtn-<?php echo e($admin["nip"]); ?>"
                        >
                            Edit Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal').forEach((modalEdit) => {
                if (!modalEdit.id.startsWith('updateModal-')) return;

                const btn = modalEdit.querySelector('[id^="editSaveBtn-"]');
                const form = modalEdit.querySelector('form');
                const inputs = modalEdit.querySelectorAll('input, select');

                let isEditing = false;

                btn?.addEventListener('click', () => {
                    if (!isEditing) {
                        inputs.forEach((el) => el.removeAttribute('disabled'));
                        btn.textContent = 'Simpan';
                        btn.classList.replace('btn-primary', 'btn-success');
                        isEditing = true;
                    } else {
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
                                        data.message ||
                                            'Gagal memperbarui data',
                                    );
                                return data;
                            })
                            .then((data) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text:
                                        data.message ||
                                        'Data admin berhasil diperbarui.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: { popup: 'rounded-4' },
                                });

                                const modalInstance =
                                    bootstrap.Modal.getInstance(modalEdit);
                                if (modalInstance) modalInstance.hide();
                                setTimeout(
                                    () => window.location.reload(),
                                    1600,
                                );
                            })
                            .catch((error) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text:
                                        error.message ||
                                        'Terjadi kesalahan, silakan coba lagi.',
                                    confirmButtonText: 'OK',
                                    customClass: { popup: 'rounded-4' },
                                });
                            });
                    }
                });

                // Reset saat modal ditutup
                modalEdit.addEventListener('hidden.bs.modal', () => {
                    inputs.forEach((el) => el.setAttribute('disabled', true));
                    btn.textContent = 'Edit Profil';
                    btn.classList.replace('btn-success', 'btn-primary');
                    isEditing = false;
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/edit_admin_modal.blade.php ENDPATH**/ ?>