{{-- Modal Edit per Admin --}}
@foreach ($admins as $admin)
    <div
        class="modal fade"
        id="updateModal-{{ $admin["nip"] }}"
        tabindex="-1"
        aria-labelledby="updateModalLabel-{{ $admin["nip"] }}"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3">
                <form
                    action="{{ route("admin.pengaturan.update", $admin["nip"]) }}"
                    method="POST"
                >
                    @csrf
                    @method("PUT")

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4
                            class="modal-title fw-bold"
                            id="updateModalLabel-{{ $admin["nip"] }}"
                        >
                            Edit Data Admin
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
                                    value="{{ $admin["name"] }}"
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
                                    value="{{ $admin["nip"] }}"
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
                                    value="{{ $admin["email"] }}"
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
                                    value="{{ $admin["phone_num"] }}"
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
                                    data-target="portfolio-select-{{ $admin["nip"] }}"
                                    required
                                    disabled
                                >
                                    @foreach ($departments as $dept)
                                        <option
                                            value="{{ $dept->department_id }}"
                                            {{ $dept->name == $admin["department"] ? "selected" : "" }}
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
                                    id="portfolio-select-{{ $admin["nip"] }}"
                                    class="form-select portfolio-select"
                                    required
                                    disabled
                                >
                                    @foreach ($portfolios as $portfolio)
                                        @if ($portfolio->department->name == $admin["department"])
                                            <option
                                                value="{{ $portfolio->portfolio_id }}"
                                                {{ $portfolio->portfolio_id == $admin["portfolio_id"] ? "selected" : "" }}
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
                                id="editSaveBtn-{{ $admin["nip"] }}"
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

            modals.forEach((modal) => {
                const btn = modal.querySelector('[id^="editSaveBtn-"]');
                const form = modal.querySelector('form');
                const inputs = modal.querySelectorAll('input, select');

                let isEditing = false;

                if (btn) {
                    btn.addEventListener('click', () => {
                        if (!isEditing) {
                            // Aktifkan semua input kecuali email & nip
                            inputs.forEach((el) => {
                                const name = el.getAttribute('name');
                                el.removeAttribute('disabled');
                            });

                            btn.textContent = 'Simpan';
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-success');
                            isEditing = true;
                        } else {
                            form.submit();
                        }
                    });
                }

                // Dropdown dinamis
                modal
                    .querySelectorAll('.department-select')
                    .forEach((select) => {
                        select.addEventListener('change', function () {
                            const deptId = this.value;
                            const targetId = this.getAttribute('data-target');
                            const targetSelect =
                                document.getElementById(targetId);

                            if (targetSelect) {
                                targetSelect.innerHTML = '';

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

                modal.addEventListener('hidden.bs.modal', () => {
                    inputs.forEach((el) => {
                        const name = el.getAttribute('name');
                        el.setAttribute('disabled', true);
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
