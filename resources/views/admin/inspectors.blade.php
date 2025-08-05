@extends("admin.layouts.app")

@section("title", "Data Petugas")

@section("content")
    <div class="row d-flex align-items-stretch">
        <!-- Data Petugas -->
        <div class="col-12 mb-2">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div
                        class="d-flex justify-content-between align-items-start flex-wrap mb-3"
                    >
                        <div class="mb-3 mb-md-0">
                            <h4>Data Inspektor</h4>
                        </div>

                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <!-- Showing -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-normal text-muted">
                                    Showing
                                </span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="
                                        width: auto;
                                        min-width: 70px;
                                        height: 36px;
                                        font-size: 0.875rem;
                                    "
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>

                            <!-- Filter -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-normal text-muted">Filter</span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="
                                        width: auto;
                                        min-width: 100px;
                                        height: 36px;
                                        font-size: 0.875rem;
                                    "
                                >
                                    <option value="all">Semua</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="menunggu">Menunggu</option>
                                </select>
                            </div>

                            <!-- Import Excel -->
                            <button
                                type="button"
                                class="btn btn-success d-flex align-items-center gap-2"
                                style="height: 36px; font-size: 0.875rem"
                                onclick="triggerFileImport()"
                            >
                                <i class="ti ti-upload fs-5"></i>
                                Import Data Petugas
                            </button>

                            <!-- Form dan input file disembunyikan -->
                            <form
                                id="importForm"
                                action="{{ route("admin.petugas.import") }}"
                                method="POST"
                                enctype="multipart/form-data"
                                style="display: none"
                            >
                                @csrf
                                <input
                                    type="file"
                                    name="file"
                                    id="importFileInput"
                                    accept=".xlsx,.xls"
                                    onchange="confirmImport()"
                                />
                            </form>

                            <!-- Tambah Petugas -->
                            <button
                                class="btn btn-primary d-flex align-items-center gap-2"
                                style="height: 36px; font-size: 0.875rem"
                                data-bs-toggle="modal"
                                data-bs-target="#tambahPetugasModal"
                            >
                                <i class="ti ti-user-plus fs-5"></i>
                                Tambah Petugas
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table
                            class="table mb-0 text-nowrap varient-table align-middle fs-3"
                        >
                            <thead>
                                <tr>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        NIP
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Nama
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Bidang
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Portofolio
                                    </th>
                                    <!-- =<th class="px-0 text-dark text-center fw-bold">Beban Saat Ini</th>
                                <th class="px-0 text-dark text-center fw-bold">Total Inspeksi</th>
                                <th class="px-0 text-dark text-center fw-bold">Kinerja</th> -->
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inspectors as $index => $inspector)
                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $index + 1 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["nip"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["name"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["department"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["portfolio"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Edit -->
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Lihat & Ubah"
                                                onclick="showEditModal('{{ $inspector["nip"] }}')"
                                            >
                                                <i
                                                    class="ti ti-edit fs-5 text-warning"
                                                ></i>
                                            </button>

                                            <!-- Hapus -->
                                            <form
                                                method="POST"
                                                action="{{ route("admin.petugas.destroy", $inspector["nip"]) }}"
                                                class="d-inline delete-form"
                                            >
                                                @csrf
                                                @method("DELETE")
                                                <button
                                                    type="button"
                                                    class="btn btn-sm px-1 border-0 bg-transparent delete-button"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-title="Hapus"
                                                >
                                                    <i
                                                        class="ti ti-trash fs-5 text-danger"
                                                    ></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @include("admin.add_inspector_modal")
                @include("admin.edit_inspector_modal")
            </div>
        </div>
    </div>

    @if (session("success"))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                confirmButtonText: 'OK',
            }).then(() => {
                location.href = '{{ route("admin.petugas.index") }}';
            });
        </script>
    @endif
@endsection

@push("scripts")
    <script>
        function triggerFileImport() {
            document.getElementById('importFileInput').click();
        }

        function confirmImport() {
            const input = document.getElementById('importFileInput');
            if (input.files.length > 0) {
                const fileName = input.files[0].name;

                Swal.fire({
                    title: 'Konfirmasi Import',
                    text: `Yakin ingin mengimpor file "${fileName}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, impor',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-success rounded-2 me-2 px-4',
                        cancelButton: 'btn btn-outline-danger rounded-2 px-4',
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('importForm').submit();
                    } else {
                        input.value = ''; // reset input file
                    }
                });
            }
        }

        function showEditModal(nip) {
            const modal = new bootstrap.Modal(
                document.getElementById(`updateModal-${nip}`),
            );
            modal.show();
        }
    </script>
@endpush
