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

                    <div class="row g-3">
                        <!-- Bidang -->
                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <select
                                id="adminDepartmentSelect"
                                name="department_id"
                                class="form-select rounded-2 bg-white"
                                required
                            >
                                <option value="">Pilih Bidang</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->department_id); ?>">
                                        <?php echo e($dept->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Portofolio -->
                        <div class="col-md-6">
                            <label class="form-label">Portofolio</label>
                            <select
                                id="adminPortfolioSelect"
                                name="portfolio_id"
                                class="form-select rounded-2 bg-white"
                                required
                            >
                                <option value="">Pilih Portofolio</option>
                                <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($portfolio->portfolio_id); ?>"
                                        data-dept="<?php echo e($portfolio->department_id); ?>"
                                    >
                                        <?php echo e($portfolio->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Telepon -->
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input
                                type="text"
                                name="phone_num"
                                class="form-control rounded-2 bg-white"
                                required
                            />
                        </div>

                        <!-- Email -->
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
        const form = document.querySelector('#tambahAdminModal form');
        const submitBtn = form.querySelector('button[type="submit"]');
        const deptSelect = document.getElementById('adminDepartmentSelect');
        const portfolioSelect = document.getElementById('adminPortfolioSelect');

        const allOptions = Array.from(portfolioSelect.options).filter(
            (o) => o.value !== '',
        );

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

        deptSelect.addEventListener('change', function () {
            const selectedDept = this.value;
            portfolioSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih Portofolio';
            portfolioSelect.appendChild(placeholder);

            allOptions.forEach((opt) => {
                if (opt.dataset.dept === selectedDept) {
                    portfolioSelect.appendChild(opt);
                }
            });

            updateSubmitBtn();
        });

        const requiredElements = form.querySelectorAll(
            'input[required], select[required]',
        );
        requiredElements.forEach((el) => {
            el.addEventListener('input', updateSubmitBtn);
            el.addEventListener('change', updateSubmitBtn);
        });

        const modal = document.getElementById('tambahAdminModal');
        modal.addEventListener('hidden.bs.modal', function () {
            form.reset();
            portfolioSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih Portofolio';
            portfolioSelect.appendChild(placeholder);
            allOptions.forEach((opt) => portfolioSelect.appendChild(opt));
            updateSubmitBtn();
        });

        submitBtn.disabled = true;
    });
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/add_admin_modal.blade.php ENDPATH**/ ?>