

<?php $__env->startSection("title", "Dashboard"); ?>

<?php $__env->startSection("content"); ?>
    
    <div class="row mb-4">
        <?php
            $cards = [
                ["key" => "inspeksi_selesai", "label" => "Inspeksi Selesai", "icon" => "ti-circle-check", "color" => "primary"],
                ["key" => "menunggu_validasi", "label" => "Menunggu", "icon" => "ti-alert-circle", "color" => "secondary"],
                ["key" => "laporan_ditolak", "label" => "Laporan Ditolak", "icon" => "ti-circle-x", "color" => "danger"],
                ["key" => "belum_lapor", "label" => "Belum Lapor", "icon" => "ti-clock-hour-4", "color" => "warning"],
            ];

            $cardUrls = [
                "inspeksi_selesai" => route("inspector.riwayat.index"),
                "menunggu_validasi" => route("inspector.jadwal.index"),
                "laporan_ditolak" => route("inspector.jadwal.index"),
                "belum_lapor" => route("inspector.jadwal.index"),
            ];
        ?>

        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-3 col-sm-6 mb-2">
                <a
                    href="<?php echo e($cardUrls[$card["key"]] ?? "#"); ?>"
                    class="text-decoration-none"
                >
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
                                    <?php echo e($card["label"]); ?>

                                </small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="row align-items-stretch">
        
        <div class="col-lg-5 d-flex">
            <div class="card rounded-4 h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-semibold mb-4">Inspeksi Terbaru</h5>

                    <?php if($latest): ?>
                        <?php $__currentLoopData = ["Mitra", "Tanggal Inspeksi", "Produk", "Lokasi"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-2 d-flex">
                                <div class="text-muted" style="width: 130px">
                                    <?php echo e($label); ?>

                                </div>
                                <div class="flex-grow-1">
                                    <div class="bg-light p-2 rounded">
                                        <?php echo e($latest[$label] ?? "-"); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <button
                            class="btn w-100 mt-auto mb-2 <?php echo e($hasRequestedChange ? "btn-muted" : "btn-primary"); ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#changeInspectorModal"
                            <?php if($hasRequestedChange): ?> disabled <?php endif; ?>
                        >
                            <?php if($hasRequestedChange): ?>
                                Menunggu Konfirmasi
                            <?php else: ?>
                                    Ajukan Ganti Petugas
                            <?php endif; ?>
                        </button>

                        
                        <?php if($hasRequestedChange): ?>
                            <small class="text-danger d-block mb-2">
                                Anda telah mengajukan pergantian petugas dan
                                sedang menunggu konfirmasi dari admin.
                            </small>
                        <?php else: ?>
                            <small
                                class="d-block text-muted"
                                style="font-size: 0.75rem"
                            >
                                Maksimal 2 kali pergantian petugas setiap bulan.
                                <br />
                                Konfirmasi pergantian terakhir sebelum
                                <strong><?php echo e($latestDeadline ?? "-"); ?></strong>
                                .
                            </small>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Belum ada jadwal inspeksi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7 d-flex">
            <div class="card rounded-4 h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-semibold">Aktivitas & Pemberitahuan</h5>
                    <div class="mt-4">
                        <small class="text-muted">Hari Ini</small>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i class="ti ti-upload text-primary me-2"></i>
                                Anda mengunggah laporan inspeksi untuk PT Wilfar
                            </div>
                            <small class="text-muted">10 Menit lalu</small>
                        </div>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i
                                    class="ti ti-calendar-event text-danger me-2"
                                ></i>
                                Jadwal inspeksi baru ditugaskan: UD Sentosa
                            </div>
                            <small class="text-muted">Hari ini, 09.10</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">Minggu Ini</small>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i
                                    class="ti ti-calendar-event text-danger me-2"
                                ></i>
                                Jadwal inspeksi baru ditugaskan: UD Sentosa
                            </div>
                            <small class="text-muted">Kemarin, 16.45</small>
                        </div>
                        <div
                            class="d-flex align-items-start justify-content-between bg-light rounded-3 p-2 mb-2"
                        >
                            <div>
                                <i class="ti ti-upload text-primary me-2"></i>
                                Anda mengunggah laporan inspeksi untuk PT Wilfar
                            </div>
                            <small class="text-muted">2 Hari lalu</small>
                        </div>
                    </div>
                    <div class="flex-grow-1"></div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make("inspector.change_officer_modal", ["latest" => $latest], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("inspector.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/dashboard.blade.php ENDPATH**/ ?>