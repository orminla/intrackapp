<?php $__env->startPush("styles"); ?>
    <style>
        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $requestChange): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div
        class="modal fade"
        id="viewChangeInspectorModal-<?php echo e($i); ?>"
        tabindex="-1"
        aria-labelledby="viewChangeInspectorModalLabel-<?php echo e($i); ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
                <div
                    class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                >
                    <h4
                        class="modal-title fw-bold"
                        id="viewChangeInspectorModalLabel-<?php echo e($i); ?>"
                    >
                        Detail Permintaan Ganti Petugas
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
                                value="<?php echo e($requestChange["mitra"] ?? "-"); ?>"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pengajuan</label>
                            <input
                                type="text"
                                class="form-control"
                                value="<?php echo e(\Carbon\Carbon::parse($requestChange["requested_date"] ?? now())->format("Y-m-d")); ?>"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Petugas Lama</label>
                            <input
                                type="text"
                                class="form-control"
                                value="<?php echo e($requestChange["petugas"] ?? "-"); ?>"
                                readonly
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Petugas Baru</label>
                            <input
                                type="text"
                                class="form-control"
                                value="<?php echo e($requestChange["petugas_baru"] ?? "-"); ?>"
                                readonly
                            />
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alasan Penggantian</label>
                            <textarea class="form-control" rows="3" readonly>
<?php echo e($requestChange["reason"] ?? "-"); ?></textarea
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/detail_changeinsp_modal.blade.php ENDPATH**/ ?>