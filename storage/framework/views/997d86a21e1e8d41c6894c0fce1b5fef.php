

<?php $__env->startSection("title", "Riwayat Inspeksi"); ?>

<?php $__env->startSection("content"); ?>
    <div class="card rounded-4">
        <div class="card-body">
            <div
                class="d-flex justify-content-between align-items-center flex-wrap mb-4"
            >
                <h4 class="card-title mb-0">Riwayat Inspeksi</h4>
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap align-middle fs-3">
                    <thead>
                        <tr class="text-center text-dark fw-bold">
                            <th>No</th>
                            <th>Mitra</th>
                            <th>Lokasi</th>
                            <th>Produk</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="text-center align-middle">
                                <td><?php echo e($index + 1); ?></td>
                                <td><?php echo e($item->partner); ?></td>
                                <td><?php echo e($item->location ?? "-"); ?></td>
                                <td><?php echo e($item->product); ?></td>
                                <td><?php echo e($item->date ?? "-"); ?></td>
                                <td><?php echo e($item->tanggal_selesai ?? "-"); ?></td>

                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm px-1 border-0 bg-transparent"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailHistoryModal-<?php echo e($index); ?>"
                                        title="Lihat Detail"
                                    >
                                        <i
                                            class="ti ti-eye fs-5 text-primary"
                                        ></i>
                                    </button>

                                    <a
                                        href="<?php echo e(route("inspector.riwayat.download", $item->id)); ?>"
                                        class="btn btn-sm px-1 border-0 bg-transparent"
                                        title="Unduh Bukti Inspeksi"
                                    >
                                        <i
                                            class="ti ti-download fs-5 text-success"
                                        ></i>
                                    </a>
                                </td>
                            </tr>

                            
                            <?php echo $__env->make(
                                "inspector.detail_history_modal",
                                [
                                    "index" => $index,
                                    "schedule" => [
                                        "mitra" => $item->partner,
                                        "lokasi" => $item->location ?? "-",
                                        "tanggal" => $item->date,
                                        "tanggal_selesai" => $item->tanggal_selesai ?? "",
                                        "produk" => $item->product,
                                        "detail_produk" => $item->detail_produk ?? [],
                                        "dokumen" => $item->documents ?? [],
                                    ],
                                ]
                            , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center text-muted py-4"
                                >
                                    Tidak ada riwayat inspeksi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("inspector.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/inspection_history.blade.php ENDPATH**/ ?>