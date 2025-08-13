<?php
    use Illuminate\Support\Str;
?>

<header class="app-header bg-white shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <!-- Menu Toggle (Sidebar Toggler) -->
        <div class="d-block d-xl-none">
            <a
                class="nav-link sidebartoggler"
                id="headerCollapse"
                href="javascript:void(0)"
            >
                <i class="ti ti-menu-2 fs-5"></i>
            </a>
        </div>

        <!-- Search -->
        <div class="flex-grow-1 ms-3 rounded-pill bg-secondary-subtle px-3">
            <div class="input-group align-items-center" style="gap: 4px">
                <div
                    class="border-0 p-1"
                    style="padding-right: 0.25rem; background: none"
                >
                    <i
                        class="ti ti-search text-muted"
                        style="font-size: 16px; margin-right: -4px"
                    ></i>
                </div>
                <input
                    type="text"
                    class="form-control border-0"
                    placeholder="Tap to search"
                    style="padding-left: 0.3rem"
                />
            </div>
        </div>

        <!-- Notifikasi -->
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a
                    class="nav-link"
                    href="javascript:void(0)"
                    id="drop1"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="ti ti-bell" style="font-size: 24px"></i>
                    <div class="notification bg-primary rounded-circle"></div>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-animate-up"
                    aria-labelledby="drop1"
                >
                    <div class="message-body">
                        <a href="javascript:void(0)" class="dropdown-item">
                            Item 1
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            Item 2
                        </a>
                    </div>
                </div>
            </li>
        </ul>

        <!-- Profil - Trigger Modal -->
        <div
            class="nav-link d-flex align-items-center gap-2 me-4 profile-hover"
            style="cursor: pointer"
            data-bs-toggle="modal"
            data-bs-target="#profileModal"
        >
            <img
                src="<?php echo e($profile["photo_url"] ?? asset("inspector_assets/images/profile/user-7.jpg")); ?>"
                alt="Profile"
                width="40"
                height="40"
                class="rounded-circle object-fit-cover"
            />
            <div class="d-flex flex-column hide-menu">
                <span class="fw-semibold">
                    <?php echo e(Str::title( collect(explode(" ", $profile["name"] ?? "-"))->take(2)->implode(" "),)); ?>

                </span>
                <small class="text-muted" style="margin-top: -4px">
                    <?php echo e(Str::title($profile["role"] ?? "-")); ?>

                </small>
            </div>
        </div>
    </nav>
</header>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/inspector/layouts/header.blade.php ENDPATH**/ ?>