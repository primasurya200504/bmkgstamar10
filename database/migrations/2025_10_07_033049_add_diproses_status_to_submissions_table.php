<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'Diproses' status
        DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM('pending', 'verified', 'payment_pending', 'paid', 'processing', 'completed', 'rejected', 'Diproses') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'Diproses' from the enum
        DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM('pending', 'verified', 'payment_pending', 'paid', 'processing', 'completed', 'rejected') DEFAULT 'pending'");
    }
};
