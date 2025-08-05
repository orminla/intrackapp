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

                    <div class="row g-3">
                        <!-- Bidang -->
                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <select
                                id="departmentSelect"
                                class="form-select rounded-2 bg-white"
                                name="department_id"
                                required
                            >
                                <option value="">Pilih Bidang</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->department_id }}">
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Portofolio -->
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
                                        data-dept="{{ $portfolio->department_id }}"
                                    >
                                        {{ $portfolio->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Telepon -->
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input
                                type="text"
                                name="phone_num"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>

                        <!-- Email -->
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

<!-- Script Filtering Portofolio & Reset Form -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deptSelect = document.getElementById('departmentSelect');
        const portfolioSelect = document.getElementById('portfolioSelect');
        const form = document.querySelector('#tambahPetugasModal form');
        const submitBtn = form.querySelector('button[type="submit"]');

        const allOptions = Array.from(portfolioSelect.options).filter(
            (o) => o.value !== '',
        );

        // Fungsi cek validasi semua input required dan select required
        function checkFormValidity() {
            // Cari semua elemen input/select yang required dalam form
            const requiredElements = form.querySelectorAll(
                'input[required], select[required]',
            );

            // Cek apakah semua sudah terisi / valid
            for (const el of requiredElements) {
                // Untuk input/select harus ada value dan tidak kosong
                if (!el.value || el.value.trim() === '') {
                    return false;
                }
            }
            return true;
        }

        // Fungsi untuk update status tombol submit
        function updateSubmitBtn() {
            if (checkFormValidity()) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        deptSelect.addEventListener('change', function () {
            const selectedDept = this.value;

            // Reset pilihan portofolio
            portfolioSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih Portofolio';
            portfolioSelect.appendChild(placeholder);

            // Tampilkan opsi yang sesuai
            allOptions.forEach((option) => {
                if (option.dataset.dept === selectedDept) {
                    portfolioSelect.appendChild(option);
                }
            });

            updateSubmitBtn();
        });

        // Pantau perubahan di semua input dan select required
        const requiredElements = form.querySelectorAll(
            'input[required], select[required]',
        );
        requiredElements.forEach((el) => {
            el.addEventListener('input', updateSubmitBtn);
            el.addEventListener('change', updateSubmitBtn);
        });

        // Reset form saat modal ditutup
        const tambahPetugasModal =
            document.getElementById('tambahPetugasModal');
        tambahPetugasModal.addEventListener('hidden.bs.modal', function () {
            form.reset();

            // Reset ulang portofolio ke semua opsi awal
            portfolioSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih Portofolio';
            portfolioSelect.appendChild(placeholder);

            allOptions.forEach((option) => {
                portfolioSelect.appendChild(option);
            });

            updateSubmitBtn();
        });

        // Disable tombol submit di awal
        submitBtn.disabled = true;
    });
</script>
