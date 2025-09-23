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
        Schema::table('applications', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('applications', 'start_date')) {
                $table->date('start_date')->after('documents');
            }

            if (!Schema::hasColumn('applications', 'end_date')) {
                $table->date('end_date')->after('start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
