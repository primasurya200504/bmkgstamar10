<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guidelines', function (Blueprint $table) {
            if (!Schema::hasColumn('guidelines', 'type')) {
                $table->enum('type', ['pnbp', 'non_pnbp'])->after('description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('guidelines', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
