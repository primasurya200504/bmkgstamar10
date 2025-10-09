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
        Schema::table('archives', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['application_id']);

            // Rename application_id to submission_id
            $table->renameColumn('application_id', 'submission_id');

            // Add user_id column
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // Add new foreign key constraint for submission_id
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Drop the new foreign key constraints
            $table->dropForeign(['submission_id']);
            $table->dropForeign(['user_id']);

            // Drop user_id column
            $table->dropColumn('user_id');

            // Rename submission_id back to application_id
            $table->renameColumn('submission_id', 'application_id');

            // Add back the old foreign key constraint
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });
    }
};
