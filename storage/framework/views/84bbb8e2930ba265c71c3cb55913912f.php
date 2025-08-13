<!-- Modal: Reset Password -->
<div
    class="modal fade"
    id="resetPasswordModal"
    tabindex="-1"
    aria-labelledby="resetPasswordLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content p-4" id="resetPasswordForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="user_id" id="resetUserIdInput" />
            <input type="hidden" name="token" id="resetOtpTokenInput" />

            <div class="modal-header border-0">
                <h5 class="modal-title" id="resetPasswordLabel">
                    Atur Ulang Password
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>

            <div class="modal-body">
                <div class="mb-2">
                    <label for="newPasswordInput" class="form-label">
                        Password Baru
                    </label>
                    <div class="position-relative">
                        <input
                            type="password"
                            class="form-control pe-5"
                            id="newPasswordInput"
                            name="password"
                            placeholder="Masukkan password baru"
                            required
                            minlength="6"
                        />
                        <iconify-icon
                            icon="mdi:eye-off"
                            id="toggleNewPassword"
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                            style="font-size: 1.25rem"
                        ></iconify-icon>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="confirmPasswordInput" class="form-label">
                        Konfirmasi Password
                    </label>
                    <div class="position-relative">
                        <input
                            type="password"
                            class="form-control pe-5"
                            id="confirmPasswordInput"
                            name="password_confirmation"
                            placeholder="Konfirmasi password"
                            required
                        />
                        <iconify-icon
                            icon="mdi:eye-off"
                            id="toggleConfirmPassword"
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                            style="font-size: 1.25rem"
                        ></iconify-icon>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Iconify (Web Component) -->
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('resetPasswordForm');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const password = formData.get('password');
            const confirmPassword = formData.get('password_confirmation');

            if (!password || password.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Password minimal 6 karakter.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Konfirmasi password tidak cocok.',
                    confirmButtonColor: '#d33',
                });
                return;
            }

            fetch('<?php echo e(route("password.update")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: formData,
            })
                .then(async (res) => {
                    if (!res.ok)
                        throw new Error('Respon gagal (' + res.status + ')');
                    const data = await res.json();
                    if (data.success) {
                        const resetModalEl =
                            document.getElementById('resetPasswordModal');
                        const resetModal =
                            bootstrap.Modal.getInstance(resetModalEl);
                        resetModal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Password berhasil diubah. Silakan login.',
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(() => {
                            window.location.href = '/login';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Gagal reset password.',
                            confirmButtonColor: '#d33',
                        });
                    }
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat reset password.',
                        confirmButtonColor: '#d33',
                    });
                });
        });

        // Toggle Password Visibility
        const toggleNew = document.getElementById('toggleNewPassword');
        const newInput = document.getElementById('newPasswordInput');
        const iconNew = toggleNew.querySelector('iconify-icon');

        toggleNew.addEventListener('click', function () {
            const type =
                newInput.getAttribute('type') === 'password'
                    ? 'text'
                    : 'password';
            newInput.setAttribute('type', type);
            iconNew.setAttribute(
                'icon',
                type === 'password' ? 'mdi:eye-off' : 'mdi:eye',
            );
        });

        const toggleConfirm = document.getElementById('toggleConfirmPassword');
        const confirmInput = document.getElementById('confirmPasswordInput');
        const iconConfirm = toggleConfirm.querySelector('iconify-icon');

        toggleConfirm.addEventListener('click', function () {
            const type =
                confirmInput.getAttribute('type') === 'password'
                    ? 'text'
                    : 'password';
            confirmInput.setAttribute('type', type);
            iconConfirm.setAttribute(
                'icon',
                type === 'password' ? 'mdi:eye-off' : 'mdi:eye',
            );
        });
    });
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/auth/reset_modal.blade.php ENDPATH**/ ?>