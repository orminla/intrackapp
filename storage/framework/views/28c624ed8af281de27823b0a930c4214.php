<?php $__env->startPush("styles"); ?>
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
<?php $__env->stopPush(); ?>

<div
    class="modal fade"
    id="detailScheduleModal-<?php echo e($index); ?>"
    tabindex="-1"
    aria-labelledby="detailScheduleModalLabel-<?php echo e($index); ?>"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
            <div
                class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
            >
                <h4
                    class="modal-title fw-bold"
                    id="detailScheduleModalLabel-<?php echo e($index); ?>"
                >
                    Detail Jadwal Inspeksi
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mitra</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?php echo e($schedule["mitra"]); ?>"
                            readonly
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alamat</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?php echo e($schedule["lokasi"]); ?>"
                            readonly
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input
                            type="date"
                            class="form-control"
                            value="<?php echo e($schedule["tanggal"]); ?>"
                            readonly
                        />
                    </div>

                    <?php if(! empty($schedule["tanggal_selesai"])): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input
                                type="date"
                                class="form-control"
                                value="<?php echo e($schedule["tanggal_selesai"]); ?>"
                                readonly
                            />
                        </div>
                    <?php endif; ?>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Produk</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?php echo e($schedule["produk"]); ?>"
                            readonly
                        />
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Detail Produk</label>
                        <?php if(! empty($schedule["detail_produk"]) && count($schedule["detail_produk"])): ?>
                            <?php $__currentLoopData = $schedule["detail_produk"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input
                                    type="text"
                                    class="form-control mb-2"
                                    value="<?php echo e($detail); ?>"
                                    readonly
                                />
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <input
                                type="text"
                                class="form-control text-muted"
                                value="-"
                                readonly
                            />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/detail_schedule_modal.blade.php ENDPATH**/ ?>