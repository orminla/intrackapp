@push("style")
    <style>
        .certification-card {
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                color 0.25s ease,
                box-shadow 0.25s ease;
            cursor: pointer;
            box-sizing: border-box;
            word-wrap: break-word;
        }

        .certification-card:hover {
            background-color: #e2ebff !important;
            border-color: #e2ebff !important;
            color: #1e4db7;
        }

        .certification-card .small {
            display: block;
            width: 100%;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            word-break: break-word;
        }
    </style>
@endpush

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
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Tutup"
                        ></button>
                    </div>

                    <div class="modal-body pt-2 mt-2 text-start">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    value="{{ $inspector["name"] }}"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="nip"
                                    value="{{ $inspector["nip"] }}"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select
                                    name="gender"
                                    class="form-select"
                                    disabled
                                    required
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    value="{{ $inspector["email"] }}"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="phone_num"
                                    value="{{ $inspector["phone_num"] }}"
                                    disabled
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bidang</label>
                                <select
                                    name="department_id"
                                    class="form-select department-select"
                                    data-target="portfolio-select-{{ $inspector["nip"] }}"
                                    disabled
                                    required
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Portofolio</label>
                                <select
                                    name="portfolio_id"
                                    id="portfolio-select-{{ $inspector["nip"] }}"
                                    class="form-select portfolio-select"
                                    disabled
                                    required
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

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Sertifikasi</label>
                                <div
                                    class="certification-list"
                                    style="max-height: 200px; overflow-y: auto"
                                >
                                    @if (isset($inspector["sertifikasi"]) && $inspector["sertifikasi"]->isNotEmpty())
                                        @foreach ($inspector["sertifikasi"] as $cert)
                                            @php
                                                $certUrl = $cert->file_path ? asset("storage/" . $cert->file_path) : null;
                                                $fullPortfolioName = $cert->portfolio->name ?? "-";
                                                $portfolioParts = explode("-", $fullPortfolioName, 2);
                                                $portfolioName = $portfolioParts[1] ?? $fullPortfolioName;
                                            @endphp

                                            <div
                                                class="d-flex align-items-start mb-2"
                                            >
                                                <div
                                                    class="certification-card flex-grow-1"
                                                >
                                                    <div class="fw-bold mb-1">
                                                        {{ $cert->name }}
                                                        <span
                                                            class="text-muted"
                                                        >
                                                            &nbsp;&ndash;&nbsp;{{ $portfolioName }}
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="small text-muted"
                                                    >
                                                        Diterbitkan oleh
                                                        {{ $cert->issuer ?? "-" }}
                                                        | Berlaku:
                                                        {{ $cert->issued_at ? \Carbon\Carbon::parse($cert->issued_at)->format("d M Y") : "-" }}
                                                        hingga
                                                        {{ $cert->expired_at ? \Carbon\Carbon::parse($cert->expired_at)->format("d M Y") : "selamanya" }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="d-flex flex-column ms-2 gap-1"
                                                >
                                                    @if ($certUrl)
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-primary view-cert-btn"
                                                            data-url="{{ $certUrl }}"
                                                            title="Lihat File"
                                                        >
                                                            <i
                                                                class="ti ti-eye"
                                                            ></i>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger remove-cert-btn"
                                                            data-cert-id="{{ $cert->certification_id }}"
                                                            title="Hapus Sertifikasi"
                                                            disabled
                                                        >
                                                            <i
                                                                class="ti ti-trash"
                                                            ></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-muted">
                                            Belum ada sertifikasi
                                        </div>
                                    @endif
                                </div>
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
            document.querySelectorAll('.modal').forEach((modalEdit) => {
                if (!modalEdit.id.startsWith('updateModal-')) return;

                const btn = modalEdit.querySelector('[id^="editSaveBtn-"]');
                const form = modalEdit.querySelector('form');
                const inputs = modalEdit.querySelectorAll('input, select');
                const removeBtns =
                    modalEdit.querySelectorAll('.remove-cert-btn');
                const viewBtns = modalEdit.querySelectorAll('.view-cert-btn');

                let isEditing = false;
                let deletedCerts = [];

                // Tombol Edit Profil / Simpan
                btn?.addEventListener('click', () => {
                    if (!isEditing) {
                        inputs.forEach((el) => el.removeAttribute('disabled'));
                        removeBtns.forEach((el) =>
                            el.removeAttribute('disabled'),
                        );
                        viewBtns.forEach((el) =>
                            el.removeAttribute('disabled'),
                        );
                        btn.textContent = 'Simpan';
                        btn.classList.replace('btn-primary', 'btn-success');
                        isEditing = true;
                    } else {
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }

                        // Tambahkan sertifikasi yang dihapus
                        deletedCerts.forEach((id) => {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'deleted_certifications[]';
                            hiddenInput.value = id;
                            form.appendChild(hiddenInput);
                        });

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
                                // Tutup modal dulu
                                const bootstrapModal =
                                    bootstrap.Modal.getInstance(modalEdit);
                                bootstrapModal.hide();

                                // Setelah modal tertutup, tampilkan popup
                                modalEdit.addEventListener(
                                    'hidden.bs.modal',
                                    function handler() {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text:
                                                data.message ||
                                                'Data petugas berhasil diperbarui.',
                                            timer: 3000,
                                            showConfirmButton: false,
                                            customClass: {
                                                popup: 'rounded-4',
                                                confirmButton:
                                                    'btn btn-primary rounded-2 px-4',
                                                timerProgressBar: true,
                                            },
                                            buttonsStyling: false,
                                        });
                                        modalEdit.removeEventListener(
                                            'hidden.bs.modal',
                                            handler,
                                        );
                                        window.location.reload();
                                    },
                                );
                            })
                            .catch((error) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text:
                                        error.message ||
                                        'Terjadi kesalahan, silakan coba lagi.',
                                    customClass: {
                                        popup: 'rounded-4',
                                        confirmButton:
                                            'btn btn-primary rounded-2 px-4',
                                        timerProgressBar: true,
                                    },
                                    buttonsStyling: false,
                                });
                            });
                    }
                });

                // Dropdown Bidang -> Portofolio dinamis
                modalEdit
                    .querySelectorAll('.department-select')
                    .forEach((select) => {
                        select.addEventListener('change', function () {
                            const deptId = this.value;
                            const targetId = this.dataset.target;
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

                // Reset saat modal ditutup
                modalEdit.addEventListener('hidden.bs.modal', () => {
                    inputs.forEach((el) => el.setAttribute('disabled', true));
                    removeBtns.forEach((el) =>
                        el.setAttribute('disabled', true),
                    );
                    viewBtns.forEach((el) => el.removeAttribute('disabled')); // lihat tetap aktif
                    btn.textContent = 'Edit Profil';
                    btn.classList.replace('btn-success', 'btn-primary');
                    isEditing = false;
                    deletedCerts = [];
                    location.reload();
                });

                // Tombol lihat & hapus sertifikasi
                modalEdit.addEventListener('click', function (e) {
                    // Lihat file
                    if (e.target.closest('.view-cert-btn')) {
                        const btn = e.target.closest('.view-cert-btn');
                        const url = btn.dataset.url;
                        if (url) window.open(url, '_blank');
                    }

                    // Hapus sertifikasi hanya saat edit
                    if (isEditing && e.target.closest('.remove-cert-btn')) {
                        const btn = e.target.closest('.remove-cert-btn');
                        const cardWrapper = btn.closest(
                            '.d-flex.align-items-start.mb-2',
                        );
                        if (!cardWrapper) return;

                        cardWrapper.remove(); // Hapus dari UI
                        deletedCerts.push(btn.dataset.certId); // Simpan untuk dikirim
                    }
                });
            });
        });
    </script>
@endpush
