@extends("admin.layouts.app")

@section("title", "Pengaturan")

@section("content")
    <div class="row d-flex align-items-stretch">
        <!-- Data Admin -->
        <div class="col-12 mb-2">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div
                        class="d-flex justify-content-between align-items-start flex-wrap mb-3"
                    >
                        <div class="mb-3 mb-md-0">
                            <h4>Data Admin</h4>
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

                            <!-- Tambah Admin -->
                            <button
                                class="btn btn-primary d-flex align-items-center gap-2"
                                style="height: 36px; font-size: 0.875rem"
                                data-bs-toggle="modal"
                                data-bs-target="#tambahAdminModal"
                            >
                                <i class="ti ti-user-plus fs-5"></i>
                                Tambah Admin
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
                                        Jenis Kelamin
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Portofolio
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $index => $admin)
                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $index + 1 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $admin["nip"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $admin["name"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $admin["gender"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $admin["portfolio"] ?? "-" }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <!-- Edit -->
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#updateModal-{{ $admin["nip"] }}"
                                                data-bs-title="Lihat & Ubah"
                                            >
                                                <i
                                                    class="ti ti-edit fs-5 text-warning"
                                                ></i>
                                            </button>

                                            <!-- Delete -->
                                            @php
                                                $isOnlyOne = count($admins) <= 1;
                                            @endphp

                                            @if (! $isOnlyOne)
                                                <form
                                                    method="POST"
                                                    action="{{ route("admin.pengaturan.destroy", $admin["admin_id"]) }}"
                                                    class="d-inline delete-form"
                                                    onsubmit="event.preventDefault(); confirmDelete(this)"
                                                >
                                                    @csrf
                                                    @method("DELETE")
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm px-1 border-0 bg-transparent"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-title="Hapus"
                                                    >
                                                        <i
                                                            class="ti ti-trash fs-5 text-danger"
                                                        ></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @include("admin.add_admin_modal")
                @include("admin.edit_admin_modal")
                <!-- Modal untuk update -->
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
                location.href = '{{ route("admin.pengaturan.index") }}';
            });
        </script>
    @endif
@endsection

@push("scripts")
    <script>
        function showEditAdminModal(nip) {
            // Ambil data dari API
            fetch(`/admin/${nip}`)
                .then((response) => response.json())
                .then((result) => {
                    if (result.success) {
                        const data = result.data;
                        document.getElementById('updateNip').value = data.nip;
                        document.getElementById('updateName').value = data.name;
                        document.getElementById('updatePhone').value =
                            data.phone_num;
                        document.getElementById('updatePortfolio').value =
                            data.portfolio_id;

                        const modal = new bootstrap.Modal(
                            document.getElementById('editAdminModal'),
                        );
                        modal.show();
                    } else {
                        alert('Gagal memuat data admin');
                    }
                })
                .catch(() => alert('Terjadi kesalahan'));
        }

        function confirmDelete(formElement) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus admin ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-danger rounded-2 me-2 px-4',
                    cancelButton: 'btn btn-outline-secondary rounded-2 px-4',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = formElement.action;
                    const formData = new FormData(formElement);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(async (response) => {
                            const data = await response.json();
                            if (!response.ok)
                                throw new Error(
                                    data.message || 'Gagal menghapus data',
                                );
                            return data;
                        })
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Admin berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-4',
                                },
                            }).then(() => location.reload());
                        })
                        .catch((error) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text:
                                    error.message ||
                                    'Terjadi kesalahan saat menghapus data.',
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
        }
    </script>
@endpush
