<style>
    .custom-modal-width {
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 768px) {
        .custom-modal-width {
            max-width: 520px;
        }
    }

    #profileModal .profile-pic {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #0d6efd;
        padding: 2px;
    }

    #profileModal .field-label {
        min-width: 110px;
        font-weight: 600;
    }

    #profileModal .field-row {
        margin-bottom: 0.75rem;
    }

    #profileModal .certification-section input[type='file'] {
        display: none;
    }

    #profileModal .certification-section label {
        cursor: pointer;
        margin-left: 10px;
    }

    #profileModal .certification-section span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        display: inline-block;
    }

    .certification-card {
        width: 100%; /* full lebar container */
        background-color: #f8f9fa; /* warna default card */
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
        box-sizing: border-box; /* padding tidak menambah lebar */
        word-wrap: break-word; /* kata panjang akan pindah baris */
    }

    .certification-card:hover {
        background-color: #e2ebff !important; /* biru terang */
        border-color: #e2ebff !important;
        color: #1e4db7; /* semua teks jadi putih */
    }

    .certification-card .small {
        display: block; /* agar block, bukan inline */
        width: 100%; /* pakai lebar penuh */
        white-space: normal; /* wrap otomatis */
        overflow: visible; /* jangan dipotong */
        text-overflow: clip;
        word-break: break-word;
    }
</style>

@include(
    "add_certification_modal",
    [
        "allPortfolios" => $allPortfolios ?? collect([]),
        "profile" => $profile ?? [],
    ]
)
@include("edit_certification_modal")

<!-- Modal Profil -->
<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
    aria-labelledby="profileModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <div class="modal-content rounded-4 p-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="profileModalLabel">Profil Saya</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>

            <div class="modal-body">
                <!-- Foto Profil -->
                <div class="text-center mb-4">
                    <img
                        src="{{ $profile["photo_url"] }}"
                        alt="Foto Profil"
                        class="profile-pic mb-2"
                    />
                    <h5 class="fw-bold mb-0">{{ $profile["name"] }}</h5>
                    <small class="text-muted text-capitalize">
                        {{ ucfirst($profile["role"]) }}
                    </small>
                </div>

                <hr />

                <!-- Informasi Profil -->
                <div class="field-row d-flex">
                    <div class="field-label">NIP</div>
                    <div>: {{ $profile["nip"] ?? "-" }}</div>
                </div>
                <div class="field-row d-flex">
                    <div class="field-label">Jenis Kelamin</div>
                    <div>: {{ $profile["gender"] ?? "-" }}</div>
                </div>
                <div class="field-row d-flex">
                    <div class="field-label">Email</div>
                    <div>: {{ $profile["email"] ?? "-" }}</div>
                </div>
                <div class="field-row d-flex">
                    <div class="field-label">No. Telepon</div>
                    <div>: {{ $profile["phone_num"] ?? "-" }}</div>
                </div>
                <div class="field-row d-flex">
                    <div class="field-label">Bidang</div>
                    <div>: {{ $profile["department"] ?? "-" }}</div>
                </div>
                <div class="field-row d-flex">
                    <div class="field-label">Portofolio</div>
                    <div>: {{ $profile["portfolio"] ?? "-" }}</div>
                </div>

                <hr />

                <!-- Sertifikasi -->
                <div class="field-row certification-section">
                    <div
                        class="d-flex justify-content-between align-items-center mb-3"
                    >
                        <strong>Sertifikasi</strong>
                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm mb-0"
                            data-bs-toggle="modal"
                            data-bs-target="#tambahCertifModal"
                        >
                            <i class="ti ti-plus me-1"></i>
                            Tambah
                        </button>
                    </div>

                    <!-- Daftar file sertifikasi -->
                    <div
                        class="certification-list"
                        style="max-height: 200px; overflow-y: auto"
                    >
                        @if (isset($profile["certifications"]) && $profile["certifications"]->isNotEmpty())
                            @foreach ($profile["certifications"] as $cert)
                                @php
                                    $certUrl = $cert->file_path ? asset("storage/" . $cert->file_path) : null;
                                @endphp

                                <div
                                    class="certification-card mb-2 p-2 border rounded bg-light"
                                    @if ($certUrl)
                                        onclick="window.open('{{ $certUrl }}', '_blank')"
                                        style="cursor: pointer;"
                                    @endif
                                >
                                    <div class="fw-bold mb-1">
                                        {{ $cert->name }}
                                    </div>

                                    <div class="small text-muted">
                                        Diterbitkan oleh
                                        {{ $cert->issuer ?? "-" }} & berlaku
                                        sejak
                                        {{ $cert->issued_at ? \Carbon\Carbon::parse($cert->issued_at)->format("d M Y") : "-" }}
                                        hingga
                                        {{ $cert->expired_at ? \Carbon\Carbon::parse($cert->expired_at)->format("d M Y") : "selamanya." }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted">Belum ada sertifikasi</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 d-flex flex-column gap-2">
                <button
                    type="button"
                    class="btn btn-primary w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#editProfileModal"
                    data-bs-dismiss="modal"
                >
                    <i class="ti ti-pencil me-2"></i>
                    Edit Profil
                </button>
                <button
                    type="button"
                    class="btn btn-outline-primary w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#resetPasswordModal"
                    data-bs-dismiss="modal"
                >
                    <i class="ti ti-lock me-2"></i>
                    Ganti Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div
    class="modal fade"
    id="editProfileModal"
    tabindex="-1"
    aria-labelledby="editProfileModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <div class="modal-content rounded-4 p-3">
            <form id="formEditProfile" enctype="multipart/form-data">
                @csrf
                @method("PUT")

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="editProfileModalLabel">
                        Edit Profil
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body pt-2 mt-2">
                    <div class="mb-3 text-center">
                        <img
                            id="photoPreview"
                            src="{{ $profile["photo_url"] ?? asset("login_assets/images/profile/user-7.jpg") }}"
                            class="rounded-circle mb-2 object-fit-cover"
                            width="80"
                            height="80"
                            alt="Preview Foto"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            value="{{ old("name", $profile["name"] ?? "") }}"
                        />
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">
                            Jenis Kelamin
                        </label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="">Pilih</option>
                            <option
                                value="Laki-laki"
                                {{ old("gender", $profile["gender"]) == "Laki-laki" ? "selected" : "" }}
                            >
                                Laki-laki
                            </option>
                            <option
                                value="Perempuan"
                                {{ old("gender", $profile["gender"]) == "Perempuan" ? "selected" : "" }}
                            >
                                Perempuan
                            </option>
                        </select>
                        <div class="invalid-feedback" id="error-gender"></div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            value="{{ old("email", $profile["email"] ?? "") }}"
                        />
                        <div class="invalid-feedback" id="error-email"></div>
                    </div>

                    <div class="mb-3">
                        <label for="phone_num" class="form-label">
                            No. Telepon
                        </label>
                        <input
                            type="text"
                            name="phone_num"
                            id="phone_num"
                            class="form-control"
                            value="{{ old("phone_num", $profile["phone_num"] ?? "") }}"
                        />
                        <div
                            class="invalid-feedback"
                            id="error-phone_num"
                        ></div>
                    </div>

                    <div class="mb-3">
                        <label for="photo_url" class="form-label">
                            Foto Profil (Upload)
                        </label>
                        <input
                            type="file"
                            name="photo_url"
                            id="photo_url"
                            accept="image/*"
                            class="form-control"
                            onchange="previewPhoto(event)"
                        />
                        <div
                            class="invalid-feedback"
                            id="error-photo_url"
                        ></div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 mt-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-check me-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Password -->
<div
    class="modal fade"
    id="resetPasswordModal"
    tabindex="-1"
    aria-labelledby="resetPasswordModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5
                    class="modal-title fw-semibold mt-2"
                    id="resetPasswordModalLabel"
                >
                    Ubah Password
                </h5>
                <button
                    type="button"
                    class="btn-close shadow-none"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form
                id="formChangePassword"
                action="{{ route("profile.change-password") }}"
                method="POST"
            >
                @csrf
                <div class="modal-body px-4 py-3">
                    <!-- Password Lama -->
                    <div class="mb-3 position-relative">
                        <label for="current_password" class="form-label">
                            Password Lama
                        </label>
                        <div class="input-group">
                            <input
                                type="password"
                                class="form-control"
                                id="current_password"
                                name="current_password"
                                required
                                autocomplete="current-password"
                            />
                            <button
                                type="button"
                                id="toggleCurrentPassword"
                                tabindex="-1"
                                aria-label="Toggle password visibility"
                                style="
                                    background: transparent !important;
                                    border: none !important;
                                    padding: 0 0.5rem;
                                    margin-left: -2.5rem;
                                    z-index: 2;
                                    color: #0d6efd;
                                    cursor: pointer;
                                "
                            >
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Baru -->
                    <div class="mb-3 position-relative">
                        <label for="new_password" class="form-label">
                            Password Baru
                        </label>
                        <div class="input-group">
                            <input
                                type="password"
                                class="form-control"
                                id="new_password"
                                name="new_password"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                id="toggleNewPassword"
                                tabindex="-1"
                                aria-label="Toggle password visibility"
                                style="
                                    background: transparent !important;
                                    border: none !important;
                                    padding: 0 0.5rem;
                                    margin-left: -2.5rem;
                                    z-index: 2;
                                    color: #0d6efd;
                                    cursor: pointer;
                                "
                            >
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="mb-2 position-relative">
                        <label
                            for="new_password_confirmation"
                            class="form-label"
                        >
                            Konfirmasi Password Baru
                        </label>
                        <div class="input-group">
                            <input
                                type="password"
                                class="form-control"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                id="toggleConfirmPassword"
                                tabindex="-1"
                                aria-label="Toggle password visibility"
                                style="
                                    background: transparent !important;
                                    border: none !important;
                                    padding: 0 0.5rem;
                                    margin-left: -2.5rem;
                                    z-index: 2;
                                    color: #0d6efd;
                                    cursor: pointer;
                                "
                            >
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pt-0 mb-2">
                    <button type="submit" class="btn btn-success w-100">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    console.log('Profile Data:', @json($profile));

    // Preview photo upload
    function previewPhoto(event) {
        const input = event.target;
        const preview = document.getElementById('photoPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Reset validation error UI
    function resetValidationErrors(formId) {
        const form = document.getElementById(formId);
        form.querySelectorAll('.is-invalid').forEach((el) =>
            el.classList.remove('is-invalid'),
        );
        form.querySelectorAll('.invalid-feedback').forEach(
            (el) => (el.textContent = ''),
        );
    }

    // Show validation errors
    function showValidationErrors(errors, formId) {
        for (const [key, messages] of Object.entries(errors)) {
            const input = document.querySelector(`#${formId} [name="${key}"]`);
            const errorDiv = document.getElementById(`error-${key}`);
            if (input) {
                input.classList.add('is-invalid');
                if (errorDiv) errorDiv.textContent = messages.join(', ');
            }
        }
    }

    // AJAX submit edit profile
    document
        .getElementById('formEditProfile')
        .addEventListener('submit', function (e) {
            e.preventDefault();

            resetValidationErrors('formEditProfile');

            const formData = new FormData(this);

            fetch('{{ route("profile.update", auth()->id()) }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Tutup modal dulu
                        const modalEl =
                            document.getElementById('editProfileModal');
                        const modalInstance =
                            bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        // Tampil popup sukses tanpa tombol konfirmasi, auto close 1.5s
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-4',
                            },
                        });

                        // Reload halaman setelah 1.5 detik (popup hilang)
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        if (data.errors) {
                            showValidationErrors(
                                data.errors,
                                'formEditProfile',
                            );
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-primary rounded-2 px-4',
                                },
                                buttonsStyling: false,
                            });
                        }
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengirim data',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    });
                });
        });

    // AJAX submit change password
    document
        .getElementById('formChangePassword')
        .addEventListener('submit', function (e) {
            e.preventDefault();

            resetValidationErrors('formChangePassword');

            const formData = new FormData(this);

            fetch('{{ route("profile.change-password") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Tutup modal dulu
                        const modalEl =
                            document.getElementById('resetPasswordModal');
                        const modalInstance =
                            bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        // Tampil popup sukses tanpa tombol konfirmasi, auto close 1.5s
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-4',
                            },
                        });

                        // Redirect ke login setelah 1.5 detik (popup hilang)
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                    } else {
                        if (data.errors) {
                            showValidationErrors(
                                data.errors,
                                'formChangePassword',
                            );
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-primary rounded-2 px-4',
                                },
                                buttonsStyling: false,
                            });
                        }
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengirim data',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'rounded-4',
                            confirmButton: 'btn btn-primary rounded-2 px-4',
                        },
                        buttonsStyling: false,
                    });
                });
        });

    // Toggle Password function
    function togglePassword(buttonId, inputId) {
        const toggleBtn = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', function () {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        });
    }

    togglePassword('toggleCurrentPassword', 'current_password');
    togglePassword('toggleNewPassword', 'new_password');
    togglePassword('toggleConfirmPassword', 'new_password_confirmation');
</script>
