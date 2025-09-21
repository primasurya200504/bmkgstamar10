<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('submission_number')->unique();
            $table->string('data_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('purpose');
            $table->enum('category', ['PNBP', 'Non-PNBP'])->default('PNBP');
            $table->enum('status', ['Menunggu', 'Diproses', 'Diterima', 'Ditolak', 'Selesai'])->default('Menunggu');
            $table->text('rejection_note')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('submissions');
    }
};
