<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield("title", "Admin") - Sistem Inspeksi</title>

        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css"
        />
        <link
            rel="shortcut icon"
            type="image/png"
            href="{{ asset("admin_assets/images/logos/favicon.png") }}"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin_assets/css/styles.min.css") }}"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin_assets/css/custom.css") }}"
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

        @stack("styles")
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
            @include("admin.layouts.sidebar")

            <!-- Main Content -->
            <div class="body-wrapper">
                <!-- Header -->
                @include("admin.layouts.header")

                <!-- Page Content -->
                <main>
                    <div class="container-fluid">
                        <div class="col">
                            @yield("content")
                        </div>
                    </div>
                </main>

                <!-- Footer -->
                @include("admin.layouts.footer")
            </div>
        </div>

        @include("profile_modal")

        <!-- Scripts -->
        <script src="{{ asset("admin_assets/libs/jquery/dist/jquery.min.js") }}"></script>
        <script src="{{ asset("admin_assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js") }}"></script>
        <script src="{{ asset("admin_assets/js/sidebarmenu.js") }}"></script>
        <script src="{{ asset("admin_assets/js/app.min.js") }}"></script>
        <script src="{{ asset("admin_assets/libs/simplebar/dist/simplebar.js") }}"></script>
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

        @stack("scripts")
        <!-- Untuk halaman yang butuh JS tambahan -->
    </body>
</html>
