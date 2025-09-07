<?php echo $__env->make(
    "add_certification_modal",
    [
        "allPortfolios" => $allPortfolios ?? collect([]),
        "profile" => $profile ?? [],
    ]
, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Modal Edit Profil -->
<div
    class="modal fade"
    id="editProfileModal"
    tabindex="-1"
    aria-labelledby="editProfileModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <div class="modal-content rounded-4 p-3">
            <form id="formEditProfile" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field("PUT"); ?>

                <input
                    type="hidden"
                    name="inspector_id"
                    value="<?php echo e($profile["inspector_id"] ?? ""); ?>"
                />

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="editProfileModalLabel">
                        Edit Profil
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body pt-2 mt-2">
                    
                    <div class="mb-3 text-center">
                        <img
                            id="photoPreview"
                            src="<?php echo e($profile["photo_url"] ?? asset("login_assets/images/profile/user-7.jpg")); ?>"
                            class="rounded-circle mb-2 object-fit-cover"
                            width="80"
                            height="80"
                            alt="Preview Foto"
                        />
                    </div>

                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="<?php echo e(old("name", $profile["name"] ?? "")); ?>"
                            />
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label">
                                Jenis Kelamin
                            </label>
                            <select
                                name="gender"
                                id="gender"
                                class="form-select"
                            >
                                <option value="">Pilih</option>
                                <option
                                    value="Laki-laki"
                                    <?php echo e(old("gender", $profile["gender"]) == "Laki-laki" ? "selected" : ""); ?>

                                >
                                    Laki-laki
                                </option>
                                <option
                                    value="Perempuan"
                                    <?php echo e(old("gender", $profile["gender"]) == "Perempuan" ? "selected" : ""); ?>

                                >
                                    Perempuan
                                </option>
                            </select>
                            <div
                                class="invalid-feedback"
                                id="error-gender"
                            ></div>
                        </div>
                    </div>

                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="<?php echo e(old("email", $profile["email"] ?? "")); ?>"
                            />
                            <div
                                class="invalid-feedback"
                                id="error-email"
                            ></div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone_num" class="form-label">
                                No. Telepon
                            </label>
                            <input
                                type="text"
                                name="phone_num"
                                id="phone_num"
                                class="form-control"
                                value="<?php echo e(old("phone_num", $profile["phone_num"] ?? "")); ?>"
                            />
                            <div
                                class="invalid-feedback"
                                id="error-phone_num"
                            ></div>
                        </div>
                    </div>

                    
                    <div class="mb-3">
                        <label for="photo_url" class="form-label">
                            Foto Profil (Upload)
                        </label>
                        <input
                            type="file"
                            name="photo_url"
                            id="photo_url"
                            accept="image/*"
                            class="form-control"
                            onchange="previewPhoto(event)"
                        />
                        <div
                            class="invalid-feedback"
                            id="error-photo_url"
                        ></div>
                    </div>

                    
                    <div class="mb-4 mt-2">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <label class="form-label mb-2">Sertifikasi</label>
                            <button
                                type="button"
                                id="addCertBtn"
                                class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#tambahCertifModal"
                            >
                                <i class="ti ti-plus me-1"></i>
                                Tambah
                            </button>
                        </div>

                        <div
                            class="certification-list mt-2"
                            style="max-height: 250px; overflow-y: auto"
                        >
                            <?php if(! empty($profile["certifications"])): ?>
                                <?php $__currentLoopData = $profile["certifications"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $certUrl = $cert->file_path ? asset("storage/" . $cert->file_path) : null;
                                        $portfolioName = $cert->portfolio->name ?? "-";
                                    ?>

                                    <div class="d-flex align-items-start mb-2">
                                        <div
                                            class="certification-card flex-grow-1"
                                        >
                                            <div class="fw-bold mb-1">
                                                <?php echo e($cert->name); ?>

                                                <span class="text-muted">
                                                    &nbsp;&ndash;&nbsp;<?php echo e($portfolioName); ?>

                                                </span>
                                            </div>
                                            <div class="small text-muted">
                                                Diterbitkan oleh
                                                <?php echo e($cert->issuer ?? "-"); ?> |
                                                Berlaku:
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
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                            <?php endif; ?>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger remove-cert-btn"
                                                data-cert-id="<?php echo e($cert->certification_id); ?>"
                                                title="Hapus Sertifikasi"
                                            >
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <div class="text-muted">
                                    Belum ada sertifikasi
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <input
                            type="hidden"
                            name="deleted_certifications[]"
                            id="deletedCertifications"
                        />

                        
                        <div id="newCertContainer" class="mt-2"></div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 mt-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-check me-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush("scripts"); ?>
    <script>
        // Preview photo upload (kalau input file ada di edit profile, pindahkan ke edit_profile_modal)
        function previewPhoto(event) {
            const input = event.target;
            const preview = document.getElementById('photoPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const formEdit = document.getElementById('formEditProfile');
            if (!formEdit) return;

            const editProfileModal =
                document.getElementById('editProfileModal');
            editProfileModal.addEventListener('hidden.bs.modal', function () {
                location.reload();
            });

            // Preview foto
            window.previewPhoto = function (event) {
                const preview = document.getElementById('photoPreview');
                if (event.target.files && event.target.files[0]) {
                    preview.src = URL.createObjectURL(event.target.files[0]);
                }
            };

            // Hapus sertifikasi
            document.querySelectorAll('.remove-cert-btn').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const certId = this.dataset.certId;

                    // bikin input hidden baru untuk sertif yg dihapus
                    const deletedInput = document.createElement('input');
                    deletedInput.type = 'hidden';
                    deletedInput.name = 'deleted_certifications[]';
                    deletedInput.value = certId;
                    formEdit.appendChild(deletedInput);

                    // hapus tampilan card sertifikasi
                    this.closest('.d-flex.align-items-start').remove();
                });
            });

            // Lihat sertifikasi
            document.querySelectorAll('.view-cert-btn').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const url = this.dataset.url;
                    window.open(url, '_blank');
                });
            });

            // Submit AJAX Edit Profil
            formEdit.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const actionUrl = '<?php echo e(route("profile.update")); ?>';
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('editProfileModal'),
                );

                fetch(actionUrl, {
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
                        if (!response.ok) throw new Error(data.message);
                        return data;
                    })
                    .then((data) => {
                        if (modal) modal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Profil berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' },
                        });
                        setTimeout(() => location.reload(), 1600);
                    })
                    .catch((error) => {
                        if (modal) modal.hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text:
                                error.message ||
                                'Terjadi kesalahan, silakan periksa data.',
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-primary rounded-2 px-4',
                            },
                            buttonsStyling: false,
                        });
                    });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/edit_profile_modal.blade.php ENDPATH**/ ?>