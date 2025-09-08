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
        Schema::create('certifications', function (Blueprint $table) {
            $table->bigIncrements('certification_id');
            $table->unsignedBigInteger('inspector_id');
            $table->unsignedBigInteger('portfolio_id');

            $table->string('name');
            $table->string('issuer');
            $table->string('original_name');
            $table->string('file_path');
            $table->date('issued_at');
            $table->date('expired_at')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('inspector_id')
                ->references('inspector_id')
                ->on('inspectors')
                ->onDelete('cascade');

            $table->foreign('portfolio_id')
                ->references('portfolio_id')
                ->on('portfolios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
