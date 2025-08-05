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
                                                ({{ $portfolio->department->name }})
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
            const modals = document.querySelectorAll('.modal');

            modals.forEach((modal_edit) => {
                const btn = modal_edit.querySelector('[id^="editSaveBtn-"]');
                const form = modal_edit.querySelector('form');
                const inputs = modal_edit.querySelectorAll('input, select');

                let isEditing = false;

                // Handle tombol Edit/Simpan hanya sekali per modal
                if (btn) {
                    btn.addEventListener('click', () => {
                        if (!isEditing) {
                            // Aktifkan semua field (kecuali email dan NIP)
                            inputs.forEach((el) => {
                                const name = el.getAttribute('name');
                                el.removeAttribute('disabled');
                            });

                            // Ubah tombol menjadi Simpan
                            btn.textContent = 'Simpan';
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-success');

                            isEditing = true;
                        } else {
                            form.submit();
                        }
                    });
                }

                // Dropdown dinamis: update Portofolio berdasarkan Bidang
                modal_edit
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

                // Saat modal ditutup, reset ke kondisi awal
                modal_edit.addEventListener('hidden.bs.modal', () => {
                    inputs.forEach((el) => {
                        const name = el.getAttribute('name');
                        if (name !== 'email' && name !== 'nip') {
                            el.setAttribute('disabled', true);
                        }
                    });

                    if (btn) {
                        btn.textContent = 'Edit Profil';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-primary');
                    }

                    isEditing = false;
                });
            });
        });
    </script>
@endpush
