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
        Schema::create('router', function (Blueprint $table) {
            $table->id();
            $table->string('session_name');
            $table->string('ip_mikrotik');
            $table->string('user_mikrotik');
            $table->string('password_mikrotik');
            $table->string('hostpot_name');
            $table->string('dns_name');
            $table->string('currency');
            $table->integer('auto_reload');
            $table->integer('idle_timeout');
            $table->integer('traffic_interface');
            $table->string('live_report');
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->timestamps();

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
        Schema::dropIfExists('router');
    }
};
