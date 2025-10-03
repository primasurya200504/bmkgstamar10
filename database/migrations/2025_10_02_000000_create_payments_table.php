<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');        // user yg akan membayar
            $table->string('ebilling_file')->nullable();  // dokumen e-billing dari admin
            $table->string('proof_file')->nullable();     // bukti pembayaran user
            $table->enum('status', ['pending', 'uploaded', 'verified'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
