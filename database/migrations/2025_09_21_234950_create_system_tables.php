<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'user'])->default('user')->after('phone');
            }
        });

        // Submissions table
        if (!Schema::hasTable('submissions')) {
            Schema::create('submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('guideline_id')->constrained()->onDelete('cascade');
                $table->string('submission_number')->unique();
                $table->enum('type', ['pnbp', 'non_pnbp']);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('purpose')->nullable();
                $table->json('documents')->nullable();
                $table->enum('status', ['Menunggu', 'Diproses', 'Diterima', 'Ditolak', 'Selesai'])->default('Menunggu');
                $table->text('rejection_note')->nullable();
                $table->text('admin_notes')->nullable();
                $table->string('cover_letter_path')->nullable();
                $table->timestamps();
            });
        }

        // Payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->string('payment_proof')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_reference')->nullable();
                $table->enum('status', ['pending', 'uploaded', 'proof_uploaded', 'verified', 'rejected'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users');
                $table->string('e_billing_path')->nullable();
                $table->string('e_billing_filename')->nullable();
                $table->timestamps();
            });
        }

        // Generated documents table
        if (!Schema::hasTable('generated_documents')) {
            Schema::create('generated_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained()->onDelete('cascade');
                $table->string('document_path');
                $table->string('document_name');
                $table->string('document_type')->nullable();
                $table->string('file_name')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('mime_type')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Archives table
        if (!Schema::hasTable('archives')) {
            Schema::create('archives', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamp('archive_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('archives');
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('submissions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role']);
        });
    }
};
