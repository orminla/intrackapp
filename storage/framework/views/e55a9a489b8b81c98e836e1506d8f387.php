<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Bukti Laporan Inspeksi</title>
        <style>
            body {
                font-family:
                    DejaVu Sans,
                    sans-serif;
                font-size: 13px;
                margin: 0;
                padding: 30px;
                background-color: #fff;
                color: #000;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            td {
                border: 1px solid #ccc;
                padding: 6px 8px;
                vertical-align: top;
            }
            .table-title {
                background-color: #004a91;
                color: #fff;
                font-weight: bold;
                text-transform: uppercase;
                text-align: left;
            }
            ul {
                margin: 5px 0;
                padding-left: 20px;
            }
            .footer {
                font-size: 11px;
                text-align: center;
                margin-top: 20px;
                color: #555;
            }
        </style>
    </head>
    <body>
        
        <h2
            style="
                text-align: center;
                font-size: 24px;
                margin-bottom: 20px;
                color: #004a91;
            "
        >
            INSPECTION REPORT VALIDATION
        </h2>

        
        <table>
            <tr>
                <td class="table-title" colspan="2">Informasi Inspeksi</td>
            </tr>
            <tr>
                <td>Mitra</td>
                <td><?php echo e($partner->name); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><?php echo e($partner->address); ?></td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td><?php echo e($inspector->name); ?></td>
            </tr>
            <tr>
                <td>Bidang</td>
                <td><?php echo e($department ?? "-"); ?></td>
            </tr>
            <tr>
                <td>Portofolio</td>
                <td><?php echo e($portofolio ?? "-"); ?></td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td>
                    <?php echo e(\Carbon\Carbon::parse($started_date)->translatedFormat("l, d-m-Y")); ?>

                </td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td>
                    <?php echo e($finished_date ? \Carbon\Carbon::parse($finished_date)->translatedFormat("l, d-m-Y") : "-"); ?>

                </td>
            </tr>

            <tr>
                <td>Produk</td>
                <td><?php echo e($product->name); ?></td>
            </tr>
        </table>

        
        <table>
            <tr>
                <td class="table-title">Detail Produk</td>
            </tr>
            <tr>
                <td>
                    <ul>
                        <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($detail->name); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </td>
            </tr>
        </table>

        
        <?php if($documents->count() > 0): ?>
            <table>
                <tr>
                    <td class="table-title">Lampiran Dokumen</td>
                </tr>
                <tr>
                    <td>
                        <ul>
                            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($doc->original_name); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </td>
                </tr>
            </table>
        <?php endif; ?>

        
        <div
            style="
                margin-top: 15px;
                padding: 10px;
                background: #e8f5e9;
                font-size: 12px;
                color: #2e7d32;
            "
        >
            Bukti ini telah terverifikasi oleh Admin PT SUCOFINDO Cabang
            Pontianak dan dinyatakan sah.
        </div>

        
        <div class="footer">
            Dicetak pada tanggal
            <?php echo e(\Carbon\Carbon::now()->format("d-m-Y H:i")); ?> | InTrack App by
            Nirmala &copy; <?php echo e(date("Y")); ?> All rights reserved.
        </div>
    </body>
</html>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/report_pdf.blade.php ENDPATH**/ ?>