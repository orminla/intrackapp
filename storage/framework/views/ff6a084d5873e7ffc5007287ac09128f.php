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

<!-- Modal Profil -->
<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
    aria-labelledby="profileModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <div class="modal-content rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="profileModalLabel">Profil Saya</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>

            <div class="modal-body pt-2 mt-2">
                <div class="text-center mb-3">
                    <img
                        src="<?php echo e($profile["photo_url"]); ?>"
                        class="rounded-circle mb-2 object-fit-cover"
                        width="80"
                        height="80"
                        alt="Foto Profil"
                    />
                    <h5 class="fw-bold mb-0"><?php echo e($profile["name"]); ?></h5>
                    <small class="text-muted text-capitalize">
                        <?php echo e(ucfirst($profile["role"])); ?>

                    </small>
                </div>

                <hr />
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">NIP</strong>
                    <span>: <?php echo e($profile["nip"] ?? "-"); ?></span>
                </div>
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">Email</strong>
                    <span>: <?php echo e($profile["email"] ?? "-"); ?></span>
                </div>
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">
                        No. Telepon
                    </strong>
                    <span>: <?php echo e($profile["phone_num"] ?? "-"); ?></span>
                </div>
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">
                        Bidang
                    </strong>
                    <span>: <?php echo e($profile["department"] ?? "-"); ?></span>
                </div>
                <div class="d-flex">
                    <strong class="me-2" style="min-width: 110px">
                        Portofolio
                    </strong>
                    <span>: <?php echo e($profile["portfolio"] ?? "-"); ?></span>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 mt-2">
                <button
                    type="button"
                    class="btn btn-primary w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#editProfileModal"
                    data-bs-dismiss="modal"
                >
                    <i class="ti ti-pencil me-2"></i>
                    Edit Profil
                </button>

                <button
                    type="button"
                    class="btn btn-outline-primary w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#resetPasswordModal"
                    data-bs-dismiss="modal"
                >
                    <i class="ti ti-lock me-2"></i>
                    Ganti Password
                </button>
            </div>
        </div>
    </div>
</div>

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
            <form
                method="POST"
                action="<?php echo e(route("profile.update", auth()->id())); ?>"
                enctype="multipart/form-data"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field("PUT"); ?>

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

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control <?php $__errorArgs = ["name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old("name", $profile["name"] ?? "")); ?>"
                        />
                        <?php $__errorArgs = ["name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control <?php $__errorArgs = ["email"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old("email", $profile["email"] ?? "")); ?>"
                        />
                        <?php $__errorArgs = ["email"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="phone_num" class="form-label">
                            No. Telepon
                        </label>
                        <input
                            type="text"
                            name="phone_num"
                            id="phone_num"
                            class="form-control <?php $__errorArgs = ["phone_num"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old("phone_num", $profile["phone_num"] ?? "")); ?>"
                        />
                        <?php $__errorArgs = ["phone_num"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                            class="form-control <?php $__errorArgs = ["photo_url"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            onchange="previewPhoto(event)"
                        />
                        <?php $__errorArgs = ["photo_url"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

<!-- Modal Ubah Password -->
<div
    class="modal fade"
    id="resetPasswordModal"
    tabindex="-1"
    aria-labelledby="resetPasswordModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5
                    class="modal-title fw-semibold mt-2"
                    id="resetPasswordModalLabel"
                >
                    Ubah Password
                </h5>
                <button
                    type="button"
                    class="btn-close shadow-none"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form
                action="<?php echo e(route("profile.change-password")); ?>"
                method="POST"
            >
                <?php echo csrf_field(); ?>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            Password Lama
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="current_password"
                            name="current_password"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            Password Baru
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="new_password"
                            name="new_password"
                            required
                        />
                    </div>
                    <div class="mb-2">
                        <label
                            for="new_password_confirmation"
                            class="form-label"
                        >
                            Konfirmasi Password Baru
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            required
                        />
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pt-0 mb-2">
                    <button type="submit" class="btn btn-success w-100">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
</script>

<script>
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
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/profile_modal.blade.php ENDPATH**/ ?>