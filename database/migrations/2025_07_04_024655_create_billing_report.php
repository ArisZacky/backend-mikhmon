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
        Schema::create('billing_report', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan')->nullable();
            $table->string('qty')->nullable();
            $table->string('bill_amount')->nullable();
            $table->boolean('is_sent')->default(false);

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
        Schema::dropIfExists('billing_report');
    }
};
