
<?php
    $role = request()->segment(2);
    $assetPath = "login_assets";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Sign In - InTrack App</title>

        <link
            rel="shortcut icon"
            href="<?php echo e(asset("$assetPath/images/logos/favicon.png")); ?>"
        />
        <link
            rel="stylesheet"
            href="<?php echo e(asset("$assetPath/css/styles.min.css")); ?>"
        />

        <style>
            html,
            body {
                height: 100%;
                overflow: hidden;
            }
            .form-control {
                background-color: #f1f1f1;
                border-radius: 0.5rem;
                transition: background-color 0.3s ease;
            }
            .form-control:focus {
                background-color: #e6e6e6;
            }
            .btn-primary {
                transition: transform 0.3s ease;
            }
            .btn-primary:hover {
                transform: scale(1.03);
            }
            .login-animation {
                animation: fadeInUp 1s ease forwards;
                opacity: 0;
                transform: translateY(15px);
            }
            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body>
        <div
            class="page-wrapper"
            id="main-wrapper"
            data-layout="vertical"
            data-navbarbg="skin6"
            data-sidebartype="full"
            data-sidebar-position="fixed"
            data-header-position="fixed"
        >
            <div
                class="d-flex min-vh-100 align-items-center justify-content-center login-animation"
            >
                <div
                    class="row rounded-4 overflow-hidden w-100 shadow"
                    style="max-width: 1200px"
                >
                    <!-- Gambar -->
                    <div class="col-md-6 d-none d-md-block bg-white p-3">
                        <img
                            src="<?php echo e(asset("$assetPath/images/sign_in.jpg")); ?>"
                            alt="Welcome Image"
                            class="img-fluid h-100 w-100 rounded-4"
                            style="object-fit: cover"
                        />
                    </div>

                    <!-- Form -->
                    <div
                        class="col-md-6 bg-white p-5 d-flex flex-column justify-content-center"
                    >
                        <div class="text-center mb-4 mt-4">
                            <img
                                src="<?php echo e(asset("$assetPath/images/logo_intrackapp.png")); ?>"
                                alt="InTrack App"
                                style="max-width: 70%"
                            />
                        </div>

                        
                        

                        <form method="POST" action="<?php echo e(route("login.post")); ?>">
                            <?php echo csrf_field(); ?>
                            <input
                                type="hidden"
                                name="role"
                                value="<?php echo e($role); ?>"
                            />

                            <div class="mb-3 mt-3">
                                <label for="email" class="form-label">
                                    Email/Username
                                </label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="Enter email or username"
                                    required
                                />
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    Password
                                </label>
                                <div class="position-relative">
                                    <input
                                        type="password"
                                        class="form-control pe-5"
                                        id="password"
                                        name="password"
                                        placeholder="Enter password"
                                        required
                                    />
                                    <iconify-icon
                                        icon="mdi:eye-off"
                                        id="togglePassword"
                                        class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer d-none"
                                        style="font-size: 1.25rem"
                                    ></iconify-icon>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-4">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="remember"
                                        name="remember"
                                        <?php echo e(old("remember") ? "checked" : ""); ?>

                                    />
                                    <label
                                        class="form-check-label"
                                        for="remember"
                                    >
                                        Remember me
                                    </label>
                                </div>
                                <a
                                    href="#"
                                    class="text-decoration-underline"
                                    data-bs-toggle="modal"
                                    data-bs-target="#forgotPasswordModal"
                                >
                                    Forgot Password?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <?php echo $__env->make("auth.forget_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make("auth.reset_modal", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <script src="<?php echo e(asset("$assetPath/libs/jquery/dist/jquery.min.js")); ?>"></script>
        <script src="<?php echo e(asset("$assetPath/libs/bootstrap/dist/js/bootstrap.bundle.min.js")); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

        <script>
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');

            // Toggle show/hide password
            toggleIcon.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                this.setAttribute('icon', isHidden ? 'mdi:eye' : 'mdi:eye-off');
            });

            // Show icon only when typing
            passwordInput.addEventListener('input', function () {
                if (this.value.length > 0) {
                    toggleIcon.classList.remove('d-none');
                } else {
                    toggleIcon.classList.add('d-none');
                }
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('togglePassword');

                toggleIcon.addEventListener('click', function () {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    this.setAttribute('icon', isHidden ? 'mdi:eye' : 'mdi:eye-off');
                });

                passwordInput.addEventListener('input', function () {
                    if (this.value.length > 0) {
                        toggleIcon.classList.remove('d-none');
                    } else {
                        toggleIcon.classList.add('d-none');
                    }
                });

                <?php if(session('verifikasi_status') && session('verifikasi_message')): ?>
                    Swal.fire({
                        icon: "<?php echo e(session('verifikasi_status')); ?>",
                        title: "<?php echo e(session('verifikasi_status') === 'success' ? 'Berhasil!' : 'Gagal!'); ?>",
                        text: "<?php echo e(session('verifikasi_message')); ?>",
                        confirmButtonText: 'OK',
                    });
                <?php endif; ?>
        </script>
    </body>
</html>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/auth/login.blade.php ENDPATH**/ ?>