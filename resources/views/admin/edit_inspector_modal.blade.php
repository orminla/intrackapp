{{-- Modal Edit per Petugas --}}
@foreach ($inspectors as $inspector)
    <div
        class="modal fade"
        id="updateModal-{{ $inspector["nip"] }}"
        tabindex="-1"
        aria-labelledby="updateModalLabel-{{ $inspector["nip"] }}"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3">
                <form
                    action="{{ route("admin.petugas.update", $inspector["nip"]) }}"
                    method="POST"
                >
                    @csrf
                    @method("PUT")

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4
                            class="modal-title fw-bold"
                            id="updateModalLabel-{{ $inspector["nip"] }}"
                        >
                            Edit Data Petugas
                        </h4>

                        <div class="d-flex gap-2 align-items-center">
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"
                            ></button>
                        </div>
                    </div>

                    <div class="modal-body pt-2 mt-2 text-start">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    value="{{ $inspector["name"] }}"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="nip"
                                    value="{{ $inspector["nip"] }}"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    value="{{ $inspector["email"] }}"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="phone_num"
                                    value="{{ $inspector["phone_num"] }}"
                                    required
                                    disabled
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select
                                    name="gender"
                                    class="form-select"
                                    required
                                    disabled
                                >
                                    <option value="">Pilih</option>
                                    <option
                                        value="Laki-laki"
                                        {{ ($inspector["gender"] ?? "") == "Laki-laki" ? "selected" : "" }}
                                    >
                                        Laki-laki
                                    </option>
                                    <option
                                        value="Perempuan"
                                        {{ ($inspector["gender"] ?? "") == "Perempuan" ? "selected" : "" }}
                                    >
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            {{-- Dropdown Departemen --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bidang</label>
                                <select
                                    name="department_id"
                                    class="form-select department-select"
                                    data-target="portfolio-select-{{ $inspector["nip"] }}"
                                    required
                                    disabled
                                >
                                    @foreach ($departments as $dept)
                                        <option
                                            value="{{ $dept->department_id }}"
                                            {{ $dept->name == $inspector["department"] ? "selected" : "" }}
                                        >
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dropdown Portofolio --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Portofolio</label>
                                <select
                                    name="portfolio_id"
                                    id="portfolio-select-{{ $inspector["nip"] }}"
                                    class="form-select portfolio-select"
                                    required
                                    disabled
                                >
                                    @foreach ($portfolios as $portfolio)
                                        @if ($portfolio->department->name == $inspector["department"])
                                            <option
                                                value="{{ $portfolio->portfolio_id }}"
                                                {{ $portfolio->portfolio_id == $inspector["portfolio_id"] ? "selected" : "" }}
                                            >
                                                {{ $portfolio->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 mt-2">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button
                                type="button"
                                class="btn btn-primary w-100"
                                id="editSaveBtn-{{ $inspector["nip"] }}"
                            >
                                Edit Profil
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@push("scripts")
    <script>
        const allPortfolios = @json($portfolios);

        document.addEventListener('DOMContentLoaded', () => {
            const allPortfolios = @json($portfolios);

            document.querySelectorAll('.modal').forEach((modalEdit) => {
                if (!modalEdit.id.startsWith('updateModal-')) return;

                const btn = modalEdit.querySelector('[id^="editSaveBtn-"]');
                const form = modalEdit.querySelector('form');
                const inputs = modalEdit.querySelectorAll('input, select');

                let isEditing = false;

                btn?.addEventListener('click', () => {
                    if (!isEditing) {
                        // Aktifkan semua input/select kecuali email & nip tetap disable jika mau
                        inputs.forEach((el) => {
                            el.removeAttribute('disabled');
                        });

                        btn.textContent = 'Simpan';
                        btn.classList.replace('btn-primary', 'btn-success');
                        isEditing = true;
                    } else {
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
                                    throw new Error(
                                        data.message ||
                                            'Gagal memperbarui data',
                                    );
                                return data;
                            })
                            .then((data) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text:
                                        data.message ||
                                        'Data petugas berhasil diperbarui.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                    },
                                    buttonsStyling: false,
                                });

                                window.location.reload();

                                // Tutup modal otomatis
                                const modalInstance =
                                    bootstrap.Modal.getInstance(modalEdit);
                                if (modalInstance) modalInstance.hide();

                                // Disable input kembali
                                inputs.forEach((el) => {
                                    el.setAttribute('disabled', true);
                                });

                                btn.textContent = 'Edit Profil';
                                btn.classList.replace(
                                    'btn-success',
                                    'btn-primary',
                                );
                                isEditing = false;
                            })
                            .catch((error) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text:
                                        error.message ||
                                        'Terjadi kesalahan, silakan coba lagi.',
                                    confirmButtonText: 'OK',
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

                // Dropdown dinamis untuk Bidang -> Portofolio
                modalEdit
                    .querySelectorAll('.department-select')
                    .forEach((select) => {
                        select.addEventListener('change', function () {
                            const deptId = this.value;
                            const targetId = this.getAttribute('data-target');
                            const targetSelect =
                                document.getElementById(targetId);

                            if (targetSelect) {
                                targetSelect.innerHTML = ''; // Kosongkan dulu
                                allPortfolios.forEach((portfolio) => {
                                    if (
                                        portfolio.department.department_id ==
                                        deptId
                                    ) {
                                        const opt =
                                            document.createElement('option');
                                        opt.value = portfolio.portfolio_id;
                                        opt.textContent = `${portfolio.name} (${portfolio.department.name})`;
                                        targetSelect.appendChild(opt);
                                    }
                                });
                            }
                        });
                    });

                // Reset form & tombol saat modal ditutup
                modalEdit.addEventListener('hidden.bs.modal', () => {
                    inputs.forEach((el) => {
                        el.setAttribute('disabled', true);
                    });
                    btn.textContent = 'Edit Profil';
                    btn.classList.replace('btn-success', 'btn-primary');
                    isEditing = false;
                });
            });
        });
    </script>
@endpush
