@push("styles")
    <style>
        .add-detail-btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            display: none;
        }

        .add-detail-btn i {
            font-size: 1rem;
        }

        .modal-lg-scrollable {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
@endpush

@foreach ($schedules as $index => $schedule)
    <div
        class="modal fade"
        id="detailScheduleModal-{{ $index }}"
        tabindex="-1"
        aria-labelledby="detailScheduleModalLabel-{{ $index }}"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 p-3 modal-lg-scrollable">
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
                                    ["label" => "Mitra", "value" => $schedule["nama_mitra"], "name" => "nama_mitra"],
                                    ["label" => "Tanggal Inspeksi", "value" => $schedule["tanggal_inspeksi"], "name" => "tanggal_inspeksi"],
                                    ["label" => "Alamat", "value" => $schedule["lokasi"], "name" => "lokasi"],
                                    ["label" => "Petugas", "value" => $schedule["nama_petugas"], "name" => "nama_petugas"],
                                    ["label" => "Produk", "value" => $schedule["produk"], "name" => "produk"],
                                    ["label" => "Portofolio", "value" => $schedule["portofolio"], "name" => "portofolio"],
                                ];
                            @endphp

                            @foreach ($fields as $f)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ $f["label"] }}
                                    </label>
                                    <input
                                        type="{{ $f["name"] === "tanggal_inspeksi" ? "date" : "text" }}"
                                        class="form-control {{ in_array($f["name"], ["nama_petugas", "portofolio"]) ? "text-muted bg-light" : "" }}"
                                        name="{{ $f["name"] }}"
                                        value="{{ $f["value"] }}"
                                        {{ in_array($f["name"], ["nama_petugas", "portofolio"]) ? "readonly" : "readonly required" }}
                                        id="{{ $f["name"] === "tanggal_inspeksi" ? "tanggalInspeksi-" . $index : "" }}"
                                    />
                                </div>
                            @endforeach

                            <div class="col-md-12 mt-2">
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2"
                                >
                                    <label class="form-label m-0">
                                        Detail Produk
                                    </label>
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary d-flex align-items-center gap-2 add-detail-btn d-none"
                                        data-form-index="{{ $index }}"
                                    >
                                        <i class="ti ti-plus"></i>
                                        Tambah Detail
                                    </button>
                                </div>

                                <div class="detail-produk-wrapper">
                                    @forelse ($schedule["detail_produk"] as $detail)
                                        <div
                                            class="input-group mb-2 detail-produk-group"
                                        >
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="detail_produk[]"
                                                value="{{ $detail }}"
                                                required
                                                readonly
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
                                                disabled
                                            />
                                        </div>
                                    @endforelse
                                </div>
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
        document.querySelectorAll('.toggle-edit-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const index = this.dataset.formIndex;
                const form = document.querySelector(
                    `form[data-form-index="${index}"]`,
                );
                const inputs = form.querySelectorAll('input[name]');
                const saveBtn = form.querySelector('.save-btn');
                const addDetailBtn = form.querySelector('.add-detail-btn');
                const wrapper = form.querySelector('.detail-produk-wrapper');
                const tanggalInspeksiInput = form.querySelector(
                    `#tanggalInspeksi-${index}`,
                );

                const updateRemoveButtons = () => {
                    const groups = wrapper.querySelectorAll(
                        '.detail-produk-group',
                    );
                    groups.forEach((group) => {
                        const removeBtn =
                            group.querySelector('.remove-detail-btn');
                        if (groups.length > 1) {
                            removeBtn.style.display = 'inline-block';
                            removeBtn.disabled = false;
                        } else {
                            removeBtn.style.display = 'none';
                            removeBtn.disabled = true;
                        }
                    });
                };

                inputs.forEach((input) => {
                    const name = input.getAttribute('name');
                    if (name !== 'nama_petugas' && name !== 'portofolio') {
                        input.removeAttribute('readonly');
                    }
                });

                if (tanggalInspeksiInput) {
                    const currentDate = tanggalInspeksiInput.value; // ambil tanggal yang sudah terisi
                    if (currentDate) {
                        tanggalInspeksiInput.setAttribute('min', currentDate);
                    }
                }

                this.classList.add('d-none');
                saveBtn.classList.remove('d-none');
                saveBtn.disabled = false;
                addDetailBtn.classList.remove('d-none');
                addDetailBtn.style.display = 'inline-flex';

                wrapper
                    .querySelectorAll('.remove-detail-btn')
                    .forEach((removeBtn) => {
                        removeBtn.addEventListener('click', function () {
                            this.closest('.detail-produk-group').remove();
                            saveBtn.disabled = false;
                            updateRemoveButtons();
                        });
                    });

                if (!addDetailBtn.dataset.bound) {
                    addDetailBtn.addEventListener('click', function () {
                        const group = document.createElement('div');
                        group.className =
                            'input-group mb-2 detail-produk-group';
                        group.innerHTML = `
                        <input type="text" name="detail_produk[]" class="form-control" required />
                        <button type="button" class="btn btn-outline-danger remove-detail-btn">
                            <i class="ti ti-x"></i>
                        </button>
                    `;
                        wrapper.appendChild(group);
                        saveBtn.disabled = false;

                        group
                            .querySelector('.remove-detail-btn')
                            .addEventListener('click', function () {
                                group.remove();
                                saveBtn.disabled = false;
                                updateRemoveButtons();
                            });

                        updateRemoveButtons();
                        group.querySelector('input').focus();
                    });
                    addDetailBtn.dataset.bound = 'true';
                }

                updateRemoveButtons();
            });
        });

        document.querySelectorAll('.modal').forEach((modal) => {
            modal.addEventListener('hidden.bs.modal', function () {
                const form = modal.querySelector('form');
                if (!form) return;

                const index = form.dataset.formIndex;
                const saveBtn = form.querySelector('.save-btn');
                const editBtn = form.querySelector('.toggle-edit-btn');
                const addDetailBtn = form.querySelector('.add-detail-btn');
                const wrapper = form.querySelector('.detail-produk-wrapper');
                const tanggalInspeksiInput = form.querySelector(
                    `#tanggalInspeksi-${index}`,
                );

                form.querySelectorAll('input[name]').forEach((input) => {
                    const name = input.getAttribute('name');
                    if (
                        name !== 'nama_petugas' &&
                        name !== 'portofolio' &&
                        name !== 'tanggal_inspeksi'
                    ) {
                        input.setAttribute('readonly', true);
                    }
                });

                // Reset tanggal inspeksi input readonly dan hapus min-nya
                if (tanggalInspeksiInput) {
                    tanggalInspeksiInput.setAttribute('readonly', true);
                    tanggalInspeksiInput.removeAttribute('min');
                }

                const originalDetails = JSON.parse(
                    form.dataset.originalDetail || '[]',
                );
                wrapper.innerHTML = '';
                if (originalDetails.length) {
                    originalDetails.forEach((detail) => {
                        const group = document.createElement('div');
                        group.className =
                            'input-group mb-2 detail-produk-group';
                        group.innerHTML = `
                        <input type="text" class="form-control" name="detail_produk[]" value="${detail}" readonly required>
                        <button type="button" class="btn btn-outline-danger remove-detail-btn" style="display: none">
                            <i class="ti ti-x"></i>
                        </button>
                    `;
                        wrapper.appendChild(group);
                    });
                } else {
                    wrapper.innerHTML = `<input type="text" class="form-control text-muted" value="-" disabled />`;
                }

                editBtn.classList.remove('d-none');
                saveBtn.classList.add('d-none');
                saveBtn.disabled = true;
                addDetailBtn.classList.add('d-none');
                addDetailBtn.style.display = 'none';
            });
        });
    </script>
@endpush
