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
            $table->string('session_name')->nullable();
            $table->string('ip_mikrotik')->nullable();
            $table->string('user_mikrotik')->nullable();
            $table->string('password_mikrotik')->nullable();
            $table->string('hostpot_name')->nullable();
            $table->string('dns_name')->nullable();
            $table->string('currency')->nullable();
            $table->integer('auto_reload')->nullable();
            $table->integer('idle_timeout')->nullable();
            $table->integer('traffic_interface')->nullable();
            $table->string('live_report')->nullable();
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
