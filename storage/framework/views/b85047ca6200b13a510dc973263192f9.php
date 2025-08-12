<!-- Modal Tambah Jadwal -->
<style>
    .modal-body-custom {
        padding: 0.5rem 2rem;
    }

    .modal-header-custom {
        margin-top: 1rem;
        padding: 1rem 2rem;
        border-bottom: none;
    }

    .btn-equal {
        height: 38px;
        padding: 0 1rem;
        font-size: 0.875rem;
        min-width: 80px;
    }
</style>

<div
    class="modal fade"
    id="tambahJadwalModal"
    tabindex="-1"
    aria-labelledby="tambahJadwalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header modal-header-custom">
                <h4 class="modal-title fw-semibold" id="tambahJadwalLabel">
                    Tambah Jadwal
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form
                id="formTambahJadwal"
                method="POST"
                action="<?php echo e(route("admin.jadwal.store")); ?>"
            >
                <?php echo csrf_field(); ?>
                <div class="modal-body modal-body-custom">
                    <div class="row g-3">
                        <!-- Mitra -->
                        <div class="col-md-6">
                            <label class="form-label">Mitra</label>
                            <input
                                list="partnerList"
                                class="form-control"
                                name="partner_name"
                                id="partnerInput"
                                placeholder="Ketik nama mitra..."
                                required
                            />
                            <datalist id="partnerList">
                                <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($partner->name); ?>"
                                        data-address="<?php echo e($partner->address); ?>"
                                    ></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                        </div>

                        <!-- Lokasi -->
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <input
                                type="text"
                                class="form-control"
                                id="locationField"
                                name="partner_address"
                                placeholder="Alamat Mitra atau manual"
                                required
                            />
                        </div>

                        <!-- Tanggal -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Inspeksi</label>
                            <input
                                type="date"
                                class="form-control"
                                id="startedDate"
                                name="started_date"
                                required
                            />
                        </div>

                        <!-- Portofolio -->
                        <div class="col-md-6">
                            <label class="form-label">Portofolio</label>
                            <select
                                class="form-select"
                                id="portfolioSelect"
                                name="portfolio_id"
                                required
                            >
                                <option value="">Pilih Portofolio</option>
                                <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $port): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($port->portfolio_id); ?>">
                                        <?php echo e($port->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Produk -->
                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input
                                type="text"
                                name="product_name"
                                class="form-control"
                                placeholder="Nama Produk"
                                required
                            />
                        </div>

                        <!-- Petugas -->
                        <div class="col-md-6">
                            <label class="form-label">Petugas</label>
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="inspectorDisplay"
                                    readonly
                                />
                                <input
                                    type="hidden"
                                    name="inspector_id"
                                    id="inspectorId"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-equal"
                                    id="btnReloadPetugas"
                                    disabled
                                >
                                    Reload
                                </button>
                            </div>
                            <small
                                class="text-muted d-block mt-1"
                                id="inspectorQuota"
                            >
                                Ketersediaan: -
                            </small>
                        </div>

                        <!-- Detail Produk -->
                        <div class="col-12">
                            <label class="form-label">Detail Produk</label>
                            <textarea
                                name="product_details_raw"
                                class="form-control"
                                rows="2"
                                placeholder="Contoh: Varian A, Varian B, Varian C"
                                required
                            ></textarea>
                            <small class="text-muted">
                                Pisahkan setiap detail produk dengan tanda koma
                                (,)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 mt-3 mb-2">
                    <button
                        type="submit"
                        class="btn btn-success rounded-2 px-4 w-100"
                        id="btnSimpanTambahJadwal"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const portfolioSelect = document.getElementById('portfolioSelect');
        const startedDateInput = document.getElementById('startedDate');
        const inspectorDisplay = document.getElementById('inspectorDisplay');
        const inspectorIdInput = document.getElementById('inspectorId');
        const quotaText = document.getElementById('inspectorQuota');
        const btnReload = document.getElementById('btnReloadPetugas');
        const partnerInput = document.getElementById('partnerInput');
        const locationField = document.getElementById('locationField');
        const formTambahJadwal = document.getElementById('formTambahJadwal');
        const btnSimpan = document.getElementById('btnSimpanTambahJadwal');
        const modalTambahJadwal = document.getElementById('tambahJadwalModal');

        btnReload.disabled = true;

        const today = new Date().toISOString().split('T')[0];
        startedDateInput.setAttribute('min', today);

        function checkFormValidity() {
            const requiredFields =
                formTambahJadwal.querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach((field) => {
                if (!field.value.trim()) isValid = false;
            });
            if (!inspectorIdInput.value.trim()) isValid = false;
            btnSimpan.disabled = !isValid;
        }

        const inputs = formTambahJadwal.querySelectorAll(
            'input, select, textarea',
        );
        inputs.forEach((input) => {
            input.addEventListener('input', checkFormValidity);
            input.addEventListener('change', checkFormValidity);
        });

        checkFormValidity();

        partnerInput.addEventListener('input', function () {
            const val = this.value;
            const options = document.getElementById('partnerList').options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    locationField.value = options[i].dataset.address;
                    break;
                }
            }
        });

        function updateInspectorUI(data) {
            if (data && data.inspector_id) {
                inspectorDisplay.value = data.name;
                inspectorIdInput.value = data.inspector_id;
                quotaText.textContent = `Ketersediaan: ${data.total_available} dari ${data.total_matched}`;
                btnReload.disabled = false;
            } else {
                inspectorDisplay.value = '';
                inspectorIdInput.value = '';
                quotaText.textContent = 'Ketersediaan: Tidak ditemukan';
                btnReload.disabled = true;
            }
        }

        portfolioSelect.addEventListener('change', fetchInspector);
        startedDateInput.addEventListener('change', fetchInspector);

        function fetchInspector() {
            const portfolioId = portfolioSelect.value;
            const startedDate = startedDateInput.value;
            const lastInspectorId = inspectorIdInput.value;

            if (!portfolioId || !startedDate) {
                inspectorDisplay.value = '';
                inspectorIdInput.value = '';
                quotaText.textContent = 'Ketersediaan: -';
                return;
            }

            quotaText.textContent = 'Ketersediaan: Memuat...';

            fetch(
                `/admin/get-inspector?portfolio_id=${portfolioId}&started_date=${startedDate}&last_inspector_id=${lastInspectorId}`,
            )
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok)
                        throw new Error(
                            data.error || 'Terjadi kesalahan server.',
                        );
                    updateInspectorUI(data);
                })
                .catch((err) => {
                    console.error('Fetch error:', err);
                    quotaText.textContent = 'Ketersediaan: Tidak ditemukan';
                    alert(err.message);
                });
        }

        let reloadClickCount = 0;
        btnReload.addEventListener('click', function () {
            if (reloadClickCount >= 2) {
                alert('Petugas hanya bisa diacak maksimal 2 kali.');
                btnReload.disabled = true;
                return;
            }

            const portfolioId = portfolioSelect.value;
            const startedDate = startedDateInput.value;

            if (!portfolioId || !startedDate) {
                alert(
                    'Silakan pilih portofolio dan tanggal inspeksi terlebih dahulu.',
                );
                return;
            }

            fetchInspector();
            reloadClickCount++;
        });

        modalTambahJadwal.addEventListener('hidden.bs.modal', function () {
            formTambahJadwal.reset();
            inspectorDisplay.value = '';
            inspectorIdInput.value = '';
            quotaText.textContent = 'Ketersediaan: -';
            btnReload.disabled = true;
            checkFormValidity();

            // Reload halaman ketika modal ditutup
            location.reload();
        });

        formTambahJadwal.addEventListener('submit', function (e) {
            e.preventDefault();

            const requiredFields =
                formTambahJadwal.querySelectorAll('[required]');
            let allFilled = true;
            requiredFields.forEach((field) => {
                if (!field.value.trim()) allFilled = false;
            });

            if (!inspectorIdInput.value.trim() || !allFilled) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form belum lengkap',
                    text: 'Semua field wajib diisi dan petugas harus dipilih.',
                });
                return;
            }

            const url = this.action;
            const formData = new FormData(this);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    Accept: 'application/json',
                },
                body: formData,
            })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok)
                        throw new Error(
                            data.message || 'Terjadi kesalahan server.',
                        );
                    return data;
                })
                .then((data) => {
                    // Tutup modal dulu
                    const bsModal =
                        bootstrap.Modal.getInstance(modalTambahJadwal);
                    bsModal.hide();

                    // Reset form & UI
                    this.reset();
                    inspectorDisplay.value = '';
                    inspectorIdInput.value = '';
                    quotaText.textContent = 'Ketersediaan: -';
                    btnReload.disabled = true;
                    reloadClickCount = 0;
                    checkFormValidity();

                    // Setelah modal tertutup, munculkan SweetAlert
                    // Gunakan event 'hidden.bs.modal' agar SweetAlert muncul setelah modal benar-benar tertutup
                    modalTambahJadwal.addEventListener(
                        'hidden.bs.modal',
                        function onModalHidden() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text:
                                    data.message || 'Jadwal berhasil disimpan!',
                                timer: 2000,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                            // Hapus event listener supaya tidak jalan berulang
                            modalTambahJadwal.removeEventListener(
                                'hidden.bs.modal',
                                onModalHidden,
                            );
                        },
                        { once: true },
                    );
                })
                .catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text:
                            error.message ||
                            'Terjadi kesalahan saat menyimpan data.',
                    });
                });
        });
    });
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/add_schedule_modal.blade.php ENDPATH**/ ?>