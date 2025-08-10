<!DOCTYPE html>
<html>
    <head>
        <title>Test Session</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <?php if(session("success")): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '<?php echo e(session("success")); ?>',
                    timer: 3000,
                    showConfirmButton: false,
                });
            </script>
        <?php endif; ?>

        <h1>Halaman Test Session</h1>
        <a href="/test-session">Klik untuk test redirect + flash session</a>
    </body>
</html>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/test-session.blade.php ENDPATH**/ ?>