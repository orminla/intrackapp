<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <title>Akun Berhasil Dibuat</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f7f7f7;
                color: #333;
                padding: 20px;
            }
            .container {
                background-color: #fff;
                border-radius: 8px;
                padding: 20px;
                max-width: 600px;
                margin: auto;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #4caf50;
            }
            a.verif-link {
                color: #4caf50;
                text-decoration: underline;
                word-break: break-all;
            }
            ul {
                padding-left: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Halo, <?php echo e($pendingUser->name); ?>!</h2>
            <p>Akun Anda berhasil dibuat dengan detail berikut:</p>
            <ul>
                <li>Email: <?php echo e($pendingUser->email); ?></li>
                <li>Password default: <?php echo e($pendingUser->password_plain); ?></li>
                <li>Role: <?php echo e(ucfirst($pendingUser->role)); ?></li>
            </ul>
            <p>Silakan verifikasi akun Anda dengan mengklik link berikut:</p>
            <p>
                <a href="<?php echo e($verifLink); ?>" class="verif-link">
                    <?php echo e($verifLink); ?>

                </a>
            </p>
            <p>Link ini akan kadaluarsa dalam 2 hari.</p>
            <p>
                Terima kasih!
                <br />
                <?php echo e(config("app.name")); ?>

            </p>
        </div>
    </body>
</html>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/emails/account_created.blade.php ENDPATH**/ ?>