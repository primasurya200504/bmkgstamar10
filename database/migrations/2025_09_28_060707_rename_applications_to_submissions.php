<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Rename columns in existing submissions table if they don't exist
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                // Add submission_number column if it doesn't exist
                if (!Schema::hasColumn('submissions', 'submission_number')) {
                    $table->string('submission_number')->nullable()->unique()->after('id');
                }

                // Remove application_number if it exists
                if (Schema::hasColumn('submissions', 'application_number')) {
                    $table->dropColumn('application_number');
                }
            });
        }

        // Update related tables foreign keys
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'application_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['application_id']);
                $table->renameColumn('application_id', 'submission_id');
                $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('generated_documents') && Schema::hasColumn('generated_documents', 'application_id')) {
            Schema::table('generated_documents', function (Blueprint $table) {
                $table->dropForeign(['application_id']);
                $table->renameColumn('application_id', 'submission_id');
                $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('application_histories')) {
            Schema::rename('application_histories', 'submission_histories');

            Schema::table('submission_histories', function (Blueprint $table) {
                if (Schema::hasColumn('submission_histories', 'application_id')) {
                    $table->dropForeign(['application_id']);
                    $table->renameColumn('application_id', 'submission_id');
                    $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
                }
            });
        }

        // Update any existing application_number to submission_number
        if (Schema::hasTable('submissions')) {
            DB::table('submissions')
                ->whereNull('submission_number')
                ->update([
                    'submission_number' => DB::raw("CONCAT('BMKG-SUR-', DATE_FORMAT(created_at, '%d%m'), '-', YEAR(created_at), '-', LPAD(id, 4, '0'))")
                ]);
        }
    }

    public function down()
    {
        // Reverse the changes
        if (Schema::hasTable('submission_histories')) {
            Schema::rename('submission_histories', 'application_histories');

            Schema::table('application_histories', function (Blueprint $table) {
                if (Schema::hasColumn('application_histories', 'submission_id')) {
                    $table->dropForeign(['submission_id']);
                    $table->renameColumn('submission_id', 'application_id');
                    $table->foreign('application_id')->references('id')->on('submissions')->onDelete('cascade');
                }
            });
        }

        if (Schema::hasTable('generated_documents') && Schema::hasColumn('generated_documents', 'submission_id')) {
            Schema::table('generated_documents', function (Blueprint $table) {
                $table->dropForeign(['submission_id']);
                $table->renameColumn('submission_id', 'application_id');
                $table->foreign('application_id')->references('id')->on('submissions')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'submission_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['submission_id']);
                $table->renameColumn('submission_id', 'application_id');
                $table->foreign('application_id')->references('id')->on('submissions')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                if (Schema::hasColumn('submissions', 'submission_number')) {
                    $table->dropColumn('submission_number');
                }
                if (!Schema::hasColumn('submissions', 'application_number')) {
                    $table->string('application_number')->nullable()->unique()->after('id');
                }
            });
        }
    }
};
