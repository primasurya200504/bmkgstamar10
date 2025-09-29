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

        // Applications table
        if (!Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('guideline_id')->constrained()->onDelete('cascade');
                $table->string('application_number')->unique();
                $table->enum('type', ['pnbp', 'non_pnbp']);
                $table->json('documents')->nullable();
                $table->enum('status', ['pending', 'verified', 'payment_pending', 'paid', 'processing', 'completed', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->string('payment_proof')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_reference')->nullable();
                $table->enum('status', ['pending', 'uploaded', 'verified', 'rejected'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users');
                $table->timestamps();
            });
        }

        // Generated documents table
        if (!Schema::hasTable('generated_documents')) {
            Schema::create('generated_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->onDelete('cascade');
                $table->string('document_path');
                $table->string('document_name');
                $table->timestamps();
            });
        }

        // Archives table
        if (!Schema::hasTable('archives')) {
            Schema::create('archives', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->onDelete('cascade');
                $table->date('archive_date');
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
        Schema::dropIfExists('applications');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role']);
        });
    }
};
