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
        Schema::create('schedule_details', function (Blueprint $table) {
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('detail_id');

            $table->foreign('schedule_id')
                ->references('schedule_id')
                ->on('schedules')
                ->onDelete('cascade');

            $table->foreign('detail_id')
                ->references('detail_id')
                ->on('detail_products')
                ->onDelete('cascade');

            $table->primary(['schedule_id', 'detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_details');
    }
};
