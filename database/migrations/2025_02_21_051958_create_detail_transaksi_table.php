<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->onDelete('cascade');
            $table->string('server')->nullable();
            $table->string('user')->nullable();
            $table->string('address')->nullable();
            $table->string('mac')->nullable();
            $table->string('uptime')->nullable();
            $table->string('bytes_in')->nullable();
            $table->string('bytes_out')->nullable();
            $table->string('time_left')->nullable();
            $table->string('login_by')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
