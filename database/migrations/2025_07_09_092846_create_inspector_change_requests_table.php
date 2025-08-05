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
        Schema::create('inspector_change_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->text('reason');
            $table->unsignedBigInteger('old_inspector_id');
            $table->unsignedBigInteger('new_inspector_id')->nullable();
            $table->date('requested_date');
            $table->enum('status', ['Menunggu Konfirmasi', 'Disetujui', 'Ditolak'])->default('Menunggu Konfirmasi');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('schedule_id')->references('schedule_id')->on('schedules')->onDelete('cascade');
            $table->foreign('old_inspector_id')->references('inspector_id')->on('inspectors')->onDelete('cascade');
            $table->foreign('new_inspector_id')->references('inspector_id')->on('inspectors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspector_change_requests');
    }
};
