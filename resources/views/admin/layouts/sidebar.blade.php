<!-- Sidebar Start -->
<aside
    class="left-sidebar"
    style="height: 100vh; display: flex; flex-direction: column"
>
    <div>
        <div
            class="brand-logo d-flex align-items-center justify-content-between py-4 mt-4 mb-2"
        >
            <a
                href="{{ route("admin.dashboard") }}"
                class="text-nowrap logo-img"
            >
                <img
                    src="{{ asset("admin_assets/images/logo_intrackapp.png") }}"
                    alt="InTrack App"
                    style="max-width: 90%"
                />
            </a>
            <div
                class="close-btn d-xl-none d-block sidebartoggler cursor-pointer"
                id="sidebarCollapse"
            >
                <i class="ti ti-x" style="font-size: 32px"></i>
            </div>
        </div>

        <!-- Sidebar Items -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="sidebar-item mb-2">
                    <a
                        class="sidebar-link justify-content-between"
                        href="{{ route("admin.dashboard") }}"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-dashboard"></i>
                            <span class="hide-menu">Dashboard</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-item mb-2">
                    <a
                        class="sidebar-link justify-content-between"
                        href="{{ route("admin.jadwal.index") }}"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-calendar"></i>
                            <span class="hide-menu">Penjadwalan Inspeksi</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-item mb-2">
                    <a
                        class="sidebar-link justify-content-between"
                        href="{{ route("admin.laporan") }}"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-file-text"></i>
                            <span class="hide-menu">Laporan Inspeksi</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-item mb-2">
                    <a
                        class="sidebar-link justify-content-between"
                        href="{{ route("admin.riwayat") }}"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-history"></i>
                            <span class="hide-menu">Riwayat Inspeksi</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-item mb-2">
                    <a
                        class="sidebar-link justify-content-between"
                        href="{{ route("admin.petugas.index") }}"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-users"></i>
                            <span class="hide-menu">Petugas</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-item mb-2">
                    <a
                        class="sidebar-link justify-content-between"
                        href="{{ route("admin.pengaturan") }}"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-settings"></i>
                            <span class="hide-menu">Pengaturan</span>
                        </div>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Logout -->
    <div class="p-3 mt-auto">
        <form action="{{ route("logout") }}" method="POST">
            @csrf
            <button
                type="submit"
                class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2"
            >
                <i class="ti ti-logout"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
<!-- Sidebar End -->
