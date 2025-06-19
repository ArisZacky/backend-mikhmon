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
        Schema::create('user_list', function (Blueprint $table) {
            $table->id();
            $table->string('server')->nullable();
            $table->string('user')->nullable();
            $table->string('user_password')->nullable();
            $table->string('profile')->nullable(); // Nama Paket Voucher
            
            // $table->string('mac')->nullable();
            // $table->string('uptime')->nullable();
            // $table->string('bytes_in')->nullable();
            // $table->string('bytes_out')->nullable();
            // $table->string('time_left')->nullable();
            // $table->string('login_by')->nullable();
            $table->integer('time_limit')->nullable();
            $table->integer('data_limit')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('paket_voucher_id'); 
            $table->unsignedBigInteger('user_id'); 
            $table->timestamps();

            $table->foreign('paket_voucher_id')
            ->references('id')
            ->on('paket_voucher')
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
        Schema::table('user_list', function (Blueprint $table) {
            Schema::dropIfExists('user_list');
        });
    }
};
