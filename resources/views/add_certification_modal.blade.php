<!-- Modal Tambah Sertifikasi -->
<div
    class="modal fade"
    id="tambahCertifModal"
    tabindex="-1"
    aria-labelledby="tambahCertifLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="tambahCertifLabel">
                    Tambah Sertifikasi
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form
                method="POST"
                action="{{ route("certifications.store") }}"
                enctype="multipart/form-data"
            >
                @csrf
                <div class="modal-body pt-2 mt-2">
                    <div class="row g-3 mb-3">
                        <!-- Nama Sertifikasi -->
                        <div class="col-md-6">
                            <label class="form-label">Nama Sertifikasi</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>

                        <!-- Penerbit -->
                        <div class="col-md-6">
                            <label class="form-label">Penerbit</label>
                            <input
                                type="text"
                                name="issuer"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Tanggal Terbit -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Terbit</label>
                            <input
                                type="date"
                                name="issued_at"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>

                        <!-- Tanggal Kadaluarsa (opsional) -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Kadaluarsa</label>
                            <input
                                type="date"
                                name="expired_at"
                                class="form-control rounded-2 bg-white"
                            />
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input
                            type="file"
                            name="file_path"
                            class="form-control rounded-2 bg-white"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        />
                    </div>

                    <!-- Inspector ID (hidden) -->
                    <input
                        type="hidden"
                        name="inspector_id"
                        value="{{ $profile["inspector_id"] ?? "" }}"
                    />

                    <!-- Portofolio terkait (opsional) -->
                    <div class="mb-3">
                        <label class="form-label">Portofolio</label>
                        <select
                            name="portfolio_id"
                            class="form-select rounded-2 bg-white"
                        >
                            <option value="">Tidak terkait</option>
                            @foreach ($allPortfolios as $portfolio)
                                <option value="{{ $portfolio->portfolio_id }}">
                                    {{ $portfolio->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 mt-2">
                    <button
                        type="submit"
                        class="btn btn-success rounded-2 px-4 w-100"
                        disabled
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
        const modal = document.getElementById('tambahCertifModal');
        if (!modal) return;

        const form = modal.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');

        function checkFormValidity() {
            // hanya cek input wajib
            const requiredElements = form.querySelectorAll('input[required]');
            for (const el of requiredElements) {
                if (!el.value || el.value.trim() === '') return false;
            }
            return true;
        }

        function updateSubmitBtn() {
            submitBtn.disabled = !checkFormValidity();
        }

        // Update tombol submit saat input/change
        const requiredElements = form.querySelectorAll(
            'input[required], select[required]',
        );
        requiredElements.forEach((el) => {
            el.addEventListener('input', updateSubmitBtn);
            el.addEventListener('change', updateSubmitBtn);
        });

        submitBtn.disabled = true;

        // AJAX Submit + SweetAlert
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!checkFormValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const modalInstance = bootstrap.Modal.getInstance(modal);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok)
                        throw new Error(data.message || 'Gagal menyimpan data');
                    return data;
                })
                .then((data) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text:
                            data.message || 'Sertifikasi berhasil ditambahkan.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' },
                        buttonsStyling: false,
                    });
                    if (modalInstance) modalInstance.hide();
                    setTimeout(() => location.reload(), 1600);
                })
                .catch((error) => {
                    if (modalInstance) modalInstance.hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text:
                            error.message ||
                            'Terjadi kesalahan, silakan periksa data.',
                        showConfirmButton: true,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                        preConfirm: () => {
                            const m = new bootstrap.Modal(modal);
                            m.show();
                        },
                    });
                });
        });
    });
</script>
