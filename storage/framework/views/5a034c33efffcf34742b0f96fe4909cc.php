

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
                            <form
                                id="filterForm"
                                method="GET"
                                class="d-flex align-items-center gap-3 mb-0"
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
                                            max-width: 120px;
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

                            <button
                                class="btn btn-primary d-flex align-items-center gap-2"
                                data-bs-toggle="modal"
                                data-bs-target="#tambahJadwalModal"
                                style="height: 36px; font-size: 0.875rem"
                            >
                                <i class="ti ti-plus"></i>
                                Tambah Jadwal
                            </button>
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
                                    <th class="text-center fw-bold">Petugas</th>
                                    <th class="text-center fw-bold">
                                        Portofolio
                                    </th>
                                    <th class="text-center fw-bold">Lokasi</th>
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
                                        <td class="text-center fw-bolder">
                                            <?php echo e($schedule["nama_petugas"]); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e(explode("-", $schedule["portofolio"])[0] ?? $schedule["portofolio"]); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $parts = explode(",", $schedule["lokasi"] ?? "");
                                                $last = trim(end($parts));
                                            ?>

                                            <?php echo e($last ?: "-"); ?>

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
                                                        class="ti ti-eye fs-5 text-warning"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-title="Lihat"
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

                        <!-- Showing & Filter -->
                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <form
                                id="filterChangeForm"
                                method="GET"
                                class="d-flex align-items-center gap-3 mb-0"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="showing_change"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Showing
                                    </label>
                                    <select
                                        name="showing_change"
                                        id="showing_change"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            min-width: 70px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterChangeForm').submit()"
                                    >
                                        <option
                                            value="10"
                                            <?php echo e(($showingChangeSelected ?? 10) == 10 ? "selected" : ""); ?>

                                        >
                                            10
                                        </option>
                                        <option
                                            value="25"
                                            <?php echo e(($showingChangeSelected ?? 10) == 25 ? "selected" : ""); ?>

                                        >
                                            25
                                        </option>
                                        <option
                                            value="50"
                                            <?php echo e(($showingChangeSelected ?? 10) == 50 ? "selected" : ""); ?>

                                        >
                                            50
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="filter_change"
                                        class="fw-normal text-muted mb-0"
                                    >
                                        Filter
                                    </label>
                                    <select
                                        name="filter_change"
                                        id="filter_change"
                                        class="form-select form-select-sm border-0 bg-light"
                                        style="
                                            width: auto;
                                            max-width: 160px;
                                            height: 36px;
                                            font-size: 0.875rem;
                                        "
                                        onchange="document.getElementById('filterChangeForm').submit()"
                                    >
                                        <option
                                            value="all"
                                            <?php echo e(($filterChangeSelected ?? "all") == "all" ? "selected" : ""); ?>

                                        >
                                            Semua
                                        </option>
                                        <option
                                            value="Menunggu Konfirmasi"
                                            <?php echo e(($filterChangeSelected ?? "") == "Menunggu Konfirmasi" ? "selected" : ""); ?>

                                        >
                                            Menunggu Konfirmasi
                                        </option>
                                        <option
                                            value="Disetujui"
                                            <?php echo e(($filterChangeSelected ?? "") == "Disetujui" ? "selected" : ""); ?>

                                        >
                                            Disetujui
                                        </option>
                                        <option
                                            value="Ditolak"
                                            <?php echo e(($filterChangeSelected ?? "") == "Ditolak" ? "selected" : ""); ?>

                                        >
                                            Ditolak
                                        </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Permintaan -->
                    <div class="table-responsive mt-4">
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Mitra</th>
                                    <th class="text-center">Petugas Lama</th>
                                    <th class="text-center">Petugas Baru</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo e(($changeRequests->currentPage() - 1) * $changeRequests->perPage() + $i + 1); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($req->tanggal_pengajuan ?? "-"); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($req->mitra ?? "-"); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($req->petugas ?? "-"); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php echo e($req->petugas_baru ?? "-"); ?>

                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $badgeColor = match (strtolower($req->status ?? "")) {
                                                    "disetujui" => "bg-success-subtle text-success",
                                                    "ditolak" => "bg-danger-subtle text-danger",
                                                    default => "bg-secondary-subtle text-secondary",
                                                };
                                            ?>

                                            <span
                                                class="badge <?php echo e($badgeColor); ?> py-2 px-3 rounded-2"
                                            >
                                                <?php echo e(ucfirst($req->status)); ?>

                                            </span>
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Lihat -->
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewChangeInspectorModal-<?php echo e($i); ?>"
                                                title="Lihat"
                                            >
                                                <i
                                                    class="ti ti-eye fs-5 text-primary"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Lihat"
                                                ></i>
                                            </button>

                                            <?php if($req->status === "Menunggu Konfirmasi"): ?>
                                                <!-- Reload -->
                                                <button
                                                    class="btn btn-sm px-1 border-0 bg-transparent btn-reload-inspector"
                                                    type="button"
                                                    title="Reload Petugas Baru"
                                                    data-id="<?php echo e($req["id"]); ?>"
                                                    data-portfolio-id="<?php echo e($req->schedule->inspector->portfolio_id ?? ""); ?>"
                                                    data-started-date="<?php echo e($req["tanggal_pengajuan"] ?? ($req["requested_date"] ?? "")); ?>"
                                                    data-reload-count="0"
                                                >
                                                    <i
                                                        class="ti ti-refresh fs-5 text-warning"
                                                    ></i>
                                                </button>
                                            <?php endif; ?>

                                            <!-- Validasi opsi (disetujui/ditolak) untuk status 'menunggu konfirmasi' -->
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
                                                                    class="d-inline-block w-100 validation-form"
                                                                >
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field("PUT"); ?>
                                                                    <input
                                                                        type="hidden"
                                                                        name="status"
                                                                        value="<?php echo e($opt["label"]); ?>"
                                                                    />
                                                                    <input
                                                                        type="hidden"
                                                                        name="reloaded_inspector_id"
                                                                        value=""
                                                                        class="reload-id-input"
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
<?php $component->withAttributes(['data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($changeRequests)]); ?>
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
    </div>

    
    <?php echo $__env->make("admin.add_schedule_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make("admin.detail_schedule_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make("admin.detail_changeinsp_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("scripts"); ?>
    <script>
        // SweetAlert flash message langsung jalan tanpa tunggu DOMContentLoaded
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

        <?php if(session('inspector_changed')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "<?php echo e(session('inspector_changed')); ?>",
                timer: 1800,
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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tombol reload petugas di tabel
            document
                .querySelectorAll('.btn-reload-inspector')
                .forEach((btn) => {
                    btn.addEventListener('click', function () {
                        let reloadCount = parseInt(
                            this.dataset.reloadCount || '0',
                        );

                        if (reloadCount >= 2) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Batas reload tercapai',
                                text: 'Maksimal reload petugas 2 kali sebelum menyimpan.',
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-warning rounded-2 px-4',
                                },
                                buttonsStyling: false,
                            });
                            return;
                        }

                        const id = this.dataset.id;
                        const portfolioId = this.dataset.portfolioId;
                        const startedDate = this.dataset.startedDate;

                        this.disabled = true;
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.classList.add(
                                'spinner-border',
                                'spinner-border-sm',
                            );
                            icon.classList.remove('ti-refresh');
                        }

                        fetch(
                            `/admin/get-inspector?portfolio_id=${portfolioId}&started_date=${startedDate}`,
                        )
                            .then((res) => res.json())
                            .then((data) => {
                                if (data.inspector_id && data.name) {
                                    const row = btn.closest('tr');
                                    const petugasBaruCell =
                                        row.querySelectorAll('td')[4];
                                    const currentName =
                                        petugasBaruCell.textContent.trim();

                                    if (currentName === data.name) {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Petugas sama',
                                            html: `Petugas pengganti adalah <strong>${data.name}</strong> yang sudah ada di tabel.`,
                                            customClass: {
                                                popup: 'rounded-4',
                                                confirmButton:
                                                    'btn btn-info rounded-2 px-4',
                                            },
                                            buttonsStyling: false,
                                        });
                                    } else {
                                        const availabilityNote =
                                            data.note ||
                                            'Info ketersediaan tidak tersedia.';

                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Petugas pengganti tersedia',
                                            html: `<p>Ganti petugas dengan <strong>${data.name}</strong>?</p>`,
                                            showCancelButton: true,
                                            confirmButtonText: 'Ya, ganti',
                                            cancelButtonText: 'Batal',
                                            customClass: {
                                                popup: 'rounded-4',
                                                confirmButton:
                                                    'btn btn-success rounded-2 px-4 me-2',
                                                cancelButton:
                                                    'btn btn-outline-muted rounded-2 px-4',
                                            },
                                            buttonsStyling: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                petugasBaruCell.textContent =
                                                    data.name;
                                                petugasBaruCell.title =
                                                    availabilityNote;

                                                btn.dataset.newInspectorId =
                                                    data.inspector_id;

                                                reloadCount++;
                                                btn.dataset.reloadCount =
                                                    reloadCount;

                                                setTimeout(() => {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil',
                                                        text: `Petugas berhasil diganti menjadi ${data.name}`,
                                                        timer: 1500,
                                                        showConfirmButton: false,
                                                        customClass: {
                                                            popup: 'rounded-4',
                                                            confirmButton:
                                                                'btn btn-primary rounded-2 px-4',
                                                        },
                                                        buttonsStyling: false,
                                                    });
                                                }, 500);
                                            }
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Petugas tidak ditemukan',
                                        text: 'Petugas yang tersedia tidak ditemukan.',
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton:
                                                'btn btn-warning rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat mengambil data petugas.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-danger rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                });
                            })
                            .finally(() => {
                                this.disabled = false;
                                if (icon) {
                                    icon.classList.remove(
                                        'spinner-border',
                                        'spinner-border-sm',
                                    );
                                    icon.classList.add('ti-refresh');
                                }
                            });
                    });
                });

            // Saat submit validasi, sertakan ID petugas hasil reload
            document
                .querySelectorAll('form[action*="validasi"]')
                .forEach((form) => {
                    form.addEventListener('submit', function () {
                        const row = this.closest('tr');
                        const reloadBtn = row.querySelector(
                            '.btn-reload-inspector',
                        );
                        const reloadIdInput =
                            this.querySelector('.reload-id-input');

                        if (reloadBtn) {
                            if (
                                reloadIdInput &&
                                reloadBtn.dataset.newInspectorId
                            ) {
                                reloadIdInput.value =
                                    reloadBtn.dataset.newInspectorId;
                            }
                            reloadBtn.dataset.reloadCount = 0;
                            reloadBtn.dataset.newInspectorId = '';
                        }
                    });
                });

            // Konfirmasi hapus dengan SweetAlert2
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
                            cancelButton:
                                'btn btn-outline-muted rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]',
                                    ).content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    Accept: 'application/json',
                                },
                                body: new URLSearchParams(new FormData(form)),
                            })
                                .then(async (response) => {
                                    const data = await response.json();
                                    if (!response.ok)
                                        throw new Error(
                                            data.message ||
                                                'Gagal menghapus data',
                                        );
                                    return data;
                                })
                                .then((data) => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text:
                                            data.message ||
                                            'Data petugas berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton:
                                                'btn btn-primary rounded-2 px-4',
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
                                        text:
                                            error.message ||
                                            'Terjadi kesalahan saat menghapus.',
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton:
                                                'btn btn-primary rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    });
                                });
                        }
                    });
                });
            });

            // Auto submit filter
            document
                .getElementById('showing')
                ?.addEventListener('change', function () {
                    document.getElementById('filterForm').submit();
                });
            document
                .getElementById('filter')
                ?.addEventListener('change', function () {
                    document.getElementById('filterForm').submit();
                });

            // ======= AJAX SUBMIT VALIDASI FORM =======
            document
                .querySelectorAll('form.validation-form')
                .forEach((form) => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const url = this.action;
                        const formData = new FormData(this);
                        const submitBtn = this.querySelector(
                            'button[type="submit"]',
                        );
                        if (submitBtn) submitBtn.disabled = true;

                        fetch(url, {
                            method: 'POST', // ganti dari PUT jadi POST karena pakai spoof method di form
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                            },
                            body: formData,
                        })
                            .then(async (res) => {
                                const data = await res.json();
                                if (!res.ok)
                                    throw new Error(
                                        data.message || 'Terjadi kesalahan',
                                    );
                                return data;
                            })
                            .then((data) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text:
                                        data.message ||
                                        'Status permintaan berhasil diperbarui.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch((err) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text:
                                        err.message ||
                                        'Terjadi kesalahan saat memproses permintaan.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                });
                            })
                            .finally(() => {
                                if (submitBtn) submitBtn.disabled = false;
                            });
                    });
                });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("admin.layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/inspection_schedule.blade.php ENDPATH**/ ?>