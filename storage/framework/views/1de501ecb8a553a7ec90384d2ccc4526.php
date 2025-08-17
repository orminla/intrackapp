<?php $__env->startPush("styles"); ?>
    <style>
        tr[onclick]:hover {
            background-color: #f5f5f5;
        }
    </style>
<?php $__env->stopPush(); ?>



<?php $__env->startSection("title", "Dashboard"); ?>

<?php $__env->startSection("content"); ?>
    
    <div class="row">
        <?php $__currentLoopData = [
                [
                    "key" => "inspeksi_selesai",
                    "title" => "Inspeksi Selesai",
                    "icon" => "ti-circle-check",
                    "color" => "primary",
                    "url" => route("admin.riwayat")
                ],
                [
                    "key" => "inspeksi_hari_ini",
                    "title" => "Inspeksi Hari Ini",
                    "icon" => "ti-calendar-event",
                    "color" => "success",
                    "url" => route("admin.jadwal.index")
                ],
                [
                    "key" => "inspeksi_mendatang",
                    "title" => "Akan Datang",
                    "icon" => "ti-calendar-stats",
                    "color" => "warning",
                    "url" => route("admin.dashboard") . "#upcoming"
                ],
                [
                    "key" => "laporan_perlu_validasi",
                    "title" => "Validasi Laporan",
                    "icon" => "ti-file-check",
                    "color" => "danger",
                    "url" => route("admin.laporan")
                ]
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-3 col-sm-6 mb-1">
                <a href="<?php echo e($card["url"]); ?>" class="text-decoration-none">
                    <div class="card h-100 rounded-4">
                        <div
                            class="card-body d-flex align-items-center gap-3 p-3"
                        >
                            <span
                                class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?php echo e($card["color"]); ?>-subtle text-<?php echo e($card["color"]); ?> ms-4"
                                style="width: 40px; height: 40px"
                            >
                                <i
                                    class="ti <?php echo e($card["icon"]); ?>"
                                    style="font-size: 18px"
                                ></i>
                            </span>
                            <div style="max-width: 100px">
                                <h5 class="mb-0 fw-semibold">
                                    <?php echo e($summary[$card["key"]] ?? 0); ?>

                                </h5>
                                <small class="text-muted">
                                    <?php echo e($card["title"]); ?>

                                </small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="row d-flex align-items-stretch mt-4">
        
        <div class="col-lg-8 d-flex">
            <div class="card w-100 rounded-4">
                <div class="card-body">
                    <div
                        class="d-flex justify-content-between align-items-start gap-2 mb-2"
                    >
                        <div>
                            <h4 class="card-title">Statistik Inspeksi</h4>
                            <p class="card-subtitle text-muted">
                                Perbandingan jumlah inspeksi BITU & BIP
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a
                                href="javascript:void(0)"
                                id="download-png"
                                class="text-muted"
                                title="Download PNG"
                            >
                                <i class="ti ti-download fs-6"></i>
                            </a>
                        </div>
                    </div>

                    <div
                        id="inspectionChart"
                        class="mt-1 mx-n6"
                        style="height: 300px; max-height: 300px"
                    ></div>

                    <ul class="list-unstyled mb-0 mt-3">
                        <li class="list-inline-item text-primary">
                            <span
                                class="round-8 text-bg-primary rounded-circle me-1 d-inline-block"
                            ></span>
                            Bidang Inspeksi Teknik & Umum (BITU)
                        </li>
                        <li class="list-inline-item text-info">
                            <span
                                class="round-8 text-bg-info rounded-circle me-1 d-inline-block"
                            ></span>
                            Bidang Inspeksi & Pengujian (BIP)
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4 d-flex">
            <div class="card w-100 rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div>
                            <h4 class="card-title">Status Distribusi Tugas</h4>
                            <p class="card-subtitle text-muted">
                                Distribusi berdasarkan kategori
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="javascript:void(0)" class="text-muted">
                                <i class="ti ti-dots fs-7"></i>
                            </a>
                        </div>
                    </div>
                    <div id="distributionPieChart" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row d-flex align-items-stretch" id="upcoming">
        <div class="col-12 d-flex">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center">
                        <div>
                            <h4 class="card-title">
                                Jadwal Inspeksi Mendatang
                            </h4>
                            <p class="card-subtitle">
                                Inspeksi 7 hari ke depan
                            </p>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr
                                        style="cursor: pointer"
                                        onclick="window.location='<?php echo e(route("admin.jadwal.index")); ?>'"
                                    >
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
                                            <?php echo e($item["portofolio"]); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php
                                                $status = $item["status"];
                                                $badgeMap = [
                                                    "Selesai" => ["label" => "Selesai", "warna" => "success"],
                                                    "Menunggu konfirmasi" => ["label" => "Menunggu", "warna" => "info"],
                                                    "Dalam proses" => ["label" => "Diproses", "warna" => "warning"],
                                                    "Dijadwalkan ganti" => ["label" => "Ganti", "warna" => "secondary"],
                                                ];
                                                $statusBadge = $badgeMap[$status] ?? ["label" => $status, "warna" => "dark"];
                                            ?>

                                            <span
                                                class="badge bg-<?php echo e($statusBadge["warna"]); ?>-subtle text-<?php echo e($statusBadge["warna"]); ?> py-2 px-3 rounded-2"
                                            >
                                                <?php echo e($statusBadge["label"]); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("scripts"); ?>
    
    <script src="<?php echo e(asset("admin_assets/js/linechart.js")); ?>"></script>
    <script src="<?php echo e(asset("admin_assets/js/doughnutchart.js")); ?>"></script>
    <script src="<?php echo e(asset("admin_assets/js/dashboard.js")); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    
    <script>
        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Login',
                text: "<?php echo e(session('success')); ?>",
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: "<?php echo e(session('error')); ?>",
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("admin.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>