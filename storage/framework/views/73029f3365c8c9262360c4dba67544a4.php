<style>
    /* ================================
       Change Inspector Modal Specific
       ================================ */
    #changeInspectorModal .custom-modal-width {
        max-width: 85%;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 768px) {
        #changeInspectorModal .custom-modal-width {
            max-width: 480px;
        }
    }

    /* Optional: Styling form fields inside changeInspectorModal */
    #changeInspectorModal .form-group label {
        font-weight: 600;
    }

    #changeInspectorModal textarea {
        resize: none; /* mencegah resize manual */
    }

    #changeInspectorModal .modal-footer button {
        min-width: 100px; /* agar tombol tidak terlalu kecil */
    }
</style>

<!-- Modal Ajukan Ganti Petugas -->
<div
    class="modal fade"
    id="changeInspectorModal"
    tabindex="-1"
    aria-labelledby="changeInspectorModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <form
            id="changeInspectorForm"
            method="POST"
            action="<?php echo e(route("inspector.change-request")); ?>"
        >
            <?php echo csrf_field(); ?>
            <div class="modal-content rounded-4 p-3">
                <div class="modal-header border-0 pb-0">
                    <h4
                        class="modal-title fw-semibold"
                        id="changeInspectorModalLabel"
                    >
                        Ajukan Ganti Petugas
                    </h4>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body pt-2">
                    <p class="mb-4">
                        Apakah Anda yakin ingin mengajukan pergantian petugas
                        untuk inspeksi
                        <strong><?php echo e($latest["Mitra"] ?? ""); ?></strong>
                        ?
                    </p>

                    <div class="form-group mb-3">
                        <label for="reason" class="fw-semibold mb-1">
                            Alasan Penggantian
                        </label>
                        <textarea
                            id="reason"
                            name="reason"
                            class="form-control rounded-2"
                            placeholder="Contoh: Jadwal berbenturan atau alasan lainnya"
                            rows="4"
                            required
                        ></textarea>
                    </div>

                    <input
                        type="hidden"
                        name="schedule_id"
                        value="<?php echo e($latest["schedule_id"] ?? ""); ?>"
                    />
                </div>

                <div class="modal-footer border-0 pt-0 justify-content-end">
                    <button
                        type="button"
                        class="btn btn-danger"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">Kirim</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush("scripts"); ?>
    <script>
        document
            .getElementById('changeInspectorForm')
            .addEventListener('submit', function (e) {
                e.preventDefault();

                const reasonInput = document.getElementById('reason');
                const reason = reasonInput.value.trim();
                const scheduleId = document.querySelector(
                    "input[name='schedule_id']",
                ).value;
                const modalEl = document.getElementById('changeInspectorModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);

                if (!reason) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Alasan penggantian wajib diisi.',
                        customClass: { popup: 'rounded-4' },
                        buttonsStyling: false,
                    });
                    return;
                }

                if (modalInstance) modalInstance.hide(); // tutup modal sementara

                fetch('<?php echo e(route("inspector.change-request")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        reason: reason,
                        schedule_id: scheduleId,
                    }),
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text:
                                    data.message ||
                                    'Permintaan ganti petugas berhasil dikirim.',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-4' },
                                buttonsStyling: false,
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text:
                                    data.message ||
                                    'Terjadi kesalahan saat mengirim permintaan.',
                                showConfirmButton: true,
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-primary rounded-2 px-4',
                                },
                                buttonsStyling: false,
                                preConfirm: () => {
                                    const m = new bootstrap.Modal(modalEl);
                                    m.show();
                                },
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat mengirim permintaan.',
                            showConfirmButton: true,
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-primary rounded-2 px-4',
                            },
                            buttonsStyling: false,
                            preConfirm: () => {
                                const m = new bootstrap.Modal(modalEl);
                                m.show();
                            },
                        });
                    });
            });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/change_officer_modal.blade.php ENDPATH**/ ?>