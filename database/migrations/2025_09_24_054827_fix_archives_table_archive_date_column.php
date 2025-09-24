<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Ubah kolom archive_date agar bisa nullable atau punya default
            $table->timestamp('archive_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->timestamp('archive_date')->nullable(false)->change();
        });
    }
};
