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
        Schema::create('pending_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('gender');
            $table->string('email')->unique();
            $table->string('phone_num');
            $table->enum('role', ['admin', 'inspector'])->default('inspector');
            $table->string('nip')->nullable()->unique();
            $table->unsignedBigInteger('portfolio_id')->nullable();
            $table->string('password_plain');
            $table->string('verif_token')->unique();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('portfolio_id')
                ->references('portfolio_id')
                ->on('portfolios')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_users');
    }
};
