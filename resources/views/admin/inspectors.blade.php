@extends("admin.layouts.app")

@section("title", "Petugas")

@php
    $showingSelected = request()->get("showing", "");
    $filterSelected = request()->get("filter", "all");
@endphp

@section("content")
    <div class="row d-flex align-items-stretch">
        <!-- Data Petugas -->
        <div class="col-12 mb-2">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div
                        class="d-flex justify-content-between align-items-start flex-wrap"
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
                                    id="showing"
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="
                                        width: auto;
                                        min-width: 70px;
                                        height: 36px;
                                        font-size: 0.875rem;
                                    "
                                >
                                    <option
                                        value="10"
                                        {{ $showingSelected == "10" ? "selected" : "" }}
                                    >
                                        10
                                    </option>
                                    <option
                                        value="25"
                                        {{ $showingSelected == "25" ? "selected" : "" }}
                                    >
                                        25
                                    </option>
                                    <option
                                        value="50"
                                        {{ $showingSelected == "50" ? "selected" : "" }}
                                    >
                                        50
                                    </option>
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
                                Unggah Petugas
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
                                    {{--
                                        <th
                                        class="px-0 text-dark text-center fw-bold"
                                        >
                                        Bidang
                                        </th>
                                    --}}
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Portofolio
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Beban Saat Ini
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Total Inspeksi
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Kinerja
                                    </th>
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
                                        {{--
                                            <td
                                            class="px-0 text-center align-middle"
                                            >
                                            {{ $inspector["department"] }}
                                            </td>
                                        --}}
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ explode("-", $inspector["portfolio"])[0] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["beban_kerja"] ?? 0 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["pekerjaan_selesai"] ?? 0 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $inspector["kinerja"] ?? 0 }}%
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

                        <x-table-pagination :data="$inspectors" />
                    </div>
                </div>

                @include("admin.add_inspector_modal")
                @include("admin.edit_inspector_modal")
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function () {
                function triggerFileImport() {
                    document.getElementById('importFileInput').click();
                }

                // Fungsi reload dengan parameter query sesuai pilihan user
                function reloadPageWithParams() {
                    const showing = document.getElementById('showing').value;
                    const params = new URLSearchParams(window.location.search);

                    params.set('showing', showing);
                    params.set('page', 1); // reset page ke 1 biar gak nyangkut di page sebelumnya

                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                }

                // Event untuk ganti jumlah data per halaman
                const showingEl = document.getElementById('showing');
                if (showingEl) {
                    showingEl.addEventListener('change', reloadPageWithParams);
                }

                // document.getElementById('filter').addEventListener('change', reloadPageWithParams);

                document
                    .getElementById('importFileInput')
                    .addEventListener('change', function () {
                        const input = this;
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
                                    const formData = new FormData();
                                    formData.append('file', input.files[0]);

                                    fetch('{{ route("admin.petugas.import") }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]',
                                            ).content,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            Accept: 'application/json',
                                        },
                                        body: formData,
                                    })
                                        .then((response) => response.json())
                                        .then((data) => {
                                            if (data.success) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil!',
                                                    text:
                                                        data.message ||
                                                        'Import data berhasil. Menunggu Petugas Verifikasi akun',
                                                    timer: 1500,
                                                    showConfirmButton: false,
                                                    customClass: {
                                                        popup: 'rounded-4',
                                                        confirmButton:
                                                            'btn btn-primary rounded-2 px-4',
                                                    },
                                                }).then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                throw new Error(
                                                    data.message || 'Import gagal.',
                                                );
                                            }
                                        })
                                        .catch((error) => {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Gagal!',
                                                text: error.message,
                                                customClass: {
                                                    popup: 'rounded-4',
                                                    confirmButton:
                                                        'btn btn-primary rounded-2 px-4',
                                                },
                                            });
                                        });
                                } else {
                                    input.value = ''; // reset file input
                                }
                            });
                        }
                    });

                window.triggerFileImport = triggerFileImport; // expose function jika dipanggil dari HTML onclick

                function showEditModal(nip) {
                    const modal = new bootstrap.Modal(
                        document.getElementById(`updateModal-${nip}`),
                    );
                    modal.show();
                }

                window.showEditModal = showEditModal; // expose jika dipanggil dari HTML onclick

                // Konfirmasi sebelum hapus
                document.querySelectorAll('.delete-button').forEach((btn) => {
                    btn.addEventListener('click', function () {
                        const form = btn.closest('form');
                        Swal.fire({
                            title: 'Yakin ingin menghapus data petugas?',
                            text: 'Tindakan ini tidak dapat dibatalkan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-danger rounded-2 px-4 me-2',
                                cancelButton: 'btn btn-outline-muted rounded-2 px-4',
                            },
                            buttonsStyling: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        Accept: 'application/json',
                                    },
                                    body: new URLSearchParams(new FormData(form)),
                                })
                                .then(async (response) => {
                                    const data = await response.json();
                                    if (!response.ok) throw new Error(data.message || 'Gagal menghapus data');
                                    return data;
                                })
                                .then((data) => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message || 'Data petugas berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton: 'btn btn-primary rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    }).then(() => {
                                        location.reload();
                                    });
                                })
                                .catch((error) => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: error.message || 'Terjadi kesalahan saat menghapus.',
                                        customClass: {
                                            popup: 'rounded-4',
                                            confirmButton: 'btn btn-primary rounded-2 px-4',
                                        },
                                        buttonsStyling: false,
                                    });
                                });
                            }
                        });
                    });
                });

                @if(session('success') || session('error') || session('warning'))
                    Swal.fire({
                        icon: '{{ session("success") ? "success" : (session("error") ? "error" : "warning") }}',
                        title: '{{ session("success") ? "Berhasil!" : (session("error") ? "Gagal!" : "Perhatian!") }}',
                        text: "{{ session('success') ?? session('error') ?? session('warning') }}",
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    });
                @endif

                @if ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: '{!! implode("<br>", $errors->all()) !!}',
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    });
                @endif
            });
    </script>
@endpush
