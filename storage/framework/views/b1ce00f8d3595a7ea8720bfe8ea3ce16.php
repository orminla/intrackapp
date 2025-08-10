<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo $__env->yieldContent("title", "Inspector"); ?> - Sistem Inspeksi</title>
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css"
        />
        <link
            rel="shortcut icon"
            type="image/png"
            href="<?php echo e(asset("inspector_assets/images/logos/favicon.png")); ?>"
        />
        <link
            rel="stylesheet"
            href="<?php echo e(asset("inspector_assets/css/styles.min.css")); ?>"
        />
        <link
            rel="stylesheet"
            href="<?php echo e(asset("inspector_assets/css/custom.css")); ?>"
        />

        <style>
            .app-header,
            .left-sidebar {
                top: 0;
                position: fixed;
            }

            .body-wrapper {
                padding-top: 100px;
            }

            main,
            .container-fluid {
                margin-top: 0 !important;
                padding-top: 0 !important;
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            .profile-hover span,
            .profile-hover small {
                transition: color 0.3s ease;
            }

            .profile-hover:hover span,
            .profile-hover:hover small {
                color: var(--bs-primary);
            }
        </style>

        <?php echo $__env->yieldPushContent("styles"); ?>
        <!-- Untuk halaman yang butuh style tambahan -->
    </head>

    <body>
        <!-- Wrapper -->
        <div
            class="page-wrapper"
            id="main-wrapper"
            data-layout="vertical"
            data-navbarbg="skin6"
            data-sidebartype="full"
            data-header-position="fixed"
        >
            <!-- Sidebar -->
            <?php echo $__env->make("inspector.layouts.sidebar", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Main Content -->
            <div class="body-wrapper">
                <!-- Header -->
                <?php echo $__env->make("inspector.layouts.header", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <!-- Page Content -->
                <main>
                    <div class="container-fluid">
                        <div class="col">
                            <?php echo $__env->yieldContent("content"); ?>
                        </div>
                    </div>
                </main>

                <!-- Footer -->
                <?php echo $__env->make("inspector.layouts.footer", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

        <?php echo $__env->make("profile_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Scripts -->
        <script src="<?php echo e(asset("inspector_assets/libs/jquery/dist/jquery.min.js")); ?>"></script>
        <script src="<?php echo e(asset("inspector_assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js")); ?>"></script>
        <script src="<?php echo e(asset("inspector_assets/js/sidebarmenu.js")); ?>"></script>
        <script src="<?php echo e(asset("inspector_assets/js/app.min.js")); ?>"></script>
        <script src="<?php echo e(asset("inspector_assets/libs/simplebar/dist/simplebar.js")); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const header = document.querySelector('.app-header'); // pastikan ini class header kamu
                const main = document.querySelector('main');

                if (header && main) {
                    const headerHeight = header.offsetHeight;
                    main.style.marginTop = headerHeight + 'px';
                }
            });
        </script>

        <!-- Tooltip -->
        <script>
            document
                .querySelectorAll('[data-bs-toggle="tooltip"]')
                .forEach((el) => {
                    new bootstrap.Tooltip(el);
                });
        </script>

        <!-- SweetAlert2 untuk konfirmasi penghapusan -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document
                    .querySelectorAll('.delete-button')
                    .forEach((button) => {
                        button.addEventListener('click', function (e) {
                            const form = button.closest('form');

                            Swal.fire({
                                title: 'Yakin ingin menghapus?',
                                text: 'Data tidak dapat dikembalikan setelah dihapus!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal',
                                customClass: {
                                    popup: 'rounded-4',
                                    confirmButton:
                                        'btn btn-danger rounded-2 px-4 me-2',
                                    cancelButton:
                                        'btn btn-outline-muted rounded-2 px-4',
                                },
                                buttonsStyling: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        });
                    });
            });
        </script>

        <?php echo $__env->yieldPushContent("scripts"); ?>
        <!-- Untuk halaman yang butuh JS tambahan -->
    </body>
</html>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/layouts/app.blade.php ENDPATH**/ ?>