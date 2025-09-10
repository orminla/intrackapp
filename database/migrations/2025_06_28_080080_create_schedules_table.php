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
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('schedule_id');
            $table->unsignedBigInteger('inspector_id');
            $table->string('letter_number')->unique();
            $table->date('letter_date');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('product_id');
            $table->date('started_date');
            $table->enum('status', [
                'Menunggu konfirmasi',
                'Dalam proses',
                'Selesai'
            ])->default('Menunggu konfirmasi');
            $table->timestamps();

            $table->foreign('inspector_id')
                ->references('inspector_id')
                ->on('inspectors')
                ->onDelete('cascade');

            $table->foreign('partner_id')
                ->references('partner_id')
                ->on('partners')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
