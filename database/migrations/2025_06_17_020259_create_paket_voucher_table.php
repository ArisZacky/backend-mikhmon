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
        Schema::create('paket_voucher', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_name')->nullable();
            $table->string('address_pool')->nullable();
            $table->integer('shared_user')->nullable();
            $table->string('rate_limit')->nullable();
            $table->string('expired_mode')->nullable();
            $table->integer('price')->nullable();
            $table->integer('selling_price')->nullable();
            $table->string('lock_user')->nullable();
            $table->string('parent_queue')->nullable();
            $table->unsignedBigInteger('router_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('router_id')
            ->references('id')
            ->on('router')
            ->onDelete('cascade');

            $table->foreign('user_id')
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
        Schema::table('paket_voucher', function (Blueprint $table) {
            Schema::dropIfExists('paket_voucher');
        });
    }
};
