<?php
    $showingSelected = request()->get("showing", "10");
?>



<?php $__env->startSection("title", "Riwayat Inspeksi"); ?>

<?php $__env->startSection("content"); ?>
    <div class="card rounded-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Riwayat Inspeksi</h4>

                <form
                    method="GET"
                    id="showingForm"
                    class="d-flex align-items-center gap-2"
                >
                    <label for="showing" class="fw-normal text-muted mb-0">
                        Showing
                    </label>
                    <select
                        name="showing"
                        id="showing"
                        class="form-select form-select-sm border-0 bg-light"
                        style="
                            width: auto;
                            min-width: 70px;
                            height: 36px;
                            font-size: 0.875rem;
                        "
                        onchange="document.getElementById('showingForm').submit()"
                    >
                        <option
                            value="10"
                            <?php echo e($showingSelected == "10" ? "selected" : ""); ?>

                        >
                            10
                        </option>
                        <option
                            value="25"
                            <?php echo e($showingSelected == "25" ? "selected" : ""); ?>

                        >
                            25
                        </option>
                        <option
                            value="50"
                            <?php echo e($showingSelected == "50" ? "selected" : ""); ?>

                        >
                            50
                        </option>
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap align-middle fs-3">
                    <thead>
                        <tr class="text-center text-dark fw-bold">
                            <th>No</th>
                            <th>Mitra</th>
                            <th>Lokasi</th>
                            <th>Petugas</th>
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
                                <td><?php echo e($item->inspector_name ?? "-"); ?></td>
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
                                        href="<?php echo e(route("admin.riwayat.download", $item->id)); ?>"
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
                                "admin.detail_history_modal",
                                [
                                    "index" => $index,
                                    "schedule" => [
                                        "mitra" => $item->partner,
                                        "lokasi" => $item->location ?? "-",
                                        "tanggal" => $item->date,
                                        "tanggal_selesai" => $item->tanggal_selesai ?? "",
                                        "produk" => $item->product,
                                        "bidang" => $item->bidang ?? "-",
                                        "petugas" => $item->petugas ?? "-",
                                        "detail_produk" => $item->detail_produk ?? [],
                                        "dokumen" => $item->documents ?? [],
                                    ],
                                ]
                            , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td
                                    colspan="8"
                                    class="text-center text-muted py-4"
                                >
                                    Tidak ada riwayat inspeksi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                
                <?php if (isset($component)) { $__componentOriginal2db78c7485c7f31546e3ba142cc29213 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2db78c7485c7f31546e3ba142cc29213 = $attributes; } ?>
<?php $component = App\View\Components\TablePagination::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\TablePagination::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($histories)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2db78c7485c7f31546e3ba142cc29213)): ?>
<?php $attributes = $__attributesOriginal2db78c7485c7f31546e3ba142cc29213; ?>
<?php unset($__attributesOriginal2db78c7485c7f31546e3ba142cc29213); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2db78c7485c7f31546e3ba142cc29213)): ?>
<?php $component = $__componentOriginal2db78c7485c7f31546e3ba142cc29213; ?>
<?php unset($__componentOriginal2db78c7485c7f31546e3ba142cc29213); ?>
<?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/inspection_history.blade.php ENDPATH**/ ?>