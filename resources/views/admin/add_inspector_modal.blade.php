<!-- Modal Tambah Petugas -->
<div
    class="modal fade"
    id="tambahPetugasModal"
    tabindex="-1"
    aria-labelledby="tambahPetugasLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="tambahPetugasLabel">
                    Tambah Petugas
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form method="POST" action="{{ route("admin.petugas.store") }}">
                @csrf
                <div class="modal-body pt-2 mt-2">
                    <!-- Nama & NIP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input
                                type="text"
                                name="nip"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                    </div>

                    <!-- Gender & Portofolio -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select
                                name="gender"
                                class="form-select rounded-2 bg-white"
                                required
                            >
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Portofolio</label>
                            <select
                                id="portfolioSelect"
                                class="form-select rounded-2 bg-white"
                                name="portfolio_id"
                                required
                            >
                                <option value="">Pilih Portofolio</option>
                                @foreach ($portfolios as $portfolio)
                                    <option
                                        value="{{ $portfolio->portfolio_id }}"
                                    >
                                        {{ $portfolio->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Telepon & Email -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input
                                type="text"
                                name="phone_num"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 mt-2">
                    <button
                        type="submit"
                        class="btn btn-success rounded-2 px-4 w-100"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Reset Form & SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tambahPetugasModal =
            document.getElementById('tambahPetugasModal');
        if (!tambahPetugasModal) return;

        const form = tambahPetugasModal.querySelector('form');
        const portfolioSelect = document.getElementById('portfolioSelect');
        const allOptions = Array.from(portfolioSelect.options).filter(
            (o) => o.value !== '',
        );

        // Reset form saat modal ditutup
        tambahPetugasModal.addEventListener('hidden.bs.modal', function () {
            form.reset();
            portfolioSelect.innerHTML =
                '<option value="">Pilih Portofolio</option>';
            allOptions.forEach((option) => portfolioSelect.appendChild(option));
        });

        // Submit AJAX + SweetAlert
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
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
                            data.message ||
                            'Data petugas berhasil ditambahkan.',
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        showConfirmButton: false,
                    });
                    bootstrap.Modal.getInstance(tambahPetugasModal).hide();
                    setTimeout(() => location.reload(), 1600);
                })
                .catch((error) => {
                    bootstrap.Modal.getInstance(tambahPetugasModal).hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text:
                            error.message ||
                            'Terjadi kesalahan, silakan coba lagi.',
                        showConfirmButton: true,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                        preConfirm: () => {
                            const modal = new bootstrap.Modal(
                                tambahPetugasModal,
                            );
                            modal.show();
                        },
                    });
                });
        });
    });
</script>
