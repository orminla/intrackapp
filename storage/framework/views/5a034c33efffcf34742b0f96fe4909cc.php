

<?php $__env->startSection("title", "Jadwal Inspeksi"); ?>

<?php $__env->startSection("content"); ?>
    <div class="row d-flex align-items-stretch">
        <!-- Jadwal Inspeksi -->
        <div class="col-12 mb-2">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Jadwal Inspeksi</h4>
                        </div>

                        <!-- Showing, filter, tambah -->
                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <!-- Filter & Showing -->
                            <form
                                id="filterForm"
                                method="GET"
                                class="d-flex align-items-center gap-3 mb-3"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="showing"
                                        class="fw-normal text-muted mb-0"
                                    >
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
                                        onchange="document.getElementById('filterForm').submit()"
                                    >
                                        <option
                                            value="10"
                                            <?php echo e($showingSelected == 10 ? "selected" : ""); ?>

                                        >
                                            10
                                        </option>
                                        <option
                                            value="25"
                                            <?php echo e($showingSelected == 25 ? "selected" : ""); ?>

                                        >
                                            25
                                        </option>
                                        <option
                                            value="50"
                                            <?php echo e($showingSelected == 50 ? "selected" : ""); ?>

                                        >
                                            50
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="filter"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Filter
                                    </label>
                                    <select
                                        name="filter"
                                        id="filter"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            min-width: 100px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterForm').submit()"
                                    >
                                        <option
                                            value="all"
                                            <?php echo e($filterSelected == "all" ? "selected" : ""); ?>

                                        >
                                            Semua
                                        </option>
                                        <option
                                            value="Menunggu konfirmasi"
                                            <?php echo e($filterSelected == "Menunggu konfirmasi" ? "selected" : ""); ?>

                                        >
                                            Menunggu
                                        </option>
                                        <option
                                            value="Dijadwalkan ganti"
                                            <?php echo e($filterSelected == "Dijadwalkan ganti" ? "selected" : ""); ?>

                                        >
                                            Dijadwalkan ganti
                                        </option>
                                        <option
                                            value="Dalam proses"
                                            <?php echo e($filterSelected == "Dalam proses" ? "selected" : ""); ?>

                                        >
                                            Diproses
                                        </option>
                                    </select>
                                </div>
                            </form>

                            <!-- Tambah -->
                            <div>
                                <button
                                    class="btn btn-primary d-flex align-items-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#tambahJadwalModal"
                                >
                                    <i class="ti ti-plus"></i>
                                    Tambah Jadwal
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <!-- Tabel Jadwal -->
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th class="text-center fw-bold">No</th>
                                    <th class="text-center fw-bold">Tanggal</th>
                                    <th class="text-center fw-bold">Mitra</th>
                                    <th class="text-center fw-bold">Lokasi</th>
                                    <th class="text-center fw-bold">Petugas</th>
                                    <th class="text-center fw-bold">Produk</th>
                                    <th class="text-center fw-bold">Status</th>
                                    <th class="text-center fw-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo e(($schedules->currentPage() - 1) * $schedules->perPage() + $index + 1); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($schedule["tanggal_inspeksi"]); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($schedule["nama_mitra"]); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $parts = explode(",", $schedule["lokasi"]);
                                                $first = trim($parts[0] ?? "-");
                                                $last = trim(end($parts) ?? "-");
                                            ?>

                                            <?php echo e($first); ?>, <?php echo e($last); ?>

                                        </td>
                                        <td class="text-center fw-bolder">
                                            <?php echo e($schedule["nama_petugas"]); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($schedule["produk"]); ?>

                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge <?php switch($schedule['status']):
                            case ('Menunggu konfirmasi'): ?> bg-secondary-subtle text-secondary <?php break; ?>
                            <?php case ('Dijadwalkan ganti'): ?> bg-orange-subtle text-orange <?php break; ?>
                            <?php case ('Dalam proses'): ?> bg-warning-subtle text-warning <?php break; ?>
                            <?php default: ?> bg-light text-dark
                        <?php endswitch; ?> py-2 px-3 rounded-2"
                                            >
                                                <?php echo e($schedule["status"]); ?>

                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php if($schedule["status"] === "Menunggu konfirmasi"): ?>
                                                <button
                                                    class="btn btn-sm px-1 border-0 bg-transparent"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailScheduleModal-<?php echo e($index); ?>"
                                                >
                                                    <i
                                                        class="ti ti-edit fs-5 text-warning"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-title="Lihat & Ubah"
                                                    ></i>
                                                </button>
                                            <?php endif; ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route("admin.jadwal.destroy", $schedule["id"])); ?>"
                                                class="d-inline delete-form"
                                            >
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field("DELETE"); ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm px-1 border-0 bg-transparent delete-button"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Hapus"
                                                >
                                                    <i
                                                        class="ti ti-trash fs-5 text-danger"
                                                    ></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Tidak ada jadwal inspeksi.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($component)) { $__componentOriginal2db78c7485c7f31546e3ba142cc29213 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2db78c7485c7f31546e3ba142cc29213 = $attributes; } ?>
<?php $component = App\View\Components\TablePagination::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\TablePagination::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($schedules)]); ?>
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

        <!-- Permintaan Ganti Petugas -->
        <div class="col-12 mt-4">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Permintaan Ganti Petugas</h4>
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
                                    style="min-width: 70px; height: 36px"
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
                                    style="min-width: 100px; height: 36px"
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
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Mitra</th>
                                    <th class="text-center">Petugas</th>
                                    <th class="text-center">Petugas Baru</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($i + 1); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($req["tanggal_pengajuan"] ?? "-"); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($req["mitra"] ?? "-"); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($req["petugas"] ?? "-"); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php echo e($req["petugas_baru"] ?? "-"); ?>

                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <?php
                                                $badgeColor = match (strtolower($req["status"] ?? "")) {
                                                    "disetujui" => "bg-success-subtle text-success",
                                                    "ditolak" => "bg-danger-subtle text-danger",
                                                    default => "bg-secondary-subtle text-secondary",
                                                };
                                            ?>

                                            <span
                                                class="badge <?php echo e($badgeColor); ?> py-2 px-3 rounded-2"
                                            >
                                                <?php echo e(ucfirst($req["status"])); ?>

                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Lihat -->
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeInspectorModal-<?php echo e($i); ?>"
                                                title="Lihat"
                                            >
                                                <i
                                                    class="ti ti-edit fs-5 text-warning"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Lihat & Ubah"
                                                ></i>
                                            </button>

                                            <!-- Validasi -->
                                            <?php
                                                $status = strtolower($req["status"]);
                                                $validasiOpsi = [];

                                                if ($status === "menunggu konfirmasi") {
                                                    $validasiOpsi = [
                                                        ["label" => "Disetujui", "icon" => "ti ti-check", "color" => "text-success"],
                                                        ["label" => "Ditolak", "icon" => "ti ti-x", "color" => "text-danger"],
                                                    ];
                                                }
                                            ?>

                                            <?php if(count($validasiOpsi)): ?>
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
                                                        <?php $__currentLoopData = $validasiOpsi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li>
                                                                <form
                                                                    method="POST"
                                                                    action="<?php echo e(route("admin.jadwal.validasi", $req["id"])); ?>"
                                                                    class="d-inline-block w-100"
                                                                >
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field("PUT"); ?>
                                                                    <input
                                                                        type="hidden"
                                                                        name="status"
                                                                        value="<?php echo e($opt["label"]); ?>"
                                                                    />
                                                                    <button
                                                                        type="submit"
                                                                        class="dropdown-item d-flex align-items-center gap-2 <?php echo e($opt["color"]); ?>"
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
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Tidak ada permintaan ganti petugas.
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

    
    <?php echo $__env->make("admin.add_schedule_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make("admin.detail_schedule_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        document
            .getElementById('showing')
            .addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });
        document
            .getElementById('filter')
            .addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });

        document.addEventListener('DOMContentLoaded', function () {
            // Konfirmasi sebelum hapus dengan SweetAlert2 dan AJAX
            document.querySelectorAll('.delete-button').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const form = btn.closest('form');
                    Swal.fire({
                        title: 'Yakin ingin menghapus data petugas?',
                        text: 'Tindakan ini tidak dapat dibatalkan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-danger rounded-2 px-4 me-2',
                            cancelButton: 'btn btn-outline-muted rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    Accept: 'application/json',
                                },
                                body: new URLSearchParams(new FormData(form)),
                            })
                            .then(async (response) => {
                                const data = await response.json();
                                if (!response.ok) throw new Error(data.message || 'Gagal menghapus data');
                                return data;
                            })
                            .then((data) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message || 'Data petugas berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton: 'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch((error) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: error.message || 'Terjadi kesalahan saat menghapus.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton: 'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                });
                            });
                        }
                    });
                });
            });

            <?php if(session('success') || session('error') || session('warning')): ?>
                Swal.fire({
                    icon: '<?php echo e(session("success") ? "success" : (session("error") ? "error" : "warning")); ?>',
                    title: '<?php echo e(session("success") ? "Berhasil!" : (session("error") ? "Gagal!" : "Perhatian!")); ?>',
                    text: "<?php echo e(session('success') ?? session('error') ?? session('warning')); ?>",
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-primary rounded-2 px-4',
                    },
                    buttonsStyling: false,
                });
            <?php endif; ?>

            <?php if($errors->any()): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: '<?php echo implode("<br>", $errors->all()); ?>',
                    timer: 1500,
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-primary rounded-2 px-4',
                    },
                    buttonsStyling: false,
                });
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("admin.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/inspection_schedule.blade.php ENDPATH**/ ?>