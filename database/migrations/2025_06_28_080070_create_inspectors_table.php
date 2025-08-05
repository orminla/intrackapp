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
        Schema::create('inspectors', function (Blueprint $table) {
            $table->bigIncrements('inspector_id');
            $table->unsignedBigInteger('users_id')->unique();
            $table->string('name');
            $table->string('nip')->unique();
            $table->string('phone_num');
            $table->unsignedBigInteger('portfolio_id');
            $table->timestamps();

            $table->foreign('portfolio_id')
                ->references('portfolio_id')
                ->on('portfolios')
                ->onDelete('cascade');

            $table->foreign('users_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspectors');
    }
};
