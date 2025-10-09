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
        // Modify the enum to include 'proof_uploaded' status
        DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM('pending', 'verified', 'payment_pending', 'paid', 'processing', 'completed', 'rejected', 'Diproses', 'proof_uploaded') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'proof_uploaded' from the enum
        DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM('pending', 'verified', 'payment_pending', 'paid', 'processing', 'completed', 'rejected', 'Diproses') DEFAULT 'pending'");
    }
};
