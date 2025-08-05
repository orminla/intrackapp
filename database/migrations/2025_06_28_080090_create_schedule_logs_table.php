<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->unsignedBigInteger('schedule_id');
            $table->enum('status', [
                'Menunggu konfirmasi',
                'Dijadwalkan ganti',
                'Disetujui',
                'Ditolak',
                'Dalam proses',
                'Selesai'
            ]);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); // tambahkan ini
            $table->timestamps(); // otomatis created_at & updated_at

            $table->foreign('schedule_id')
                ->references('schedule_id')
                ->on('schedules')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // opsional: bisa juga cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_logs');
    }
};
