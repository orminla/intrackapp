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
                action="{{ route("admin.jadwal.store") }}"
            >
                @csrf
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
                                @foreach ($partners as $partner)
                                    <option
                                        value="{{ $partner->name }}"
                                        data-address="{{ $partner->address }}"
                                    ></option>
                                @endforeach
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
                                @foreach ($portfolios as $port)
                                    <option value="{{ $port->portfolio_id }}">
                                        {{ $port->name }}
                                    </option>
                                @endforeach
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

        btnReload.disabled = true;

        // Batas minimum tanggal hari ini
        const today = new Date().toISOString().split('T')[0];
        startedDateInput.setAttribute('min', today);

        // Cek kelengkapan field
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

        formTambahJadwal.addEventListener('submit', function (e) {
            const requiredFields =
                formTambahJadwal.querySelectorAll('[required]');
            let allFilled = true;

            requiredFields.forEach((field) => {
                if (!field.value.trim()) allFilled = false;
            });

            if (!inspectorIdInput.value.trim() || !allFilled) {
                e.preventDefault();
                alert('Semua field wajib diisi dan petugas harus dipilih.');
            }
        });

        // Input listener untuk validasi otomatis
        const inputs = formTambahJadwal.querySelectorAll(
            'input, select, textarea',
        );
        inputs.forEach((input) => {
            input.addEventListener('input', checkFormValidity);
            input.addEventListener('change', checkFormValidity);
        });

        checkFormValidity();

        // Auto isi alamat dari datalist
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

        // Tampilkan data petugas ke UI
        function updateInspectorUI(data) {
            if (data && data.inspector_id) {
                inspectorDisplay.value = data.name;
                inspectorIdInput.value = data.inspector_id;
                quotaText.textContent = `Ketersediaan: ${data.total_available} dari ${data.total_matched}`;
                btnReload.disabled = false; // ✅ Aktifkan tombol
            } else {
                inspectorDisplay.value = '';
                inspectorIdInput.value = '';
                quotaText.textContent = 'Ketersediaan: Tidak ditemukan';
                btnReload.disabled = true; // ✅ Nonaktifkan tombol
            }
        }

        // Trigger fetch saat isi input diubah
        portfolioSelect.addEventListener('change', fetchInspector);
        startedDateInput.addEventListener('change', fetchInspector);

        // Ambil petugas dari API
        // function fetchInspector() {
        //     const portfolioId = portfolioSelect.value;
        //     const startedDate = startedDateInput.value;

        //     // console.log('portfolioId:', portfolioId);
        //     // console.log('startedDate:', startedDate);

        //     if (!portfolioId || !startedDate) {
        //         inspectorDisplay.value = '';
        //         inspectorIdInput.value = '';
        //         quotaText.textContent = 'Ketersediaan: -';
        //         return;
        //     }

        //     quotaText.textContent = 'Ketersediaan: Memuat...';

        //     fetch(
        //         `/admin/get-inspector?portfolio_id=${portfolioId}&started_date=${startedDate}`,
        //     )
        //         .then(async (res) => {
        //             const data = await res.json();
        //             if (!res.ok)
        //                 throw new Error(
        //                     data.error || 'Terjadi kesalahan server.',
        //                 );
        //             updateInspectorUI(data);
        //         })
        //         .catch((err) => {
        //             console.error('Fetch error:', err);
        //             quotaText.textContent = 'Ketersediaan: Tidak ditemukan';
        //             alert(err.message);
        //         });
        // }
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

        // Tombol Reload
        let reloadClickCount = 0;

        btnReload.addEventListener('click', function () {
            if (reloadClickCount >= 2) {
                alert('Petugas hanya bisa di-*reload* maksimal 2 kali.');
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

        // Reset semua data saat modal ditutup
        const modalTambahJadwal = document.getElementById('tambahJadwalModal');
        modalTambahJadwal.addEventListener('hidden.bs.modal', function () {
            formTambahJadwal.reset(); // Reset semua field input/form
            inspectorDisplay.value = '';
            inspectorIdInput.value = '';
            quotaText.textContent = 'Ketersediaan: -';
            btnReload.disabled = true;

            // Cek ulang validasi (untuk disable tombol Simpan)
            checkFormValidity();
        });
    });
</script>
