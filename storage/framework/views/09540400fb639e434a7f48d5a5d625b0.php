<!-- Modal Tambah Admin -->
<div
    class="modal fade"
    id="tambahAdminModal"
    tabindex="-1"
    aria-labelledby="tambahAdminLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="tambahAdminLabel">
                    Tambah Admin
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form method="POST" action="<?php echo e(route("admin.pengaturan.store")); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body pt-2 mt-2">
                    <!-- Nama & NIP -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input
                                type="text"
                                name="nip"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                    </div>

                    <!-- Gender & Portofolio -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select
                                name="gender"
                                class="form-select rounded-2 bg-white"
                                required
                            >
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Portofolio</label>
                            <select
                                name="portfolio_id"
                                class="form-select rounded-2 bg-white"
                                required
                            >
                                <option value="">Pilih Portofolio</option>
                                <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($portfolio->portfolio_id); ?>"
                                    >
                                        <?php echo e($portfolio->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <!-- Telepon & Email -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input
                                type="text"
                                name="phone_num"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 mt-2">
                    <button
                        type="submit"
                        class="btn btn-success rounded-2 px-4 w-100"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('tambahAdminModal');
        if (!modal) return;

        const form = modal.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');
        const portfolioSelect = document.getElementById('adminPortfolioSelect');

        function checkFormValidity() {
            const requiredElements = form.querySelectorAll(
                'input[required], select[required]',
            );
            for (const el of requiredElements) {
                if (!el.value || el.value.trim() === '') return false;
            }
            return true;
        }

        function updateSubmitBtn() {
            submitBtn.disabled = !checkFormValidity();
        }

        // Update tombol submit saat input berubah
        const requiredElements = form.querySelectorAll(
            'input[required], select[required]',
        );
        requiredElements.forEach((el) => {
            el.addEventListener('input', updateSubmitBtn);
            el.addEventListener('change', updateSubmitBtn);
        });

        submitBtn.disabled = true;

        // AJAX Submit + SweetAlert
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!checkFormValidity()) {
                form.reportValidity();
                return;
            }

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
                        throw new Error(data.message || 'Gagal menyimpan data');
                    return data;
                })
                .then((data) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text:
                            data.message || 'Data admin berhasil ditambahkan.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' },
                        buttonsStyling: false,
                    });
                    if (modalInstance) modalInstance.hide();
                    setTimeout(() => location.reload(), 1600);
                })
                .catch((error) => {
                    if (modalInstance) modalInstance.hide();

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
                });
        });
    });
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/add_admin_modal.blade.php ENDPATH**/ ?>