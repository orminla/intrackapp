<?php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route;

    $currentRoute = Route::currentRouteName();
    $searchAction = "#"; // default jika tidak ada search di halaman ini

    // Mapping halaman yang mendukung search
    $searchRoutes = [
        "admin.dashboard" => route("admin.dashboard"),
        "admin.jadwal.index" => route("admin.jadwal.index"),
        "admin.laporan" => route("admin.laporan"),
        "admin.riwayat" => route("admin.riwayat"),
        "admin.petugas.index" => route("admin.petugas.index"),
        "admin.pengaturan" => route("admin.pengaturan"),
    ];

    if (isset($searchRoutes[$currentRoute])) {
        $searchAction = $searchRoutes[$currentRoute];
    }
?>

<header class="app-header bg-white shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <!-- Menu Toggle -->
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
        <?php if($searchAction !== "#"): ?>
            <form
                action="<?php echo e($searchAction); ?>"
                method="GET"
                class="flex-grow-1 ms-3 rounded-pill bg-secondary-subtle px-3"
            >
                <div class="input-group align-items-center" style="gap: 4px">
                    <!-- Icon search -->
                    <div class="border-0 p-1" style="background: none">
                        <i
                            class="ti ti-search text-muted"
                            style="font-size: 16px"
                        ></i>
                    </div>

                    <!-- Input -->
                    <input
                        type="text"
                        name="search"
                        class="form-control border-0"
                        placeholder="<?php echo e($currentRoute === "admin.dashboard" ? "Pencarian dinonaktifkan di dashboard" : "Ketik & tekan Enter untuk mencari"); ?>"
                        style="padding-left: 0.3rem"
                        value="<?php echo e(request("search")); ?>"
                        <?php echo e($currentRoute === "admin.dashboard" ? "readonly" : ""); ?>

                    />

                    <!-- Tombol clear -->
                    <button
                        type="button"
                        id="clearSearch"
                        class="border-0 bg-transparent p-0 m-0"
                        style="
                            display: <?php echo e(request("search") ? "inline" : "none"); ?>;
                            font-size: 22px;
                            line-height: 1;
                            cursor: pointer;
                        "
                    >
                        &times;
                    </button>
                </div>
            </form>
        <?php endif; ?>

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

        <!-- Profil -->
        <div
            class="nav-link d-flex align-items-center gap-2 me-4 profile-hover"
            style="cursor: pointer"
            data-bs-toggle="modal"
            data-bs-target="#profileModal"
        >
            <img
                src="<?php echo e($profile["photo_url"] ?? asset("admin_assets/images/profile/user-7.jpg")); ?>"
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let typingTimer;
        const doneTypingInterval = 400;
        const searchInput = document.querySelector('input[name="search"]');
        const form = searchInput?.closest('form');
        const clearBtn = document.getElementById('clearSearch');

        if (
            searchInput &&
            form &&
            '<?php echo e($currentRoute); ?>' !== 'admin.dashboard'
        ) {
            // Auto Search (AJAX)
            searchInput.addEventListener('keyup', function (event) {
                clearTimeout(typingTimer);
                clearBtn.style.display = searchInput.value
                    ? 'inline-flex'
                    : 'none';

                // Jika tekan Enter → submit form
                if (event.key === 'Enter') {
                    event.preventDefault();
                    form.submit();
                    return;
                }

                typingTimer = setTimeout(() => {
                    const query = searchInput.value;
                    let url =
                        form.getAttribute('action') || window.location.href;
                    url +=
                        (url.indexOf('?') === -1 ? '?' : '&') +
                        'search=' +
                        encodeURIComponent(query);

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    })
                        .then((res) => res.text())
                        .then((html) => {
                            const container =
                                document.querySelector('#dataContainer');
                            if (container) container.innerHTML = html;
                        })
                        .catch((err) => console.error(err));
                }, doneTypingInterval);
            });

            // Tombol X untuk clear & reload default data
            clearBtn?.addEventListener('click', function () {
                searchInput.value = '';
                clearBtn.style.display = 'none';

                const baseUrl = form.getAttribute('action');

                // Hapus query search dari URL
                window.history.replaceState({}, document.title, baseUrl);

                // Reload default data via AJAX
                fetch(baseUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                    .then((res) => res.text())
                    .then((html) => {
                        const container =
                            document.querySelector('#dataContainer');
                        if (container) container.innerHTML = html;

                        location.reload();
                    })
                    .catch((err) => {
                        console.error(err);
                        location.reload();
                    });
            });
        }
    });
</script>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/admin/layouts/header.blade.php ENDPATH**/ ?>