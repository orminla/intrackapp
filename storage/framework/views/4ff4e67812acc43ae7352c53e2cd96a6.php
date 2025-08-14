

<?php $__env->startSection("title", "Jadwal & Laporan"); ?>

<?php $__env->startSection("content"); ?>
    <div class="col">
        <div class="row d-flex align-items-stretch">
            
            <div class="col-12 mb-4">
                <div class="card w-100 h-100 rounded-4">
                    <div class="card-body">
                        <div
                            class="d-md-flex align-items-center flex-wrap gap-3"
                        >
                            <h4>Jadwal Inspeksi</h4>
                            <div
                                class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                            >
                                <?php if($jadwalDalamProses): ?>
                                    <button
                                        class="btn btn-primary d-flex align-items-center gap-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#tambahLaporanModal"
                                    >
                                        <i class="ti ti-plus"></i>
                                        Tambah Laporan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table text-nowrap align-middle fs-3">
                                <thead>
                                    <tr class="text-center text-dark fw-bold">
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Mitra</th>
                                        <th>Lokasi</th>
                                        <th>Produk</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $indexAktif = 1;
                                    ?>

                                    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="text-center align-middle">
                                            <td><?php echo e($indexAktif++); ?></td>
                                            <td><?php echo e($schedule["tanggal"]); ?></td>
                                            <td><?php echo e($schedule["mitra"]); ?></td>
                                            <td><?php echo e($schedule["lokasi"]); ?></td>
                                            <td class="fw-bold">
                                                <?php echo e($schedule["produk"]); ?>

                                            </td>
                                            <td>
                                                <span
                                                    class="badge <?php echo e(match ($schedule["status"]) {
                                                            "Dalam proses" => "bg-warning-subtle text-warning",
                                                            default => "bg-secondary-subtle text-secondary",
                                                        }); ?> py-2 px-3 rounded-2"
                                                >
                                                    <?php echo e($schedule["status"]); ?>

                                                </span>
                                            </td>
                                            <td
                                                class="px-0 text-center align-middle"
                                            >
                                                
                                                <button
                                                    class="btn btn-sm px-1 border-0 bg-transparent"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailScheduleModal-<?php echo e("jadwal-" . $i); ?>"
                                                >
                                                    <i
                                                        class="ti ti-eye fs-5 text-primary"
                                                        title="Lihat"
                                                    ></i>
                                                </button>

                                                
                                                <?php if($schedule["status"] === "Menunggu konfirmasi"): ?>
                                                    <div class="btn-group">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm px-1 border-0 bg-transparent dropdown-toggle"
                                                            data-bs-toggle="dropdown"
                                                        >
                                                            <i
                                                                class="ti ti-circle-check fs-5 text-success"
                                                            ></i>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu dropdown-menu-end shadow rounded-3"
                                                        >
                                                            <?php
                                                                $opsi = [
                                                                    ["label" => "Disetujui", "icon" => "ti ti-check", "color" => "text-success"],
                                                                    ["label" => "Ditolak", "icon" => "ti ti-x", "color" => "text-danger"],
                                                                ];
                                                            ?>

                                                            <?php $__currentLoopData = $opsi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li>
                                                                    <form
                                                                        method="POST"
                                                                        action="<?php echo e(route("inspector.jadwal.validasi", $schedule["id"])); ?>"
                                                                    >
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field("PUT"); ?>
                                                                        <button
                                                                            type="button"
                                                                            class="dropdown-item d-flex align-items-center gap-2 btn-validasi <?php echo e($opt["color"]); ?>"
                                                                            data-status="<?php echo e($opt["label"]); ?>"
                                                                            data-action="<?php echo e(route("inspector.jadwal.validasi", $schedule["id"])); ?>"
                                                                            data-schedule-id="<?php echo e($schedule["id"]); ?>"
                                                                            data-mitra="<?php echo e($schedule["mitra"] ?? "-"); ?>"
                                                                        >
                                                                            <i
                                                                                class="<?php echo e($opt["icon"]); ?>"
                                                                            ></i>
                                                                            <?php echo e($opt["label"]); ?>

                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php if($indexAktif === 1): ?>
                                        <tr>
                                            <td
                                                colspan="7"
                                                class="text-center text-muted py-4"
                                            >
                                                Tidak ada jadwal aktif.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-12 mb-4">
                <div class="card w-100 h-100 rounded-4">
                    <div class="card-body">
                        <h4 class="mb-4">Laporan Menunggu / Ditolak</h4>
                        <div class="table-responsive">
                            <table class="table text-nowrap align-middle fs-3">
                                <thead>
                                    <tr class="text-center text-dark fw-bold">
                                        <th>No</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Mitra</th>
                                        <th>Lokasi</th>
                                        <th>Produk</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $indexLaporan = 1;
                                    ?>

                                    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="text-center align-middle">
                                            <td><?php echo e($indexLaporan++); ?></td>
                                            <td><?php echo e($schedule["tanggal"]); ?></td>
                                            <td>
                                                <?php echo e($schedule["tanggal_selesai"] ?? "-"); ?>

                                            </td>
                                            <td><?php echo e($schedule["mitra"]); ?></td>
                                            <td><?php echo e($schedule["lokasi"]); ?></td>
                                            <td class="fw-bold">
                                                <?php echo e($schedule["produk"]); ?>

                                            </td>
                                            <td>
                                                <span
                                                    class="badge <?php echo e($schedule["status"] === "Ditolak" ? "bg-danger-subtle text-danger" : "bg-info-subtle text-info"); ?> py-2 px-3 rounded-2"
                                                >
                                                    <?php echo e($schedule["status"]); ?>

                                                </span>
                                            </td>
                                            <td
                                                class="px-0 text-center align-middle"
                                            >
                                                <button
                                                    class="btn btn-sm px-1 border-0 bg-transparent"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editReportModal-jadwal-<?php echo e($i); ?>"
                                                >
                                                    <i
                                                        class="ti ti-edit fs-5 text-warning"
                                                        title="Lihat & Ubah"
                                                    ></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php if($indexLaporan === 1): ?>
                                        <tr>
                                            <td
                                                colspan="8"
                                                class="text-center text-muted py-4"
                                            >
                                                Belum ada laporan ditolak atau
                                                menunggu konfirmasi.
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
    </div>

    <?php echo $__env->make("inspector.add_report_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make("inspector.change_officer_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php
        $allDataForModal = collect($schedules)
            ->mapWithKeys(fn ($item, $i) => ["jadwal-$i" => $item])
            ->merge(collect($reports)->mapWithKeys(fn ($item, $i) => ["laporan-$i" => $item]));
    ?>

    <?php $__currentLoopData = $allDataForModal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make("inspector.detail_schedule_modal", ["schedule" => $schedule, "index" => $key], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $allDataForModal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make("inspector.edit_report_modal", ["schedule" => $report, "index" => $key], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <form id="form-validasi" method="POST" style="display: none">
        <?php echo csrf_field(); ?>
        <?php echo method_field("PUT"); ?>
        <input type="hidden" name="status" id="status-input" />
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.btn-validasi').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    const status = this.dataset.status;
                    const action = this.dataset.action;
                    const scheduleId = this.dataset.scheduleId;
                    const mitra = this.dataset.mitra || '-';

                    if (status.toLowerCase() === 'ditolak') {
                        // Modal ganti petugas
                        e.preventDefault();
                        const modalEl = document.getElementById('changeInspectorModal');
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();

                        const scheduleInput = modalEl.querySelector('input[name="schedule_id"]');
                        if (scheduleInput) scheduleInput.value = scheduleId;

                        const mitraField = modalEl.querySelector('input[name="mitra"]');
                        if (mitraField) mitraField.value = mitra;

                    } else if (status.toLowerCase() === 'disetujui' || status.toLowerCase() === 'selesai') {
                        // SweetAlert konfirmasi
                        e.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: `Yakin ingin mengubah status menjadi "${status}"?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, setujui',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-success rounded-2 px-4 me-2',
                                cancelButton: 'btn btn-outline-muted rounded-2 px-4'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Submit via AJAX
                                const form = document.getElementById('form-validasi');
                                form.action = action;
                                form.querySelector('#status-input').value = status;

                                const formData = new FormData(form);
                                fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        Accept: 'application/json'
                                    },
                                    body: formData
                                })
                                .then(async res => {
                                    const data = await res.json();
                                    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan');

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message || 'Status inspeksi berhasil diperbarui.',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        customClass: { popup: 'rounded-4' },
                                        buttonsStyling: false
                                    }).then(() => location.reload());
                                })
                                .catch(err => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: err.message || 'Terjadi kesalahan saat memproses permintaan.',
                                        customClass: { popup: 'rounded-4' },
                                        buttonsStyling: false
                                    });
                                });
                            }
                        });
                    }
                });
            });

            // SweetAlert global untuk session success/error (setelah reload)
            <?php if(session('success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "<?php echo e(session('success')); ?>",
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' },
                    buttonsStyling: false
                });
            <?php endif; ?>
            <?php if(session('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "<?php echo e(session('error')); ?>",
                    customClass: { popup: 'rounded-4' },
                    buttonsStyling: false
                });
            <?php endif; ?>

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("inspector.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/inspection_schedule_and_report.blade.php ENDPATH**/ ?>