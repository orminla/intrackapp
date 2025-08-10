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
                        <strong><?php echo e($latest["Mitra"] ?? "-"); ?></strong>
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

                const reason = document.getElementById('reason').value;
                const scheduleId = document.querySelector(
                    "input[name='schedule_id']",
                ).value;

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
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload untuk menghindari pengiriman ganda
                        } else {
                            alert(data.message || 'Gagal mengirim permintaan.');
                        }
                    })
                    .catch(() => {
                        alert('Terjadi kesalahan saat mengirim permintaan.');
                    });
            });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/change_officer_modal.blade.php ENDPATH**/ ?>