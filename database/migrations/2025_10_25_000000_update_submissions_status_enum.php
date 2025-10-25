<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('status', [
                'Menunggu',
                'Diproses',
                'Diterima',
                'Ditolak',
                'Selesai',
                'payment_pending',
                'proof_uploaded',
                'paid',
                'verified',
                'processing',
                'completed'
            ])->default('Menunggu')->change();
        });
    }

    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('status', [
                'Menunggu',
                'Diproses',
                'Diterima',
                'Ditolak',
                'Selesai'
            ])->default('Menunggu')->change();
        });
    }
};
