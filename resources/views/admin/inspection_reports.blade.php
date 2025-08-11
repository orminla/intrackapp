@extends("admin.layouts.app")

@section("title", "Riwayat Inspeksi")

@section("content")
    <div class="row d-flex align-items-stretch">
        <div class="col-12">
            <div class="card w-100 h-100 rounded-4">
                <div class="card-body">
                    <div class="d-md-flex align-items-center flex-wrap gap-3">
                        <div>
                            <h4>Laporan Inspeksi</h4>
                        </div>

                        <div
                            class="d-flex align-items-center gap-3 ms-md-auto mt-3 mt-md-0 flex-wrap"
                        >
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">
                                    Showing
                                </span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="width: auto; min-width: 70px"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-normal text-muted">Filter</span>
                                <select
                                    class="form-select form-select-sm border-0 bg-light"
                                    style="width: auto; min-width: 100px"
                                >
                                    <option value="all">Semua</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="menunggu">Menunggu</option>
                                </select>
                            </div>
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
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Mitra
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Lokasi
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Petugas
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Portofolio
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-0 text-dark text-center fw-bold"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $i => $item)
                                    @php
                                        $warna = match (strtolower($item["status"])) {
                                            "dalam proses" => "info",
                                            "ditolak" => "danger",
                                            "menunggu konfirmasi" => "warning",
                                            "disetujui" => "success",
                                            default => "secondary",
                                        };
                                    @endphp

                                    <tr>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $i + 1 }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $item["tanggal_mulai"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $item["nama_mitra"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ $item["lokasi"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle fw-bolder"
                                        >
                                            {{ $item["petugas"] }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{ Str::before($item["portofolio"], " -") }}
                                        </td>
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            <span
                                                class="badge bg-{{ $warna }}-subtle text-{{ $warna }} py-2 px-3 rounded-2"
                                            >
                                                {{ $item["status"] }}
                                            </span>
                                        </td>

                                        {{-- Tombol Aksi --}}
                                        <td
                                            class="px-0 text-center align-middle"
                                        >
                                            {{-- Lihat --}}
                                            <button
                                                class="btn btn-sm px-1 border-0 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detailReportModal-{{ $i }}"
                                                title="Lihat Detail"
                                            >
                                                <i
                                                    class="ti ti-eye fs-5 text-primary"
                                                ></i>
                                            </button>

                                            {{-- Validasi --}}
                                            @if (strtolower($item["status"]) === "menunggu konfirmasi")
                                                <div class="btn-group">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm px-1 border-0 bg-transparent dropdown-toggle"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        title="Validasi"
                                                    >
                                                        <i
                                                            class="ti ti-circle-check fs-5 text-success"
                                                        ></i>
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end shadow rounded-3"
                                                    >
                                                        {{-- Setujui --}}
                                                        <li>
                                                            <button
                                                                type="button"
                                                                class="dropdown-item d-flex align-items-center gap-2 text-success btn-validasi"
                                                                data-status="Disetujui"
                                                                data-id="{{ $item["id"] }}"
                                                            >
                                                                <i
                                                                    class="ti ti-check"
                                                                ></i>
                                                                Disetujui
                                                            </button>
                                                        </li>

                                                        {{-- Tolak (buka modal) --}}
                                                        <li>
                                                            <button
                                                                class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalTolak-{{ $item["id"] }}"
                                                            >
                                                                <i
                                                                    class="ti ti-x"
                                                                ></i>
                                                                Ditolak
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    {{-- Include modal untuk baris ini --}}
                                    @include(
                                        "admin.detail_report_modal",
                                        [
                                            "index" => $i,
                                            "schedule" => $item["detail"] ?? [
                                                "mitra" => $item["nama_mitra"] ?? "-",
                                                "lokasi" => $item["lokasi"] ?? "-",
                                                "tanggal" => $item["tanggal_mulai"] ?? "",
                                                "tanggal_selesai" => "",
                                                "produk" => "-",
                                                "bidang" => $item["portofolio"] ?? "-",
                                                "petugas" => $item["petugas"] ?? "-",
                                                "detail_produk" => [],
                                                "dokumen" => [],
                                            ],
                                        ]
                                    )
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Tidak ada laporan inspeksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- hidden validasi --}}
    <form id="form-validasi" method="POST" style="display: none">
        @csrf
        @method("PUT")
        <input type="hidden" name="status" id="input-status" value="" />
    </form>

    @include("admin.rejected_modal")
@endsection

@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-primary rounded-2 px-4'
                    },
                    buttonsStyling: false
                });
            @endif

            // Tangani tombol Disetujui
            document.querySelectorAll('.btn-validasi').forEach(btn => {
                btn.addEventListener('click', function () {
                    const status = this.dataset.status;
                    const id = this.dataset.id;

                    if (status === 'Disetujui') {
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: `Yakin ingin menyetujui laporan ini?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, setujui',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-success rounded-2 px-4 me-2',
                                cancelButton: 'btn btn-outline-muted rounded-2 px-4'
                            },
                            buttonsStyling: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                submitValidasiForm(id, status);
                            }
                        });
                    }
                });
            });

            // Fungsi submit form validasi via AJAX (Disetujui)
            function submitValidasiForm(id, status) {
                const form = document.getElementById('form-validasi');
                form.action = `/admin/laporan/${id}/status`;
                document.getElementById('input-status').value = status;

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Status laporan berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4'
                        },
                        buttonsStyling: false
                    }).then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.message || 'Terjadi kesalahan saat memproses permintaan.',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4'
                        },
                        buttonsStyling: false
                    });
                });
            }
        });
    </script>
@endpush
