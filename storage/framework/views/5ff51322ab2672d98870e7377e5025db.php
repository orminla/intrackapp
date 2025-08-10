

<?php $__env->startSection("title", "Riwayat Inspeksi"); ?>

<?php $__env->startSection("content"); ?>
    <div class="row d-flex align-items-stretch">
        <div class="col-12">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Laporan Inspeksi</h4>
                        </div>

                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">
                                    Showing
                                </span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="width: auto; min-width: 70px"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">Filter</span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="width: auto; min-width: 100px"
                                >
                                    <option value="all">Semua</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="menunggu">Menunggu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Mitra
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Portofolio
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $warna = match (strtolower($item["status"])) {
                                            "dalam proses" => "info",
                                            "ditolak" => "danger",
                                            "menunggu konfirmasi" => "warning",
                                            "disetujui" => "success",
                                            default => "secondary",
                                        };
                                    ?>

                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($i + 1); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($item["tanggal_mulai"]); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($item["nama_mitra"]); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($item["lokasi"]); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle fw-bolder"
                                        >
                                            <?php echo e($item["petugas"]); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e(Str::before($item["portofolio"], " -")); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <span
                                                class="badge bg-<?php echo e($warna); ?>-subtle text-<?php echo e($warna); ?> py-2 px-3 rounded-2"
                                            >
                                                <?php echo e($item["status"]); ?>

                                            </span>
                                        </td>

                                        
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detailReportModal-<?php echo e($i); ?>"
                                                title="Lihat Detail"
                                            >
                                                <i
                                                    class="ti ti-eye fs-5 text-primary"
                                                ></i>
                                            </button>

                                            
                                            <?php if(strtolower($item["status"]) === "menunggu konfirmasi"): ?>
                                                <div class="btn-group">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm px-1 border-0 bg-transparent dropdown-toggle"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        title="Validasi"
                                                    >
                                                        <i
                                                            class="ti ti-circle-check fs-5 text-success"
                                                        ></i>
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end shadow rounded-3"
                                                    >
                                                        
                                                        <li>
                                                            <form
                                                                method="POST"
                                                                action="<?php echo e(route("admin.laporan.validasi", $item["id"])); ?>"
                                                                class="d-inline-block w-100"
                                                            >
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field("PUT"); ?>
                                                                <input
                                                                    type="hidden"
                                                                    name="status"
                                                                    value="Disetujui"
                                                                />
                                                                <button
                                                                    type="submit"
                                                                    class="dropdown-item d-flex align-items-center gap-2 text-success"
                                                                >
                                                                    <i
                                                                        class="ti ti-check"
                                                                    ></i>
                                                                    Setujui
                                                                </button>
                                                            </form>
                                                        </li>

                                                        
                                                        <li>
                                                            <button
                                                                class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalTolak-<?php echo e($item["id"]); ?>"
                                                            >
                                                                <i
                                                                    class="ti ti-x"
                                                                ></i>
                                                                Tolak
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    
                                    <?php echo $__env->make(
                                        "admin.detail_report_modal",
                                        [
                                            "index" => $i,
                                            "schedule" => $item["detail"] ?? [
                                                "mitra" => $item["nama_mitra"] ?? "-",
                                                "lokasi" => $item["lokasi"] ?? "-",
                                                "tanggal" => $item["tanggal_mulai"] ?? "",
                                                "tanggal_selesai" => "",
                                                "produk" => "-",
                                                "bidang" => $item["portofolio"] ?? "-",
                                                "petugas" => $item["petugas"] ?? "-",
                                                "detail_produk" => [],
                                                "dokumen" => [],
                                            ],
                                        ]
                                    , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Tidak ada laporan inspeksi.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make("admin.rejected_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/inspection_reports.blade.php ENDPATH**/ ?>