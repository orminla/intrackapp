<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

        <title><?php echo $__env->yieldContent("title", "Admin"); ?> - Sistem Inspeksi</title>

        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css"
        />
        <link
            rel="shortcut icon"
            type="image/png"
            href="<?php echo e(asset("admin_assets/images/logos/favicon.png")); ?>"
        />
        <link
            rel="stylesheet"
            href="<?php echo e(asset("admin_assets/css/styles.min.css")); ?>"
        />
        <link
            rel="stylesheet"
            href="<?php echo e(asset("admin_assets/css/custom.css")); ?>"
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
            
            .pagination {
                display: flex;
                flex-wrap: nowrap;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.85rem; /* ukuran font sekitar 13-14px */
                max-width: fit-content;
                border-radius: 4px;
                padding: 0;
            }

            .pagination svg {
                width: 16px;
                height: 16px;
            }

            .pagination .page-link {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
                min-width: auto;
                height: auto;
                line-height: 1.3;
                padding-left: 1.2rem;
                padding-right: 1.2rem;
            }

            .pagination .page-item {
                margin: 0;
            }

            .pagination .page-item.active .page-link,
            .pagination .page-item.disabled .page-link {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }

            .showing-text {
                color: #adb5bd; /* abu muda */
                font-size: 0.85rem;
                margin: 0; /* pastikan gak ada margin */
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
            <?php echo $__env->make("admin.layouts.sidebar", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Main Content -->
            <div class="body-wrapper">
                <!-- Header -->
                <?php echo $__env->make("admin.layouts.header", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <!-- Page Content -->
                <main>
                    <div class="container-fluid">
                        <div class="col">
                            <?php echo $__env->yieldContent("content"); ?>
                        </div>
                    </div>
                </main>

                <!-- Footer -->
                <?php echo $__env->make("admin.layouts.footer", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

        <?php echo $__env->make("profile_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Scripts -->
        <script src="<?php echo e(asset("admin_assets/libs/jquery/dist/jquery.min.js")); ?>"></script>
        <script src="<?php echo e(asset("admin_assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js")); ?>"></script>
        <script src="<?php echo e(asset("admin_assets/js/sidebarmenu.js")); ?>"></script>
        <script src="<?php echo e(asset("admin_assets/js/app.min.js")); ?>"></script>
        <script src="<?php echo e(asset("admin_assets/libs/simplebar/dist/simplebar.js")); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        

        <?php echo $__env->yieldPushContent("scripts"); ?>
        <!-- Untuk halaman yang butuh JS tambahan -->
    </body>
</html>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>