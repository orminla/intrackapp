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
                        <!-- No Surat -->
                        <div class="col-md-6">
                            <label class="form-label">No Surat</label>
                            <input
                                type="text"
                                class="form-control"
                                id="letterNumberInput"
                                name="letter_number"
                                placeholder="Masukkan Nomor Surat Permintaan Inspeksi"
                                required
                            />
                            <small
                                id="letterNumberStatus"
                                class="d-block mt-1"
                            ></small>
                        </div>

                        <!-- Tanggal Surat -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Surat</label>
                            <input
                                type="date"
                                class="form-control"
                                id="letterDateInput"
                                name="letter_date"
                                readonly
                            />
                        </div>

                        <!-- Mitra -->
                        <div class="col-md-6">
                            <label class="form-label">Mitra</label>
                            <input
                                type="text"
                                class="form-control"
                                id="partnerInput"
                                name="partner_name"
                                readonly
                            />
                        </div>

                        <!-- Lokasi -->
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <input
                                type="text"
                                class="form-control"
                                id="locationField"
                                name="partner_address"
                                readonly
                            />
                        </div>

                        <!-- Tanggal Inspeksi -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Inspeksi</label>
                            <input
                                type="date"
                                class="form-control"
                                id="startedDate"
                                name="started_date"
                                readonly
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
                                id="productNameInput"
                                readonly
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
                        <div class="col-12" id="productDetailsWrapper">
                            <label class="form-label">Detail Produk</label>
                        </div>
                        <input
                            type="hidden"
                            name="product_details_raw"
                            id="productDetailsRaw"
                        />
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
        const modalTambahJadwal = document.getElementById('tambahJadwalModal');

        btnReload.disabled = true;

        // 🔹 Batas minimal tanggal inspeksi = hari ini
        const today = new Date().toISOString().split('T')[0];
        startedDateInput.setAttribute('min', today);

        // 🔹 Field nomor surat
        const letterNumberInput = document.getElementById('letterNumberInput');
        const letterDateInput = document.getElementById('letterDateInput');
        const letterNumberStatus =
            document.getElementById('letterNumberStatus');
        const detailWrapper = document.getElementById('productDetailsWrapper');
        const productInput = formTambahJadwal.querySelector(
            'input[name="product_name"]',
        );

        function resetLetterFields() {
            letterNumberStatus.textContent = '';
            letterNumberStatus.className = '';
            letterDateInput.value = '';
            partnerInput.value = '';
            locationField.value = '';
            startedDateInput.value = '';
            productInput.value = '';
            detailWrapper.innerHTML = `<label class="form-label">Detail Produk</label>`;
        }

        // 🔹 Event fetch otomatis ketika no surat diisi
        let fetchTimeout;
        letterNumberInput.addEventListener('input', function () {
            clearTimeout(fetchTimeout);
            const noSurat = this.value.trim();

            if (!noSurat) {
                resetLetterFields();
                return;
            }

            fetchTimeout = setTimeout(() => {
                fetch(
                    `https://dummyerp.nirmaladev.my.id/${encodeURIComponent(noSurat)}`,
                )
                    .then((res) => res.json())
                    .then((data) => {
                        if (data && data.letter_number) {
                            //surat ditemukan
                            letterNumberStatus.textContent =
                                'Surat permintaan inspeksi tersedia';
                            letterNumberStatus.className =
                                'text-success d-block mt-1';

                            letterDateInput.value = data.letter_date || '';
                            partnerInput.value = data.partner_name || '';
                            locationField.value = data.partner_address || '';
                            startedDateInput.value = data.started_date || '';
                            productInput.value = data.product_name || '';

                            detailWrapper.innerHTML = `<label class="form-label">Detail Produk</label>`;
                            if (Array.isArray(data.product_details)) {
                                data.product_details.forEach((detail, idx) => {
                                    const div = document.createElement('div');
                                    div.classList.add('mb-2');
                                    const input =
                                        document.createElement('input');
                                    input.type = 'text';
                                    input.name = 'product_details[]';
                                    input.classList.add('form-control');
                                    input.value = detail;
                                    input.placeholder = `Detail Produk ${idx + 1}`;
                                    div.appendChild(input);
                                    detailWrapper.appendChild(div);
                                });
                            }
                        } else {
                            //surat tidak ditemukan
                            resetLetterFields();
                            letterNumberStatus.textContent = `Surat permintaan dengan nomor "${noSurat}" tidak ditemukan`;
                            letterNumberStatus.className =
                                'text-danger d-block mt-1';
                        }
                    })
                    .catch((err) => {
                        console.error('Gagal fetch data API:', err);
                        resetLetterFields();
                        letterNumberStatus.textContent =
                            'Gagal mengambil data dari server';
                        letterNumberStatus.className =
                            'text-warning d-block mt-1';
                    });
            }, 100);
        });

        // 🔹 Update inspector UI
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

        // 🔹 Reload inspector max 2 kali
        let reloadClickCount = 0;
        btnReload.addEventListener('click', function () {
            if (reloadClickCount >= 2) {
                alert('Petugas hanya bisa diacak maksimal 2 kali.');
                btnReload.disabled = true;
                return;
            }

            if (!portfolioSelect.value || !startedDateInput.value) {
                alert(
                    'Silakan pilih portofolio dan tanggal inspeksi terlebih dahulu.',
                );
                return;
            }

            fetchInspector();
            reloadClickCount++;
        });

        // 🔹 Reset modal
        modalTambahJadwal.addEventListener('hidden.bs.modal', function () {
            formTambahJadwal.reset();
            resetLetterFields();
            inspectorDisplay.value = '';
            inspectorIdInput.value = '';
            quotaText.textContent = 'Ketersediaan: -';
            btnReload.disabled = true;
            reloadClickCount = 0;
        });

        // 🔹 Submit form
        formTambahJadwal.addEventListener('submit', function (e) {
            e.preventDefault();

            const detailInputs = document.querySelectorAll(
                '#productDetailsWrapper input[name="product_details[]"]',
            );
            const detailsArray = Array.from(detailInputs)
                .map((input) => input.value.trim())
                .filter((v) => v);
            document.getElementById('productDetailsRaw').value =
                detailsArray.join(',');

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
                    const bsModal =
                        bootstrap.Modal.getInstance(modalTambahJadwal);
                    bsModal.hide();

                    this.reset();
                    resetLetterFields();
                    inspectorDisplay.value = '';
                    inspectorIdInput.value = '';
                    quotaText.textContent = 'Ketersediaan: -';
                    btnReload.disabled = true;
                    reloadClickCount = 0;

                    modalTambahJadwal.addEventListener(
                        'hidden.bs.modal',
                        function onModalHidden() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text:
                                    data.message || 'Jadwal berhasil disimpan!',
                                timer: 3000,
                                showConfirmButton: false,
                            }).then(() => location.reload());

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