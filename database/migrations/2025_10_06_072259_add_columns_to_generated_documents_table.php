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
        Schema::table('generated_documents', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('generated_documents', 'submission_id')) {
                $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            }
            if (!Schema::hasColumn('generated_documents', 'document_type')) {
                $table->string('document_type')->nullable()->after('document_name');
            }
            if (!Schema::hasColumn('generated_documents', 'file_name')) {
                $table->string('file_name')->nullable()->after('document_type');
            }
            if (!Schema::hasColumn('generated_documents', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('generated_documents', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('generated_documents', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null')->after('mime_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_documents', function (Blueprint $table) {
            if (Schema::hasColumn('generated_documents', 'uploaded_by')) {
                $table->dropForeign(['uploaded_by']);
                $table->dropColumn('uploaded_by');
            }
            if (Schema::hasColumn('generated_documents', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
            if (Schema::hasColumn('generated_documents', 'file_size')) {
                $table->dropColumn('file_size');
            }
            if (Schema::hasColumn('generated_documents', 'file_name')) {
                $table->dropColumn('file_name');
            }
            if (Schema::hasColumn('generated_documents', 'document_type')) {
                $table->dropColumn('document_type');
            }
            if (Schema::hasColumn('generated_documents', 'submission_id')) {
                $table->dropForeign(['submission_id']);
                $table->dropColumn('submission_id');
            }
        });
    }
};
