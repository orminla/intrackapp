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

<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(strtolower($item["status"]) === "menunggu konfirmasi"): ?>
        <!-- Modal Tolak Laporan -->
        <div
            class="modal fade"
            id="modalTolak-<?php echo e($item["id"]); ?>"
            tabindex="-1"
            aria-labelledby="modalTolakLabel-<?php echo e($item["id"]); ?>"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered custom-modal-width">
                <div class="modal-content rounded-4 p-3">
                    <!-- Header -->
                    <div class="modal-header border-0 pb-0">
                        <h4
                            class="modal-title fw-semibold"
                            id="modalTolakLabel-<?php echo e($item["id"]); ?>"
                        >
                            Tolak Laporan
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Tutup"
                        ></button>
                    </div>

                    <!-- Form -->
                    <form
                        method="POST"
                        action="<?php echo e(route("admin.laporan.validasi", $item["id"])); ?>"
                    >
                        <?php echo csrf_field(); ?>
                        <?php echo method_field("PUT"); ?>
                        <input type="hidden" name="status" value="Ditolak" />

                        <!-- Body -->
                        <div class="modal-body pt-2">
                            <p class="mb-4">
                                Apakah Anda yakin ingin menolak laporan inspeksi
                                dari
                                <strong><?php echo e($item["nama_mitra"]); ?></strong>
                                ?
                            </p>

                            <div>
                                <label
                                    for="alasan-<?php echo e($item["id"]); ?>"
                                    class="fw-semibold mb-1"
                                >
                                    Alasan Penolakan
                                </label>
                                <textarea
                                    id="alasan-<?php echo e($item["id"]); ?>"
                                    name="alasan"
                                    class="form-control rounded-3"
                                    placeholder="Contoh: Dokumen tidak lengkap atau alasan lainnya"
                                    rows="3"
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="modal-footer border-0 pt-0 mt-2 justify-content-end"
                        >
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/rejected_modal.blade.php ENDPATH**/ ?>