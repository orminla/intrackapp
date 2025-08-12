<!-- Modal: Forgot Password -->
<div
    class="modal fade"
    id="forgotPasswordModal"
    tabindex="-1"
    aria-labelledby="forgotPasswordLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <form id="forgotPasswordForm" class="modal-content p-4">
            <?php echo csrf_field(); ?>
            <div class="modal-header border-0">
                <h5 class="modal-title" id="forgotPasswordLabel">
                    Reset Password via WhatsApp
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    Masukkan nomor WhatsApp Anda (format 628xxxx):
                </p>
                <input
                    type="text"
                    class="form-control"
                    id="phoneNumInput"
                    name="phone_num"
                    placeholder="628xxxx..."
                    required
                />
                <div
                    id="otpError"
                    class="text-danger mt-2"
                    style="display: none"
                ></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100">
                    Kirim Kode OTP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document
        .getElementById('forgotPasswordForm')
        .addEventListener('submit', function (e) {
            e.preventDefault();

            const phone = document.getElementById('phoneNumInput').value;
            const errorDiv = document.getElementById('otpError');
            errorDiv.style.display = 'none';

            fetch('/forgot-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: JSON.stringify({ phone_num: phone }),
            })
                .then((response) => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then((data) => {
                    window.lastUserId = data.user_id;

                    // Tutup modal kirim OTP
                    const forgotModal = bootstrap.Modal.getInstance(
                        document.getElementById('forgotPasswordModal'),
                    );
                    forgotModal.hide();

                    // Set nomor telepon di modal OTP
                    document.getElementById('otpPhoneNum').value = phone;

                    // Tampilkan modal OTP
                    const otpModal = new bootstrap.Modal(
                        document.getElementById('otpModal'),
                    );
                    otpModal.show();
                });
        });
</script>

<!-- Modal OTP -->
<div
    class="modal fade"
    id="otpModal"
    tabindex="-1"
    aria-labelledby="otpModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <form id="otpForm" class="modal-content p-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="phone_num" id="otpPhoneNum" />

            <div class="modal-header border-0">
                <h5 class="modal-title" id="otpModalLabel">
                    Masukkan Kode OTP
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>

            <div class="modal-body text-center">
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <?php for($i = 0; $i < 6; $i++): ?>
                        <input
                            type="text"
                            class="form-control text-center otp-input"
                            maxlength="1"
                            inputmode="numeric"
                            pattern="\d*"
                            style="
                                width: 60px;
                                height: 70px;
                                font-size: 2rem;
                                border-radius: 10px;
                            "
                            required
                        />
                    <?php endfor; ?>
                </div>
                <small class="text-muted">
                    Kode OTP telah dikirim ke WhatsApp Anda.
                </small>
            </div>

            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100">
                    Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const otpInputs = document.querySelectorAll('.otp-input');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        document
            .getElementById('otpForm')
            .addEventListener('submit', function (e) {
                e.preventDefault();

                const otpCode = Array.from(otpInputs)
                    .map((input) => input.value.trim())
                    .join('');

                if (otpCode.length !== 6) {
                    alert('Kode OTP harus 6 digit.');
                    return;
                }

                const phoneNum = document.getElementById('otpPhoneNum').value;

                fetch('/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    },
                    body: JSON.stringify({
                        token: otpCode,
                        phone_num: phoneNum,
                        user_id: window.lastUserId,
                    }),
                })
                    .then((res) => {
                        if (!res.ok) throw res;
                        return res.json();
                    })
                    .then((data) => {
                        const otpModal = bootstrap.Modal.getInstance(
                            document.getElementById('otpModal'),
                        );
                        otpModal.hide();

                        document.getElementById('resetUserIdInput').value =
                            data.user_id;
                        document.getElementById('resetOtpTokenInput').value =
                            data.token;

                        const resetModal = new bootstrap.Modal(
                            document.getElementById('resetPasswordModal'),
                        );
                        resetModal.show();
                    })
                    .catch(async (err) => {
                        let message = 'Kode OTP salah atau kedaluwarsa.';
                        if (err.json) {
                            const errJson = await err.json();
                            message = errJson.message || message;
                        }
                        alert(message);
                    });
            });
    });
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/auth/forget_modal.blade.php ENDPATH**/ ?>