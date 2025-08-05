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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('doc_id');
            $table->unsignedBigInteger('report_id');
            $table->string('original_name')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('upload_by')->nullable();
            $table->timestamps();

            $table->foreign('report_id')
                ->references('report_id')
                ->on('reports')
                ->onDelete('cascade');

            $table->foreign('upload_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
