<style>
    .custom-modal-width {
        max-width: 85%;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 768px) {
        .custom-modal-width {
            max-width: 480px;
        }
    }
</style>

<!-- Modal Profil -->
<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
    aria-labelledby="profileModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <div class="modal-content rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="profileModalLabel">Profil Saya</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>

            <div class="modal-body pt-2 mt-2">
                <div class="text-center mb-3">
                    <img
                        src="{{ $profile["photo_url"] }}"
                        class="rounded-circle mb-2 object-fit-cover"
                        width="80"
                        height="80"
                        alt="Foto Profil"
                    />
                    <h5 class="fw-bold mb-0">{{ $profile["name"] }}</h5>
                    <small class="text-muted text-capitalize">
                        {{ ucfirst($profile["role"]) }}
                    </small>
                </div>

                <hr />
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">NIP</strong>
                    <span>: {{ $profile["nip"] ?? "-" }}</span>
                </div>
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">Email</strong>
                    <span>: {{ $profile["email"] ?? "-" }}</span>
                </div>
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">
                        No. Telepon
                    </strong>
                    <span>: {{ $profile["phone_num"] ?? "-" }}</span>
                </div>
                <div class="mb-2 d-flex">
                    <strong class="me-2" style="min-width: 110px">
                        Bidang
                    </strong>
                    <span>: {{ $profile["department"] ?? "-" }}</span>
                </div>
                <div class="d-flex">
                    <strong class="me-2" style="min-width: 110px">
                        Portofolio
                    </strong>
                    <span>: {{ $profile["portfolio"] ?? "-" }}</span>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 mt-2">
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
            <form
                method="POST"
                action="{{ route("profile.update", auth()->id()) }}"
                enctype="multipart/form-data"
            >
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
                            class="form-control @error("name") is-invalid @enderror"
                            value="{{ old("name", $profile["name"] ?? "") }}"
                        />
                        @error("name")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error("email") is-invalid @enderror"
                            value="{{ old("email", $profile["email"] ?? "") }}"
                        />
                        @error("email")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone_num" class="form-label">
                            No. Telepon
                        </label>
                        <input
                            type="text"
                            name="phone_num"
                            id="phone_num"
                            class="form-control @error("phone_num") is-invalid @enderror"
                            value="{{ old("phone_num", $profile["phone_num"] ?? "") }}"
                        />
                        @error("phone_num")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                            class="form-control @error("photo_url") is-invalid @enderror"
                            onchange="previewPhoto(event)"
                        />
                        @error("photo_url")
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                action="{{ route("profile.change-password") }}"
                method="POST"
            >
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            Password Lama
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="current_password"
                            name="current_password"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            Password Baru
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="new_password"
                            name="new_password"
                            required
                        />
                    </div>
                    <div class="mb-2">
                        <label
                            for="new_password_confirmation"
                            class="form-label"
                        >
                            Konfirmasi Password Baru
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            required
                        />
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
</script>

<script>
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
</script>
