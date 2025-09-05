@push("styles")
    <style>
        .detail-schedule .detail-btn-wrapper {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .detail-schedule .detail-select {
            min-width: 200px;
        }
    </style>
@endpush

@foreach ($schedules as $index => $schedule)
    <div
        class="modal fade detail-schedule"
        id="detailScheduleModal-{{ $index }}"
        tabindex="-1"
        aria-labelledby="detailScheduleModalLabel-{{ $index }}"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3">
                <form
                    method="POST"
                    action="{{ route("admin.jadwal.update", $schedule["id"]) }}"
                    class="editable-form"
                    data-form-index="{{ $index }}"
                    data-original-detail="{{ json_encode($schedule["detail_produk"]) }}"
                >
                    @csrf
                    @method("PUT")

                    <div
                        class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center"
                    >
                        <h4
                            class="modal-title fw-bold"
                            id="detailScheduleModalLabel-{{ $index }}"
                        >
                            Detail Jadwal Inspeksi
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
                            @php
                                $fields = [
                                    ["label" => "Nomor Surat", "value" => $schedule["nomor_surat"], "name" => "nomor_surat", "readonly" => true],
                                    ["label" => "Tanggal Surat", "value" => $schedule["tanggal_surat"], "name" => "tanggal_surat", "readonly" => true],
                                    ["label" => "Mitra", "value" => $schedule["nama_mitra"], "name" => "nama_mitra"],
                                    ["label" => "Tanggal Inspeksi", "value" => $schedule["tanggal_inspeksi"], "name" => "tanggal_inspeksi"],
                                    ["label" => "Alamat", "value" => $schedule["lokasi"], "name" => "lokasi"],
                                    ["label" => "Petugas", "value" => $schedule["nama_petugas"], "name" => "nama_petugas", "readonly" => true],
                                    ["label" => "Produk", "value" => $schedule["produk"], "name" => "produk"],
                                    ["label" => "Portofolio", "value" => $schedule["portofolio"], "name" => "portofolio", "readonly" => true],
                                ];
                            @endphp

                            @foreach ($fields as $f)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ $f["label"] }}
                                    </label>
                                    <input
                                        type="{{ in_array($f["name"], ["tanggal_inspeksi", "tanggal_surat"]) ? "date" : "text" }}"
                                        class="form-control text-muted bg-light"
                                        name="{{ $f["name"] }}"
                                        value="{{ $f["value"] }}"
                                        {{ $f["readonly"] ?? false ? "readonly" : "" }}
                                        id="{{ $f["name"] == "tanggal_inspeksi" ? "tanggalInspeksi-" . $index : "" }}"
                                    />
                                </div>
                            @endforeach

                            <div
                                class="col-md-12 mt-2 d-flex align-items-center justify-content-between"
                            >
                                <label class="form-label m-0 mb-2">
                                    Detail Produk
                                </label>
                                <div
                                    class="detail-btn-wrapper"
                                    style="display: none"
                                >
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-outline-primary dropdown-toggle"
                                            type="button"
                                            id="dropdownDetail-{{ $index }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                        >
                                            Tambah Detail
                                        </button>
                                        <ul
                                            class="dropdown-menu"
                                            aria-labelledby="dropdownDetail-{{ $index }}"
                                        >
                                            <li>
                                                <a
                                                    class="dropdown-item choose-detail"
                                                    href="#"
                                                >
                                                    Pilih dari daftar
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    class="dropdown-item manual-detail"
                                                    href="#"
                                                >
                                                    Tambah manual
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="detail-produk-wrapper">
                                @php
                                    $details = is_array($schedule["detail_produk"])
                                        ? $schedule["detail_produk"]
                                        : [];
                                @endphp

                                @forelse ($details as $detail)
                                    @php
                                        $detailArr = is_array($detail) ? $detail : ["id" => "", "name" => $detail];
                                    @endphp

                                    <div
                                        class="input-group mb-2 detail-produk-group"
                                    >
                                        <input
                                            type="text"
                                            class="form-control text-muted bg-light"
                                            name="detail_produk[]"
                                            value="{{ $detailArr["id"] ?: $detailArr["name"] }}"
                                            readonly
                                            required
                                            data-id="{{ $detailArr["id"] }}"
                                        />
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger remove-detail-btn"
                                            style="display: none"
                                        >
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @empty
                                    <div
                                        class="input-group mb-2 detail-produk-group"
                                    >
                                        <input
                                            type="text"
                                            class="form-control text-muted"
                                            value="-"
                                            readonly
                                        />
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button
                            type="button"
                            class="btn btn-primary w-100 toggle-edit-btn"
                            data-form-index="{{ $index }}"
                        >
                            Edit Jadwal
                        </button>
                        <button
                            type="submit"
                            class="btn btn-success w-100 d-none save-btn mt-2"
                            data-form-index="{{ $index }}"
                            disabled
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@push("scripts")
    <script>
        const allDetailOptions = @json($allDetailProduk);

        document.querySelectorAll('.editable-form').forEach((form) => {
            const modal = form.closest('.modal');

            // Toggle edit
            form.querySelector('.toggle-edit-btn')?.addEventListener(
                'click',
                function () {
                    const index = this.dataset.formIndex;
                    const saveBtn = form.querySelector('.save-btn');
                    const btnWrapper = form.querySelector(
                        '.detail-btn-wrapper',
                    );
                    const wrapper = form.querySelector(
                        '.detail-produk-wrapper',
                    );
                    const inputs = form.querySelectorAll('input[name]');
                    const tanggalInput = form.querySelector(
                        '#tanggalInspeksi-' + index,
                    );

                    inputs.forEach((input) => {
                        if (
                            ![
                                'nomor_surat',
                                'tanggal_surat',
                                'nama_petugas',
                                'portofolio',
                            ].includes(input.name)
                        ) {
                            input.removeAttribute('readonly');
                            input.classList.remove('text-muted', 'bg-light');
                        }
                    });

                    if (tanggalInput) {
                        const today = new Date();
                        tanggalInput.setAttribute(
                            'min',
                            today.toISOString().split('T')[0],
                        );
                    }

                    btnWrapper.style.display = 'flex';
                    this.classList.add('d-none');
                    saveBtn.classList.remove('d-none');
                    saveBtn.disabled = false;

                    const updateRemoveButtons = () => {
                        wrapper
                            .querySelectorAll('.detail-produk-group')
                            .forEach((group) => {
                                const btn =
                                    group.querySelector('.remove-detail-btn');
                                btn.style.display =
                                    wrapper.querySelectorAll(
                                        '.detail-produk-group',
                                    ).length > 1
                                        ? 'inline-block'
                                        : 'none';
                            });
                    };

                    wrapper
                        .querySelectorAll('.remove-detail-btn')
                        .forEach((btn) => {
                            btn.addEventListener('click', function () {
                                this.closest('.detail-produk-group').remove();
                                saveBtn.disabled = false;
                                updateRemoveButtons();
                            });
                        });

                    if (!btnWrapper.dataset.bound) {
                        // Dropdown detail
                        btnWrapper
                            .querySelector('.choose-detail')
                            .addEventListener('click', (e) => {
                                e.preventDefault();
                                const group = document.createElement('div');
                                group.className =
                                    'input-group mb-2 detail-produk-group';
                                group.innerHTML = `
<select class="form-select detail-select" name="detail_produk[]" required>
<option value="" disabled selected>Pilih Detail Produk</option>
${allDetailOptions.map((opt) => `<option value="${opt.detail_id}">${opt.name}</option>`).join('')}
</select>
<button type="button" class="btn btn-outline-danger remove-detail-btn"><i class="ti ti-x"></i></button>`;
                                wrapper.appendChild(group);
                                group
                                    .querySelector('.remove-detail-btn')
                                    .addEventListener('click', () =>
                                        group.remove(),
                                    );
                            });

                        btnWrapper
                            .querySelector('.manual-detail')
                            .addEventListener('click', (e) => {
                                e.preventDefault();
                                const group = document.createElement('div');
                                group.className =
                                    'input-group mb-2 detail-produk-group';
                                group.innerHTML = `
<input type="text" class="form-control" name="detail_produk[]" placeholder="Masukkan detail baru" required/>
<button type="button" class="btn btn-outline-danger remove-detail-btn"><i class="ti ti-x"></i></button>`;
                                wrapper.appendChild(group);
                                group
                                    .querySelector('.remove-detail-btn')
                                    .addEventListener('click', () =>
                                        group.remove(),
                                    );
                            });

                        btnWrapper.dataset.bound = 'true';
                    }

                    updateRemoveButtons();
                },
            );

            // AJAX submit + SweetAlert
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const saveBtn = form.querySelector('.save-btn');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                saveBtn.disabled = true;
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
                            throw new Error(
                                data.message || 'Gagal menyimpan data',
                            );
                        return data;
                    })
                    .then((data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Jadwal berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' },
                            buttonsStyling: false,
                        });
                        modalInstance?.hide();
                        setTimeout(() => location.reload(), 1600);
                    })
                    .catch((error) => {
                        modalInstance?.hide();
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
                    })
                    .finally(() => (saveBtn.disabled = false));
            });

            // Reset modal saat ditutup
            modal.addEventListener('hidden.bs.modal', function () {
                const saveBtn = form.querySelector('.save-btn');
                const toggleBtn = form.querySelector('.toggle-edit-btn');
                const btnWrapper = form.querySelector('.detail-btn-wrapper');
                const wrapper = form.querySelector('.detail-produk-wrapper');

                form.querySelectorAll('input[name]').forEach((input) => {
                    input.setAttribute('readonly', true);
                    input.classList.add('text-muted', 'bg-light');
                });

                const originalDetails = JSON.parse(
                    form.dataset.originalDetail || '[]',
                );
                wrapper.innerHTML = '';
                if (originalDetails.length) {
                    originalDetails.forEach((detail) => {
                        const group = document.createElement('div');
                        group.className =
                            'input-group mb-2 detail-produk-group';
                        const detailArr =
                            isNaN(detail) && typeof detail === 'string'
                                ? { id: '', name: detail }
                                : detail;
                        group.innerHTML = `<input type="text" class="form-control text-muted bg-light" name="detail_produk[]" value="${detailArr.name}" readonly required data-id="${detailArr.id}"/>
<button type="button" class="btn btn-outline-danger remove-detail-btn" style="display:none"><i class="ti ti-x"></i></button>`;
                        wrapper.appendChild(group);
                    });
                } else {
                    wrapper.innerHTML = `<input type="text" class="form-control text-muted" value="-" readonly />`;
                }

                toggleBtn.classList.remove('d-none');
                saveBtn.classList.add('d-none');
                saveBtn.disabled = true;
                btnWrapper.style.display = 'none';
                delete btnWrapper.dataset.bound;
            });
        });
    </script>
@endpush
