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
            $table->string('voucher_name');
            $table->string('address_pool')->nullable();
            $table->integer('shared_user');
            $table->string('rate_limit');
            $table->string('expired_mode');
            $table->integer('price');
            $table->integer('selling_price');
            $table->string('lock_user');
            $table->string('parent_queue')->nullable();
            $table->unsignedBigInteger('router_id'); 
            $table->unsignedBigInteger('user_id'); 
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
