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
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('report_id');
            $table->unsignedBigInteger('schedule_id');
            $table->date('finished_date');

            $table->enum('status', [
                'Disetujui',
                'Ditolak',
                'Menunggu Konfirmasi',
            ])->default('Menunggu Konfirmasi');

            $table->text('rejection_reason')->nullable(); // ← Tambahkan ini

            $table->timestamps();

            $table->foreign('schedule_id')
                ->references('schedule_id')
                ->on('schedules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
