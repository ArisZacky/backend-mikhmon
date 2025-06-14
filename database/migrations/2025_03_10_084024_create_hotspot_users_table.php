<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('hotspot_users', function (Blueprint $table) {
        $table->id();
        $table->string('server');
        $table->string('user');
        $table->string('address');
        $table->string('mac');
        $table->string('uptime');
        $table->string('bytes_in');
        $table->string('bytes_out');
        $table->string('time_left')->nullable();
        $table->string('login_by');
        $table->text('comment')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_users');
    }
};
